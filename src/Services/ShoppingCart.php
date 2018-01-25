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
 * File: ShoppingCart.php
 * Date: 09-01-18 15:40
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient\Services;


use Dropcart\StatefulEngine\Session;
use Dropcart\StatefulEngine\StatefulEngine;

class ShoppingCart {

	private $cart = [];
	private $statefulEngine;

	public function __construct(StatefulEngine $statefulEngine = null) {
		if(is_null($statefulEngine))
			$this->statefulEngine = new Session();
		elseif(is_string($statefulEngine))
			$this->statefulEngine = new $statefulEngine;
		else if($statefulEngine instanceof StatefulEngine)
			$this->statefulEngine = $statefulEngine;
		else
			throw \Exception("Stateful engine not supported");


		$this->readSession();
	}


	/**
	 * Add an entry to the cart.
	 *
	 * @param      $product_id
	 * @param int  $quantity
	 * @param null $product_name
	 * @param null $product_description
	 */
	public function add($product_id, $quantity = 1, $product_name = null, $product_description = null)
	{
		if(isset($this->cart[$product_id]))
		{
			return $this->updateSession($product_id, $quantity, $product_name, $product_description);
		}

		if(!is_int($quantity) || $quantity < 1)
			throw \Exception("Quantity needs to be an integer and larger than zero.");

		$this->cart[$product_id] = [
			'id'    => $product_id,
			'name'  => $product_name,
			'description' => $product_description,
			'quantity'  => $quantity
		];
	}

	/**
	 * Remove an item from the cart
	 *
	 * @param $product_id
	 */
	public function remove($product_id)
	{
		if(isset($this->cart[$product_id]))
			unset($this->cart[$product_id]);
	}

	/**
	 * Update an entry in the shopping cart.
	 *
	 * @param      $product_id
	 * @param null $quantity
	 * @param null $product_name
	 * @param null $product_description
	 */
	public function update($product_id, $quantity = null, $product_name = null, $product_description = null)
	{
		if(!is_null($quantity) && is_int($quantity))
		{
			if($quantity < 1)
				$this->remove($product_id);
			else
				$this->cart[$product_id]['quantity'] = $quantity;
		}

		if(!isset($this->cart[$product_id]))
			$this->add($product_id, $quantity, $product_name, $product_description);

		if(!is_null($product_name))
			$this->cart[$product_id]['name'] = $product_name;
		if(!is_null($product_description))
			$this->cart[$product_id]['description'] = $product_description;
	}

	/**
	 * Add one or more to the product list
	 *
	 * @param     $product_id
	 * @param int $quantity     The quantity to add, needs to be higher than 0. Default: 1
	 */
	public function addSome($product_id, $quantity = 1)
	{
		if(!isset($this->cart[$product_id]))
			$this->add($product_id, $quantity);

		if($quantity < 1)
			return;

		$this->cart[$product_id][$quantity] += $quantity;
	}

	/**
	 * Subtract one or more from the product list.
	 *
	 * @param     $product_id
	 * @param int $quantity     The quantity to subtract. Needs to be higher than 0. Default: 1
	 */
	public function subtractSome($product_id, $quantity = 1)
	{
		if(!isset($this->cart[$product_id]) || $quantity < 1)
			return;

		$qty = $this->cart[$product_id]['quantity'];
		if($qty - $quantity < 1)
			$this->remove($product_id);

		$this->cart[$product_id][$quantity] -= $quantity;
	}

	/**
	 * Search within the cart.
	 *
	 * @param string $query The string to search for
	 * @param string $in    Usable values: * (default), 'name' or 'description'
	 *
	 * @return array
	 */
	public function search($query, $in = '*')
	{
		$return = [];

		if($in != '*' && $in != 'name' && $in != 'description')
			return []; // No results

		foreach($this->cart as $product_id => $product)
		{
			if($in == '*')
			{
				if(stristr($product['name'] . ' ' . $product['description'], $query) !== FALSE)
					$return[$product_id] = $product;
			} else if($in == 'name')
			{
				if(stristr($product['name'], $query) !== FALSE)
					$return[$product_id] = $product;
			} else if($in == 'description')
			{
				if(stristr($product['name'], $query) !== FALSE)
					$return[$product_id] = $product;
			}
		}

		return $return;
	}




	private function updateSession()
	{
		$this->statefulEngine->save('shopping_cart', $this->cart);
	}

	private function readSession()
	{
		$this->cart = $this->statefulEngine->read('shopping_cart', []);
	}

	public function __destruct() {
		$this->updateSession();
	}

	public function __sleep() {
		$this->updateSession();
	}

	public function __wakeup() {
		$this->readSession();
	}

}