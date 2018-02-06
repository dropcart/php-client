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
 * File: DropcartClientOptions.php
 * Date: 16-01-18 11:12
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient;


use Dropcart\PhpClient\Helpers\Str;


/**
 * Class DropcartClientOptions
 * @package Dropcart\PhpClient
 *
 * @method DropcartClientOptions setPrivateKey(string $private_key) Your private key
 * @method string getPrivateKey(mixed $default = null) Your private key
 *
 * @method DropcartClientOptions setPublicKey(string $public_key) Your public key
 * @method string getPublicKey(mixed $default = null) Your public key
 *
 * @method DropcartClientOptions setTimeout(float $timeout) The timeout in seconds
 * @method string getTimeout(float $default = 1.0) Get the timeout in seconds
 *
 * @method DropcartClientOptions setBaseUri(string $base_uri) The base URI
 * @method string getBaseUri(string $default = 'https://rest-api.dropcart.nl') The base URI
 *
 * @method DropcartClientOptions setUrl(string $url) Url where request comes from
 * @method string getUrl(string $default = 'current url')
 */
class DropcartClientOptions {

	private static $instance;
	private static $options = [
		'private_key'   => null,
		'public_key'    => null,
		'url'           => null,

		'timeout'       => 2.0,
		'base_uri'      => 'https://rest-api.dropcart.nl',
		'cert'          => __DIR__ . '/cacert.pem',
		'verify'        => true,
	];


	public function __call( $name, $arguments ) {
		$firstThreeLetters = substr($name, 0, 3);
		if($firstThreeLetters == 'get')
		{
			$name = Str::toSnakeCase(substr($name, 3));
			return $this->get($name, isset($arguments[0]) ? $arguments[0] : null);
		} else if($firstThreeLetters == 'set')
		{
			$name = Str::toSnakeCase(substr($name, 3));
			return $this->set($name, $arguments[0]);
		}
	}


	/**
	 * @param $name
	 * @param $value
	 *
	 * @return DropcartClientOptions
	 */
	public function set($name, $value)
	{
		static::$options[$name] = $value;

		return $this->getInstance();
	}

	/**
	 * @param      $name
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		if(isset(static::$options[$name]))
			return static::$options[$name];

		return $default;
	}


	/**
	 * Get current instance
	 *
	 * @return DropcartClientOptions
	 */
	public function getInstance()
	{
		if(!self::$instance)
			self::$instance = new DropcartClientOptions();

		return self::$instance;
	}


	/**
	 * Get all the options
	 *
	 * @return array
	 */
	public function getAll()
	{
		return static::$options;
	}

	/**
	 * Set all the options at once
	 *
	 * @param array $options The options
	 */
	public function setAll($options)
	{
		static::$options = $options;
	}
}