<?php
/**
 * =========================================================
 *                        DROPCART
 *                      ------------
 * This file is part of the source code of Dropcart and is
 * subject to the terms and conditions defined in the license
 * file include in this package.
 *
 * CONFIDENTIAL:
 * All information contained herein is, and remains the property
 * of Dropcart and its suppliers, if any.  The intellectual and
 * technical concepts contained herein are proprietary to Dropcart
 * and its suppliers and may be covered by Dutch and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Dropcart.
 *
 * =========================================================
 *
 * File: DropcartClient.php
 * Date: 09-01-18 17:09
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */

namespace Dropcart\PhpClient;

use Dropcart\PhpClient\Helpers\Caller;
use Dropcart\PhpClient\Helpers\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

/**
 * Class DropcartClient
 * @package Dropcart\PhpClient
 *
 * @method static \Dropcart\PhpClient\Services\Catalog catalog(...$args)
 * @method static \Dropcart\PhpClient\Services\Order order(...$args)
 * @method static \Dropcart\PhpClient\Services\Management management(...$args)
 * @method static \Dropcart\PhpClient\Services\Me me(...$args)
 *
 * @method static DropcartClient setPublicKey(string $public_key) Sets the overall public key
 * @method static DropcartClient setPrivateKey(string $private_key) Sets the overall private key
 */
class DropcartClient
{
    private static DropcartClient $instance;
    private static DropcartClientOptions $options;
    private static Client $http;
    private static Caller $current_call_stack;
    private static array $cache_call_stack = [];

    public const CALL_ENDPOINTS = [
        'get',
        'post',
        'put',
        'patch',
        'delete',
    ];

    private function __construct()
    {
        self::$options = new DropcartClientOptions();
        self::$http = new Client(
            [
                'base_uri' => static::options()->getBaseUri(),
                'timeout' => static::options()->getTimeout(),
            ]
        );
    }

    public static function __callStatic($name, $arguments)
    {
        $firstThreeLetters = substr($name, 0, 3);
        if ($firstThreeLetters == 'set') {
            $var = Str::toSnakeCase(substr($name, 3));
            return self::options()->set($var, $arguments[0]);
        } else {
            if ($firstThreeLetters == 'get') {
                $var = Str::toSnakeCase(substr($name, 3));
                return self::options()->get($var, $arguments[0] ?: null);
            } else {
                self::$current_call_stack = new Caller($name, $arguments);
                return self::getInstance();
            }
        }
    }

    public function __call($name, $arguments)
    {
        call_user_func_array([self::$current_call_stack, $name], $arguments);

        if (in_array($name, self::CALL_ENDPOINTS)) {
            // Do the request.
            return $this->request();
        } else {
            if ($name == 'addParams') {
                return self::$current_call_stack->addParams($arguments);
            } elseif ($name == 'addParam') {
                if (!isset($arguments[0]) || !isset($arguments[1])) {
                    throw new \InvalidArgumentException("Need a name and a value");
                }

                return self::$current_call_stack->addParam($arguments[0], $arguments[1]);
            } elseif ($name == 'getParams') {
                return self::$current_call_stack->getParams();
            } elseif ($name == 'hasParams') {
                return self::$current_call_stack->hasParams();
            } elseif ($name == 'getUrl') {
                return self::$current_call_stack->getUrl(self::options()->getBaseUri());
            } else {
                return self::getInstance();
            }
        }
    }

    public static function getInstance(): DropcartClient
    {
        return self::$instance
            ?? self::$instance = new DropcartClient();
    }

    public static function options(): DropcartClientOptions
    {
        return self::$options
            ?? self::$options = new DropcartClientOptions();
    }

    /**
     * @return Promise||ResponseInterface
     * @throws \Dropcart\PhpClient\DropcartClientException
     * @throws \Exception
     * @throws GuzzleException
     */
    private function request(): Promise
    {
        static::$http = static::$http
            ?? static::$http = new Client(self::options()->getAll());

        $request = self::$current_call_stack;

        $hash = $request->getHash();
        $http =& static::$http;

        $base_url = static::options()->getBaseUri();
        $url = $request->getUrl($base_url, ($request->hasQuery() && $request->getHttpMethod() == 'GET'));

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . static::getToken()->toString(),
            ],
        ];

        if (strtolower(substr($base_url, 0, 5)) == 'https') {
            $options[RequestOptions::VERIFY] = static::options()->get('verify', static::options()->get('cert'));
        }

        if ($request->hasParams() && $request->getHttpMethod() == 'GET') {
            throw new DropcartClientException("Malformed request. Cant't have a body on a GET request.");
        } else {
            if ($request->hasParams() && $request->hasFiles()) {
                $options['multipart'] = [];
                foreach ($request->getParams() as $name => $contents) {
                    $options['multipart'][] = [
                        'name' => $name,
                        'contents' => $contents,
                    ];
                }
                // TODO: implement files when necessary
            } else {
                if ($request->hasParams()) {
                    $options['form_params'] = $request->getParams();
                }
            }
        }

        // Synchonous request
        // TODO: Implement Async native
        try {
            $result = $http->request(
                $request->getHttpMethod(),
                $url,
                $options
            );
        } catch (ClientException $e) {
            $result = $e->getResponse();
        }

        static::setResult($hash, $result);
        return $result;
    }

    /**
     * Remove an item from the cache
     */
    private static function removeCache($hash): DropcartClient
    {
        if (isset(static::$cache_call_stack[$hash])) {
            unset(static::$cache_call_stack[$hash]);
        }

        return static::getInstance();
    }


    /**
     * @return Token
     * @throws DropcartClientException
     */
    private static function getToken(): Token
    {
        if (is_null(static::options()->get('public_key', null)) ||
            is_null(static::options()->get('private_key', null))) {
            throw new DropcartClientException("Public and/or private key are not set.");
        }

        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = gethostname();
        }

        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = '/';
        }

        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            Key\InMemory::plainText(
                static::options()
                    ->getPrivateKey()
            )
        );

        return $configuration->builder()
            ->issuedBy(static::options()->getPublicKey())
            ->permittedFor(static::options()->getUrl($url))
            ->expiresAt(new \DateTimeImmutable("+60 seconds"))
            ->issuedAt(new \DateTimeImmutable("-30 seconds"))
            ->getToken($configuration->signer(), $configuration->signingKey());
    }

    /**
     * Set the result of an request
     */
    public static function setResult($hash, $result): DropcartClient
    {
        if (isset(static::$cache_call_stack[$hash])) {
            static::$cache_call_stack[$hash]['result'] = $result;
        } else {
            static::$cache_call_stack[$hash] = [
                'result' => $result,
            ];
        }

        return static::getInstance();
    }
}
