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
 * File: Chainable.php
 * Date: 16-01-18 13:40
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient\Helpers;


use function GuzzleHttp\Psr7\build_query;

/**
 * Class Caller
 * @package Dropcart\PhpClient\Helpers
 */
class Caller {

	public $ignoreChain     = ['get', 'post', 'patch', 'put', 'delete', 'head', 'connect'];

	private $traceMethods   = [];
	private $traceArguments = [];

	private $params = [];
	private $query  = [];
	private $files  = [];

	public function __construct($name = null) {
		$this->addChain($name);
	}

	public static function __callStatic( $name, $arguments ) {
		return new Caller($name);
	}


	private function addChain($method, $arguments = [])
	{

		$this->traceMethods[] = $method;
		$this->traceArguments[] = $arguments;
	}

	public function __call( $name, $arguments ) {
		$this->addChain($name, $arguments);

		return $this;
	}

	/**
	 * @param string    $name
	 * @param mixed     $value
	 *
	 * @return $this
	 */
	public function addParam($name, $value)
	{
		$this->params[$name] = $value;

		return $this;
	}

	/**
	 * @param array $array  Add multiple params [$name => $value]
	 *
	 * @return $this
	 */
	public function addParams($array)
	{
		foreach($array as $name => $value)
		{
			$this->addParam($name, $value);
		}

		return $this;
	}

	public function addQuery($name, $value)
	{
		$this->query[$name] = $value;

		return $this;
	}

	// TODO: implement files

	/**
	 * @return string The SHA256 of the request
	 */
	public function getHash()
	{
		$string = "";
		foreach($this->traceMethods as $k => $method)
		{
			$string .= ".{$method}[" . implode(',', $this->traceArguments[$k]) . "]";
		}

		return hash("sha256", $string);
	}

	/**
	 * @param int $index
	 *
	 * @return string   Method corresponding with the index
	 */
	public function getMethod($index = 1)
	{
		if($index < 1) $index = 0;
		else $index--;

		if(!isset($this->traceMethods[$index]))
			throw new \InvalidArgumentException("This method doesn't exists.");

		return $this->traceMethods[$index];
	}

	/**
	 * @param int $index
	 *
	 * @return array    The arguments
	 */
	public function getArguments($index = 1)
	{
		if($index < 1) $index = 0;
		else $index--;

		if(!isset($this->traceArguments[$index]))
			throw new \InvalidArgumentException("This index doesn't exists.");

		return $this->traceArguments[$index] ?: [];
	}

	/**
	 * Returns the number of chained methods.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->traceMethods);
	}

	/**
	 * Walk to previous called methods.
	 *
	 * @return \Generator   [method => [arguments]]
	 */
	public function loop()
	{
		foreach($this->traceMethods as $k => $m)
		{
			yield $m => $this->traceArguments[$k];
		}

	}

	public function __toString() {
		$result = '<ul>';
		foreach($this->loop() as $method => $arguments)
		{
			$result .= "\n\t<li><b>{$method}</b>";
			if(count($arguments) > 0)
			{
				$result .= "\n\t\t:<ul>";
				foreach($arguments as $a) { $result .= "\n\t\t\t<li>{$a}</li>"; }
				$result .= "\n\t\t</ul>";
			}
			$result .= "\n\t</li>";
		}
		$result .= "\n</ul>";

		return $result;
	}

	/**
	 * Build the URL based on the chain.
	 *
	 * @param string $base_url
	 * @param bool   $withQuery
	 *
	 * @return string
	 */
	public function getUrl($base_url = '', $withQuery = true)
	{
		$url = $base_url;
		foreach($this->loop() as $method => $arguments)
		{
			if(!in_array($method, $this->ignoreChain))
				$url .= "/" . $method;

			if(count($arguments) > 0)
				$url .= "/" . implode("/", $arguments);
		}

		if(count($this->query) > 0 && $withQuery)
			$url .= "?" . build_query($this->query);

		return $url;
	}

	/**
	 * Get the parameters
	 *
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return array
	 */
	public function getQueryAsArray()
	{
		return $this->query;
	}

	/**
	 * @return string
	 */
	public function getQueryString()
	{
		return build_query($this->query);
	}

	/**
	 * Get the HTTP Method
	 *
	 * @return string The HTTP Method or FALSE on failure
	 */
	public function getHttpMethod()
	{
		$last_method = $this->getMethod(count($this->traceMethods));
		if(in_array($last_method, $this->ignoreChain))
		{
			return strtoupper($last_method);
		}

		return FALSE;
	}


	/**
	 * Check if the call has query parameters
	 *
	 * @return bool
	 */
	public function hasQuery()
	{
		return (count($this->query) > 0);
	}

	/**
	 * Check if the call has body/form parameters
	 *
	 * @return bool
	 */
	public function hasParams()
	{
		return (count($this->params) > 0);
	}

	/**
	 * Check if the call contains files.
	 *
	 * @return bool
	 */
	public function hasFiles()
	{
		return (count($this->files) > 0);
	}

}