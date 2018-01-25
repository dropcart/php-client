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
 * File: calltest.php
 * Date: 09-01-18 17:18
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


class async {

	private $trace = [];
	static $cache = [];

	private static $testing = false;

	public function __construct($name, $arguments) {
		$this->trace[] = $name;
	}

	public function __call( $name, $arguments = []) {
		$this->trace[] = $name;

		return $this;
	}

	public static function __callStatic( $name, $arguments ) {
		return (new async($name, $arguments));
	}

	public function post()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('post', func_get_args()));
	}
	public function put()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('put', func_get_args()));
	}

	/**
	 * The HTTP GET method requests a representation of the specified resource. Requests using GET should only retrieve data.
	 *
	 * @param   mixed [$resource optional]
	 * @return mixed
	 */
	public function get()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('get', func_get_args()));
	}

	/**
	 * The HTTP DELETE request method deletes the specified resource.
	 *
	 * @param  mixed    [$resource The resource]
	 * @return null
	 */
	public function delete()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('delete', func_get_args()));
	}


	/**
	 * The HTTP CONNECT method method starts two-way communications with the requested resource. It can be used to open a tunnel.
	 *
	 * For example, the CONNECT method can be used to access websites that use SSL (HTTPS). The client asks an HTTP Proxy server to tunnel the TCP connection to the desired destination. The server then proceeds to make the connection on behalf of the client. Once the connection has been established by the server, the Proxy server continues to proxy the TCP stream to and from the client.
	 *
	 * @return mixed
	 */
	public function connect()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('connect', func_get_args()));
	}
	public function head()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('head', func_get_args()));
	}


	public function options()
	{
		return call_user_func_array([$this, 'send'], $this->argsListAsArray('options', func_get_args()));
	}


	private function argsListAsArray($type, $args = [])
	{
		array_unshift($args, $type);

		return $args;
	}

	private function traceAsHash($type, $args)
	{
		$callStack = implode('.', $this->trace);
		$callStack .= ".{$type}." . serialize($args);

		return md5($callStack);
	}

	private function send($type, $args)
	{
		echo "<br>";
		$hash = $this->traceAsHash($type, $args);
		if($this->hasCache($hash))
		{
			echo "<b>Cache!!</b>";
			return $this->getCache($hash)['result'];
		}

		$this->setCache($hash, $type, $args, '');

		echo "<b>$type</b>:<br>";
		var_dump($this->trace);

		echo "<br><br><b>Arguments:</b><br>";
		var_dump($args);
	}

	public function getHistory()
	{
		return async::$cache;
	}

	private function hasCache($hash)
	{
		return (isset(static::$cache[$hash]));
	}

	private function getCache($hash)
	{
		return static::$cache[$hash];
	}

	private function setCache($hash, $type, $args, $result)
	{
		async::$cache[$hash] = [
			'type' => $type,
			'args' => $args,
			'result' => $result
		];
	}

	public static function setTest($value = false, $asSelf = false)
	{
		if($asSelf)
			self::$testing = $value;

		static::$testing = $value;
	}

	public static function getTest($asSelf = false)
	{
		if($asSelf)
			return self::$testing;

		return static::$testing;
	}
}

//async::setTest('staticContext', false);
//echo "<br>set static - read static: " . async::getTest();
//echo "<br>set static - read self: " . async::getTest(true);
//
//async::setTest('selfContext', true);
//echo "<br>set self - read static: " . async::getTest();
//echo "<br>set self - read self: " . async::getTest(true);

require __DIR__ . '/../vendor/autoload.php';

\Dropcart\PhpClient\DropcartClient::options()->setBaseUri('https://rest-api.sandbox.dropcart.nl');
//\Dropcart\PhpClient\DropcartClient::options()->setBaseUri('http://rest-api.local.dropcart.app');
\Dropcart\PhpClient\DropcartClient::options()->setPublicKey('1cfb463014c34c3d78605a7599991afa8e00c8363069c3bbc416b27df19357d1');
\Dropcart\PhpClient\DropcartClient::options()->setPrivateKey('c1efbfac875236860fc2c67d08e39227017af89600c6ae671279d62a767551a7');
\Dropcart\PhpClient\DropcartClient::options()->setSync(true);

$p = [];

//$one = \Dropcart\PhpClient\DropcartClient::catalog()->get();
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();

$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get();
echo $one->getBody()->getContents();

$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get(25);
echo $one->getBody()->getContents();
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();

$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->post(25);
echo $one->getBody()->getContents();

$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get();
echo $one->getBody()->getContents();

//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get();
//echo $one->getBody()->getContents();
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get();
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();
//
////echo "<br><h5>RESULT:</h5>"; var_dump($response); echo "\n------------------\n\n";
//
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get(3);
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();
//$p[] = $one;
//
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get(4);
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();
//$p[] = $one;
//
//\Dropcart\PhpClient\DropcartClient::options()->setCache(false);
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get(3,4);
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();
//$p[] = $one;
//
//
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get(4);
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();
//$p[] = $one;
//
//\Dropcart\PhpClient\DropcartClient::options()->setCache(true);
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products()->get(3,4);
//echo "<h5>Result Code:</h5>" . $one->getStatusCode() . " - " . $one->getReasonPhrase();
//$p[] = $one;
//
//echo "<hr>";
//\GuzzleHttp\Promise\settle($p)->wait();

//\Dropcart\PhpClient\DropcartClient::catalog()->products()->get(3);

//async::catalog()->test()->get(2);
//async::order()->create()->post([
//	'title'         => 'Printer a332209',
//	'description'   => 'Dit is de beschrijving van de printer'
//]);
//
//async::catalog()->test()->get(2);
//
//
//var_dump(async::$cache);