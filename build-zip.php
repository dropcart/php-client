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

if(file_exists($buildDir . '/DropcartPhpClient.zip') && !$force)
	die('Zip exists already. Use -f or --force flag to override.' . "\n");


$zip  = new ZipArchive;
$return = $zip->open($buildDir . '/DropcartPhpClient.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);

$zip->addFile(__DIR__ . "/README.md", "README.md");
$zip->addFile(__DIR__ . "/VERSION", "VERSION");
$zip->addFile(__DIR__ . "/REST.md", "REST.md");
$zip->addFile(__DIR__ . "/LICENSE", "LICENSE");

function addRecursivly(&$zip, $folder, $localname)
{
	$dir = opendir($folder);
	while ($filename = readdir($dir)) {
		// Discard . and ..
		if ($filename == '.' || $filename == '..')
			continue;

		// Proceed according to type
		$path = $folder . '/' . $filename;
		$localpath = $localname ? ($localname . '/' . $filename) : $filename;
		if (is_dir($path)) {
			// Directory: add & recurse
			$zip->addEmptyDir($localpath);
			addRecursivly($zip, $path, $localpath);
		}
		else if (is_file($path)) {
			// File: just add
			$zip->addFile($path, $localpath);
		}
	}
	closedir($dir);
}

addRecursivly($zip, __DIR__ . '/src', '/src');
addRecursivly($zip, __DIR__ . '/vendor', '/vendor');

$zip->setArchiveComment("This Dropcart PHP Client is the official PHP client for the REST API provided by Dropcart for setting up your own web frontend.");
$zip->close();
