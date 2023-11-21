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
 * File: build-method.php
 * Date: 06-02-18 17:23
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */
 
require __DIR__ . '/vendor/autoload.php';

$w = "# Allowed methods";

foreach(scandir(__DIR__ . '/src/Services') as $file)
{
	if($file == '.' || $file == '..' || $file == 'Rest.php')
		continue;

	$fileExp = explode('.', $file);
	$interface      = $fileExp[0];
	$interface_lc   = strtolower($interface);
	$reflection     = new \ReflectionClass("Dropcart\\PhpClient\\Services\\{$interface}");

	if(!$reflection->isInterface())
		continue;

	$w .= "\n## {$interface}";

	$w .= "\n\Dropcart\PhpClient\DropcartClient::{$interface}()\n";
	foreach($reflection->getMethods() as $method)
	{
		$w .= "\n + \Dropcart\PhpClient\DropcartClient::{$interface}()->{$method->getName()}()";
		foreach((new ReflectionClass($method->getReturnType()->getName()))->getMethods() as $rm)
		{
			$w .= "\n    + ->{$rm->getName()}(...\$args)";
			//$w .= "\n    {$rm->getDocComment()}";
		}

	}

	$w .= "\n";
}

file_put_contents(__DIR__ . '/REST.md', $w);