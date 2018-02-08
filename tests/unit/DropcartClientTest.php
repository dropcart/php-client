<?php
/**
 * ---------------------------------------------------
 * Created by Jason de Ridder <mail@deargonauten.com>.
 * ---------------------------------------------------
 * File: DropcartClientTest.php
 * Date: 06-02-18
 * Time: 10:52
 */

require __DIR__ . '/../../vendor/autoload.php';

use Dropcart\PhpClient\DropcartClient;
use PHPUnit\Framework\TestCase;

class DropcartClientTest extends TestCase {

	public function test__callStatic() {
		$this->assertInstanceOf(\Dropcart\PhpClient\DropcartClientOptions::class, DropcartClient::setOption('test', 'test'));
		$this->assertEquals('test', DropcartClient::getOption('test', 'test'));

		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog());
	}

	public function test__call() {
		$this->assertInstanceOf(\Dropcart\PhpClient\Helpers\Caller::class, DropcartClient::catalog()->addParam('test', 'test'));
		$this->assertInstanceOf(\Dropcart\PhpClient\Helpers\Caller::class, DropcartClient::catalog()->addParams(['test' => 'test']));

		$this->assertEquals('https://rest-api.dropcart.nl', DropcartClient::options()->getBaseUri());

		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->products());
	}

	public function testPostEndpointOnServiceException()
	{
		$this->expectExceptionMessageRegExp("/Method \[post\] doesn't exists on \'.*\'/");
		$this->expectException(\Dropcart\PhpClient\DropcartClientException::class);

		DropcartClient::catalog()->post();
	}

	public function testPutEndpointOnServiceException()
	{
		$this->expectExceptionMessageRegExp("/Method \[put\] doesn't exists on \'.*\'/");
		$this->expectException(\Dropcart\PhpClient\DropcartClientException::class);

		DropcartClient::catalog()->put();
	}

	public function testGetEndpointOnServiceException()
	{
		$this->expectExceptionMessageRegExp("/Method \[get\] doesn't exists on \'.*\'/");
		$this->expectException(\Dropcart\PhpClient\DropcartClientException::class);

		DropcartClient::catalog()->get();
	}

	public function testPatchEndpointOnServiceException()
	{
		$this->expectExceptionMessageRegExp("/Method \[patch\] doesn't exists on \'.*\'/");
		$this->expectException(\Dropcart\PhpClient\DropcartClientException::class);

		DropcartClient::catalog()->patch();
	}


	public function testDeleteEndpointOnServiceException()
	{
		$this->expectExceptionMessageRegExp("/Method \[delete\] doesn't exists on \'.*\'/");
		$this->expectException(DropcartClient::catalog()->delete());
	}

	public function testGetInstance() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::getInstance());
	}

	public function testOptions() {
		$this->assertInstanceOf(\Dropcart\PhpClient\DropcartClientOptions::class, DropcartClient::options());
	}

	public function testSetResult() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::setResult('test', 'test'));
	}

	public function testCatalog() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->brands());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->categories());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->products());

		$this->expectExceptionMessageRegExp("/Method \[.*\] doesn't exists on \'.*\'/");
		DropcartClient::catalog()->nonExisting();
	}

	public function testOrder() {
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::order());
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::order()->order());

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
		$this->assertInstanceOf(\Dropcart\PhpClient\DropcartClientOptions::class, DropcartClient::setPublicKey("test"));
	}

	public function testSetPrivateKey() {
		$this->assertInstanceOf(\Dropcart\PhpClient\DropcartClientOptions::class, DropcartClient::setPrivateKey("test"));
	}
}
