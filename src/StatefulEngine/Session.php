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
 * File: Session.php
 * Date: 09-01-18 15:57
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient\StatefulEngine;


class Session implements StatefulEngine {

	public function __construct() {
		if(empty(session_id()))
			@session_start();
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear() {
		unset($_SESSION);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		if(isset($_SESSION[$key]))
			unset($_SESSION[$key]);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function read( $key, $default = null ) {
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];

		return $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( $key, $value ) {
		$_SESSION[$key] = $value;

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function listKeys() {
		return array_keys($_SESSION);
	}

}