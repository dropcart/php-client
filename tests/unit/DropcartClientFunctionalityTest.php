<?php
/**
 * ---------------------------------------------------
 * Created by Jason de Ridder <mail@deargonauten.com>.
 * ---------------------------------------------------
 * File: DropcartClientTest.php
 * Date: 06-02-18
 * Time: 10:29
 */

namespace Dropcart\PhpClient\Tests\Unit;

use Dropcart\PhpClient\DropcartClient;
use Dropcart\PhpClient\DropcartClientException;
use Exception;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DropcartClientFunctionalityTest extends TestCase
{

    protected static $config;

    protected function setUp(): void
    {
        if (file_exists(__DIR__ . '/config.php')) {
            self::$config = include(__DIR__ . '/config.php');
        } else {
            throw new Exception("Config needs to be set.");
        }
    }

    public function testStaticInitialisation()
    {
        $this->assertInstanceOf(DropcartClient::class, DropcartClient::getInstance());
    }

    public function testGettingErrorWithoutPublicAndOrPrivateKey()
    {
        $this->expectException(DropcartClientException::class);
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
        $this->expectException(DropcartClientException::class);
        $this->expectExceptionMessageMatches("/HTTP method \[test\] doesn't exist on \'.*\'/");

        /** @noinspection PhpUndefinedMethodInspection */
        DropcartClient::catalog()->products()->test();
    }

    public function testCallingServiceGetResult()
    {
        DropcartClient::setPublicKey("test123");
        DropcartClient::setPrivateKey("test123");
        DropcartClient::options()->set('verify', false);

        $this->assertInstanceOf(Response::class, DropcartClient::catalog()->products()->get());
    }
}
