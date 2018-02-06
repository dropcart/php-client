# Dropcart PHP Client

Dropcart makes it extremely easy setting up an online shop.
All orders, payments and invoices are created and processed automatically. There is no need for for pushing the order manually to a wholesaler, or to create a package slip, or whatever. 

This Dropcart PHP Client is the official PHP client for the REST API provided by Dropcart for setting up your own web frontend.

 + **English**
   + [Installation](#installation)
     + [Via composer](#via-composer-(preferred))
     + [Standalone](#standalone)
   + [Usage](#usage)
   + [Licence](#license)
   + [Support](#support)

## Installation

Installation is rather easy. This client is available as a package on [Packagist](https://packagist.org/) and thus addable to your project via composer. 
_This is the preferred method_.

The client is also available as standalone .zip. 

### Via composer (preferred)

```bash
$ composer require dropcart/dropcart-php-client
```

Add `require vendor/autoload.php` to the files where you want to use the client. (`../` is of course relative to where you require the file)

### Standalone

1. Download the latest version of the [build/DropcartPhpClient.zip](https://raw.githubusercontent.com/dropcart/dropcart-php-client/master/build/DropcartPhpClient.zip)
2. Extract and upload via (S)FTP to your shared hosting just outside of de document root.
3. Add `require ../vendor/autoload.php` to the files where you want to use the client. (`../` is of course relative to where you require the file)

## Usage

You'll need your public and private key. Login on the [Dropcart Management Console](https://my.dropcart.nl/login) or [register](https://dropcart.nl) to obtain those keys.

##### **First set your keys**
```php
\Dropcart\PhpClient\DropcartClient::setPublicKey('PUBLIC_KEY');
\Dropcart\PhpClient\DropcartClient::setPrivateKey('PRIVATE_KEY');
``` 

##### **Making a request**
You can make request to the different services by calling it as method:
```php
\Dropcart\PhpClient\DropcartClient::catalog();
\Dropcart\PhpClient\DropcartClient::catalog()->products();
\Dropcart\PhpClient\DropcartClient::catalog()->brands();
\Dropcart\PhpClient\DropcartClient::catalog()->categories();
```

The latest method is always one of these:
```php
->get(...$args)
->post(...$args)
->put(...$args)
->delete(...$args);
```

By doing so the code will generate the appropiate URL with the requested HTTP method. For example
```php
\Dropcart\PhpClient\DropcartClient::catalog()->products()->get(12332);
// Will make a GET request to:
// https://rest-api.dropcart.nl/catalog/products/12332

\Dropcart\PhpClient\DropcartClient::catalog(34)->products()->get();
// Will make a GET request to:
// https://rest-api.dropcart.nl/catalog/34/products (this will actually fail because this isn't a valid endpoint)
```

If you need to send parameters along with a post or put request you'll use `addParam($name, $value)` or `addParams($array)`
```php
\Dropcart\PhpClient\DropcartClient::catalog()->products()->addParams([
	'name' => 'New Product',
	'description' => 'A descriptive text about this new and awesome product. You need to buy this, yo!'
])->post();
```

##### **Getting the response**
This client uses the amazing [Guzzle](http://docs.guzzlephp.org/en/stable/) for making request. The response are Psr7 Responses.
You'll get the wanted JSON by doing:
```php
try {
	$response   = \Dropcart\PhpClient\DropcartClient::catalog(34)->products()->get();
} catch(DropcartClientException $e) {
	die('Client error:' . $e->getMessage());
} catch (\Exception $e)
{
	die('Server error:' . $e->getMessage());
}
	
$json       = $respons->getBody();
``` 

For a global overview of all the REST functions check [REST.md](REST.md)

## License

See the [LICENSE](LICENSE) file for license rights and limitations (MIT).

## Support

Please file an GitHub Issue when there are errors in the code.

When failing to install please contact us: [info@dropcart.nl](mailto:info@dropcart.nl)


## Nederlands

Met Dropcart is het opzetten van een webshop bijzonder eenvoudig. Alle bestellingen, betalingen en facturen worden automatisch aangemaakt en verwerkt. Het is dus niet meer nodig om handmatig een bestelling bij een groothandel in te voeren, pakbon te verzenden etcetera. 
