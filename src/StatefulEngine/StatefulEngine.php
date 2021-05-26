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
 * File: StatefulEngine.php
 * Date: 09-01-18 15:50
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient\StatefulEngine;


interface StatefulEngine {

	/**
	 * Save some information.
	 *
	 * @param string|integer        $key
	 * @param string|array|object   $value
	 *
	 * @return boolean
	 */
	public function save($key, $value);

	/**
	 * Delete a key
	 *
	 * @param string|integer    $key
	 *
	 * @return boolean  Returns true when succeeded, false on error
	 */
	public function delete($key);

	/**
	 * Read out a key.
	 *
	 * @param string|integer    $key
	 * @param null              $default     Returns the default when key doesn't return a value
	 *
	 * @return mixed
	 */
	public function read($key, $default = null);

	/**
	 * Clears out all the saved key-values
	 *
	 * @return boolean  True on success. False on error
	 */
	public function clear();

	/**
	 * List all keys
	 *
	 * @return array
	 */
	public function listKeys();

}