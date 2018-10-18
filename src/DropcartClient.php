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


use Dropcart\PhpClient\DropcartClientException;
use Dropcart\PhpClient\Helpers\Caller;
use Dropcart\PhpClient\Helpers\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Http\Message\ResponseInterface;


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
class DropcartClient {

	/**
	 * @var DropcartClient
	 */
	private static $instance;

	/**
	 * @var DropcartClientOptions
	 */
	private static $options;

	/**
	 * @var Client
	 */
	private static $http;

	/**
	 * @var Caller
	 */
	private static $current_call_stack;

	/**
	 * @var array
	 */
	private static $cache_call_stack = [];


	const CALL_ENDPOINTS = ['get', 'post', 'put', 'patch', 'delete'];


	private function __construct() {
		self::$options  = new DropcartClientOptions();
		self::$http     = new Client([
			'base_uri'  => static::options()->getBaseUri(),
			'timeout'   => static::options()->getTimeout()
		]);
	}

	public static function __callStatic( $name, $arguments )
	{
		$firstThreeLetters = substr($name, 0,3);
		if($firstThreeLetters == 'set')
		{
			$var = Str::toSnakeCase(substr($name, 3));
			return self::options()->set($var, $arguments[0]);
		} else if($firstThreeLetters == 'get')
		{
			$var = Str::toSnakeCase(substr($name, 3));
			return self::options()->get($var, $arguments[0] ?: null);
		}
		else {
			self::$current_call_stack = new Caller($name, $arguments);
			return self::getInstance();
		}
	}

	public function __call( $name, $arguments )
	{
		call_user_func_array([self::$current_call_stack, $name], $arguments);

		if(in_array($name, self::CALL_ENDPOINTS))
		{
			// Do the request.
			return $this->request();
		}
		else if($name == 'addParams')
		{
			return self::$current_call_stack->addParams($arguments);
		}
		elseif($name == 'addParam')
		{
			if(!isset($arguments[0]) || !isset($arguments[1]))
				throw new \InvalidArgumentException("Need a name and a value");

			return self::$current_call_stack->addParam($arguments[0], $arguments[1]);
		}
        elseif($name == 'getParams') {
            return self::$current_call_stack->getParams();
        }
        elseif($name == 'hasParams')
        {
            return self::$current_call_stack->hasParams();
        }
		elseif($name == 'getUrl')
		{
			return self::$current_call_stack->getUrl(self::options()->getBaseUri());
		}
		else {
			return self::getInstance();
		}
	}

	/**
	 * @return DropcartClient
	 */
	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new DropcartClient();

		return self::$instance;
	}

	/**
	 * @return DropcartClientOptions
	 */
	public static function options()
	{
		if(!static::$options)
			static::$options = new DropcartClientOptions();

		return static::$options;
	}

	/**
	 * @return Promise||ResponseInterface
	 * @throws \Dropcart\PhpClient\DropcartClientException
	 * @throws \Exception
	 */
	private function request()
	{
		if(!static::$http)
			static::$http = new Client(self::options()->getAll());


		$request    = self::$current_call_stack;

//		$cache      = self::options()->getCache(true);
		$hash       = $request->getHash();
		$http       =& static::$http;

		// TODO: Add caching

		// Add to the cache stack
//		static::$cache_call_stack[$hash] = [
//			'call'      => $request,
//			'requested' => time(),
//			'options'   => static::options()->getAll()
//		];

		$base_url   = static::options()->getBaseUri();
		$url        = $request->getUrl($base_url, ($request->hasQuery() && $request->getHttpMethod() == 'GET'));

		$options = [
			'headers' => [
				'Authorization' => 'Bearer ' . static::getToken()
			]
		];


		if(strtolower(substr($base_url, 0, 5)) == 'https')
		{
			$options[RequestOptions::VERIFY] = static::options()->get('verify', static::options()->get('cert'));
			//$options[RequestOptions::CERT]   = static::options()->get('cert');
		}

		if($request->hasParams() && $request->getHttpMethod() == 'GET')
		{
			throw new DropcartClientException("Malformed request. Cant't have a body on a GET request.");
		}
		else if ($request->hasParams() && $request->hasFiles())
		{
			$options['multipart'] = [];
			foreach($request->getParams() as $name => $contents)
			{
				$options['multipart'][] = [
					'name'      => $name,
					'contents'  => $contents
				];
			}

			// TODO: implement files when necessary

		} else if($request->hasParams())
		{
			$options['form_params'] = $request->getParams();
		}

		// ACTUAL REQUEST

		// Synchonous request
		// TODO: Implement Async native
		try {
			$result = $http->request(
				$request->getHttpMethod(),
				$url,
				$options
			);

		} catch(ClientException $e) {
			$result = $e->getResponse();
		} catch (\Exception $e)
		{
			throw $e;
		}

		static::setResult($hash, $result);
		return $result;
	}

	/**
	 * Remove an item from the cache
	 *
	 * @param $hash
	 *
	 * @return DropcartClient
	 */
	private static function removeCache($hash)
	{
		if(isset(static::$cache_call_stack[$hash]))
			unset(static::$cache_call_stack[$hash]);

		return static::getInstance();
	}


	/**
	 * @return \Lcobucci\JWT\Token
	 * @throws DropcartClientException
	 */
	private static function getToken()
	{
		if(is_null(static::options()->get('public_key', null)) ||
		   is_null(static::options()->get('private_key', null)))
	    {
		    throw new DropcartClientException("Public and/or private key are not set.");
	    }

	    if(!isset($_SERVER['HTTP_HOST']))
	    	$_SERVER['HTTP_HOST'] = gethostname();

		if(!isset($_SERVER['REQUEST_URI']))
			$_SERVER['REQUEST_URI'] = '/';

	    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		return  (new Builder())->setIssuer(static::options()->getPublicKey())
								->setAudience(static::options()->getUrl($url))
								->setExpiration(time() + 60) // Max time is 1,5 minutes (see line below)
								->setIssuedAt(time() - 30) // Set issues at time() - 30 sec for minor server time out of synch correction
								->sign((new Sha256()), static::options()->getPrivateKey())
								->getToken();
	}

	/**
	 * Set the result of an request
	 *
	 * @param $hash
	 * @param $result
	 *
	 * @return DropcartClient
	 */
	public static function setResult($hash, $result)
	{
		if(isset(static::$cache_call_stack[$hash]))
			static::$cache_call_stack[$hash]['result'] = $result;
		else
			static::$cache_call_stack[$hash] = [
				'result'    => $result
			];

		return static::getInstance();
	}



}