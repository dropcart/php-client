<?php
/**
 * ---------------------------------------------------
 * Created by Jason de Ridder <mail@deargonauten.com>.
 * ---------------------------------------------------
 * File: DropcartClientTest.php
 * Date: 06-02-18
 * Time: 10:29
 */


use Dropcart\PhpClient\DropcartClient;

class DropcartClientFunctionalityTest extends \PHPUnit\Framework\TestCase {

	protected static $config;

	public function setUp()
	{
		if(file_exists(__DIR__ . '/config.php'))
			self::$config = include(__DIR__ . '/config.php');
		else
			throw new Exception("Config needs to be set.");
	}

	public function testStaticInitialisation()
	{
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::getInstance());
	}

	public function testGettingErrorWithoutPublicAndOrPrivateKey()
	{
		$this->expectException(\Dropcart\PhpClient\DropcartClientException::class);
		$this->expectExceptionMessage("Public and/or private key are not set.");

		// Trigger
		DropcartClient::catalog()->brands()->get();
	}

	public function testCallingServiceGetCaller()
	{
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog());
	}

	public function testCallingServiceGetMethod()
	{
		$this->assertInstanceOf(DropcartClient::class, DropcartClient::catalog()->products());
	}

	public function testCallingNotExistingHttpMethod()
	{
		$this->expectException(\Dropcart\PhpClient\DropcartClientException::class);
		$this->expectExceptionMessageRegExp("/HTTP method \[test\] doesn't exist on \'.*\'/");

		DropcartClient::catalog()->products()->test();
	}

	public function testCallingServiceGetResult()
	{
		DropcartClient::setPublicKey("test123");
		DropcartClient::setPrivateKey("test123");
		DropcartClient::options()->set('verify', false);

		$this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, DropcartClient::catalog()->products()->get());
	}


}
