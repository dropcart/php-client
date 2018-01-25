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
 * File: Rest.php
 * Date: 16-01-18 10:10
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient\Services;
use GuzzleHttp\Psr7\Response;

/**
 * Interface Rest
 * @package Dropcart\Services
 */
interface Rest {


	/**
	 * Get one or more resources
	 *
	 * @param mixed [$var1 optional]
	 * @param mixed [$var2 optional]
	 * @param mixed [$...]
	 * @return Response
	 */
	public function get();

	/**
	 * Add a new resource
	 *
	 * @param mixed [$var1 optional]
	 * @param mixed [$var2 optional]
	 * @param mixed [$...]
	 * @return Response
	 */
	public function post();

	/**
	 * Update a resource
	 *
	 * @param mixed [$var1 optional]
	 * @param mixed [$var2 optional]
	 * @param mixed [$...]
	 * @return Response
	 */
	public function put();

	/**
	 * Remove a resource
	 *
	 * @param mixed [$var1 optional]
	 * @param mixed [$var2 optional]
	 * @param mixed [$...]
	 * @return Response
	 */
	public function delete();


	/*public function head();
	public function options();
	public function connect();*/
}