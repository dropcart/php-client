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
 * File: build-phar.php
 * Date: 06-02-18 15:19
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */

$buildDir = __DIR__ . '/build';
$srcDir = __DIR__;

$force = (isset($argv[1]) && ( $argv[1] == '--force' || $argv[1] == '-f'));

if(file_exists($buildDir . '/DropcartPhpClient.phar') && !$force)
{
	die('Phar exists already. Use -f or --force flag to override.' . "\n");
}
@unlink($buildDir . '/DropcartPhpClient.phar');
$phar = new Phar($buildDir . '/DropcartPhpClient.phar',
				FilesystemIterator::CURRENT_AS_FILEINFO |
						FilesystemIterator::KEY_AS_FILENAME, "dropcart-php-client");

$phar->buildFromDirectory($srcDir, '[src|vendor]');

$phar->compressFiles(Phar::GZ);
