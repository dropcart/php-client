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




//async::setTest('staticContext', false);
//echo "<br>set static - read static: " . async::getTest();
//echo "<br>set static - read self: " . async::getTest(true);
//
//async::setTest('selfContext', true);
//echo "<br>set self - read static: " . async::getTest();
//echo "<br>set self - read self: " . async::getTest(true);

require __DIR__ . '/../vendor/autoload.php';

\Dropcart\PhpClient\DropcartClient::options()->setBaseUri('http://rest-api.sandbox.dropcart.nl/v3');
//\Dropcart\PhpClient\DropcartClient::options()->setBaseUri('http://rest-api.local.dropcart.app');
\Dropcart\PhpClient\DropcartClient::options()->setPublicKey('1cfb463014c34c3d78605a7599991afa8e00c8363069c3bbc416b27df19357d1');
\Dropcart\PhpClient\DropcartClient::options()->setPrivateKey('c1efbfac875236860fc2c67d08e39227017af89600c6ae671279d62a767551a7');
\Dropcart\PhpClient\DropcartClient::options()->setSync(true);

$p = [];

$one = \Dropcart\PhpClient\DropcartClient::catalog()->products();
echo $one->getUrl() . "<br>";
$start = microtime(true);
$one = $one->get();
$end = microtime(true);
echo "Start: {$start}<br>";
echo "End: {$end}<br>";
echo "Duration: " . ($end-$start) . "<br>";
echo "---- Result: " . $one->getStatusCode() . ": " . $one->getReasonPhrase() . "<br><br>";
$result = json_decode($one->getBody()->getContents());
echo "TOTAL: " . $result->total . "<br>";

echo "<hr>";
//echo $one->getBody()->getContents();

$one = \Dropcart\PhpClient\DropcartClient::catalog()->products();
echo $one->getUrl() . "<br>";
$start = microtime( true );
$one   = $one->get( 5 );
$end   = microtime( true );
echo "Start: {$start}<br>";
echo "End: {$end}<br>";
echo "Duration: " . ( $end - $start ) . "<br>";
echo "---- Result: " . $one->getStatusCode() . ": " . $one->getReasonPhrase() . "<br><br>";
//$one = \Dropcart\PhpClient\DropcartClient::catalog(123, 23234, 23443, ['asds' => 2234])->products();
//echo $one->getUrl() . "<br>";
//$start = microtime(true);
//$one = $one->get(25);
//$end = microtime(true);
//echo "Start: {$start}<br>";
//echo "End: {$end}<br>";
//echo "Duration: " . ($end-$start) . "<br>";
//echo "---- Result: " . $one->getStatusCode() . ": " . $one->getReasonPhrase() . "<br><br>";
//
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products();
//echo $one->getUrl() . "<br>";
//$start = microtime(true);
//$one = $one->post(25);
//$end = microtime(true);
//echo "Start: {$start}<br>";
//echo "End: {$end}<br>";
//echo "Duration: " . ($end-$start) . "<br>";
//echo "---- Result: " . $one->getStatusCode() . ": " . $one->getReasonPhrase() . "<br><br>";
//
//$one = \Dropcart\PhpClient\DropcartClient::catalog()->products();
//echo $one->getUrl() . "<br>";
//$start = microtime(true);
//$one = $one->get();
//$end = microtime(true);
//echo "Start: {$start}<br>";
//echo "End: {$end}<br>";
//echo "Duration: " . ($end-$start) . "<br>";
//echo "---- Result: " . $one->getStatusCode() . ": " . $one->getReasonPhrase() . "<br><br>";