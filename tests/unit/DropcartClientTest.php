<?php

/**
 * ---------------------------------------------------
 * Created by Jason de Ridder <mail@deargonauten.com>.
 * ---------------------------------------------------
 * File: DropcartClientTest.php
 * Date: 06-02-18
 * Time: 10:52
 */

namespace Dropcart\PhpClient\Tests\Unit;

use Dropcart\PhpClient\DropcartClient;
use Dropcart\PhpClient\DropcartClientException;
use Dropcart\PhpClient\DropcartClientOptions;
use Dropcart\PhpClient\Helpers\Caller;
use PHPUnit\Framework\TestCase;

class DropcartClientTest extends TestCase {

	public function test__callStatic() {
		$this->assertInstanceOf(DropcartClientOptions::class, DropcartClient::setOption('test', 'test'));
		$this->assertEquals('test', DropcartClient::getOption('test', 'test'));

		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog());
	}

    /** @noinspection PhpUndefinedMethodInspection */
    public function test__call() {
		$this->assertInstanceOf(Caller::class, DropcartClient::catalog()->addParam('test', 'test'));
		$this->assertInstanceOf(Caller::class, DropcartClient::catalog()->addParams(['test' => 'test']));

		$this->assertEquals('https://rest-api.dropcart.nl', DropcartClient::options()->getBaseUri());

		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->products());
	}

	public function testPostEndpointOnServiceException()
	{
		$this->expectExceptionMessageMatches("/Method \[post\] doesn't exists on \'.*\'/");
		$this->expectException(DropcartClientException::class);

        /** @noinspection PhpUndefinedMethodInspection */
        DropcartClient::catalog()->post();
	}

	public function testPutEndpointOnServiceException()
	{
		$this->expectExceptionMessageMatches("/Method \[put\] doesn't exists on \'.*\'/");
		$this->expectException(DropcartClientException::class);

        /** @noinspection PhpUndefinedMethodInspection */
        DropcartClient::catalog()->put();
	}

	public function testGetEndpointOnServiceException()
	{
		$this->expectExceptionMessageMatches("/Method \[get\] doesn't exists on \'.*\'/");
		$this->expectException(DropcartClientException::class);

        /** @noinspection PhpUndefinedMethodInspection */
        DropcartClient::catalog()->get();
	}

	public function testPatchEndpointOnServiceException()
	{
		$this->expectExceptionMessageMatches("/Method \[patch\] doesn't exists on \'.*\'/");
		$this->expectException(DropcartClientException::class);

        /** @noinspection PhpUndefinedMethodInspection */
        DropcartClient::catalog()->patch();
	}


	public function testDeleteEndpointOnServiceException()
	{
		$this->expectExceptionMessageMatches("/Method \[delete\] doesn't exists on \'.*\'/");
        /** @noinspection PhpUndefinedMethodInspection */
        $this->expectException(DropcartClient::catalog()->delete());
	}

	public function testGetInstance() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::getInstance());
	}

	public function testOptions() {
		$this->assertInstanceOf(DropcartClientOptions::class, DropcartClient::options());
	}

	public function testSetResult() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::setResult('test', 'test'));
	}

	public function testCatalog() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->brands());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->categories());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->products());

		$this->expectExceptionMessageMatches("/Method \[.*\] doesn't exists on \'.*\'/");
        /** @noinspection PhpUndefinedMethodInspection */
        DropcartClient::catalog()->nonExisting();
	}

	public function testOrder() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::order());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::order()->orders());

	}

	public function testManagement()
	{
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management()->clients());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management()->organisations());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management()->countries());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management()->couriers());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management()->stores());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::management()->suppliers());

	}

	public function testMe()
	{
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::me());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::me()->organisations());

	}

	public function testSetPublicKey() {
		$this->assertInstanceOf(DropcartClientOptions::class, DropcartClient::setPublicKey("test"));
	}

	public function testSetPrivateKey() {
		$this->assertInstanceOf(DropcartClientOptions::class, DropcartClient::setPrivateKey("test"));
	}
}
