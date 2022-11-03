# Omnipay: Esto

**Esto gateway for Omnipay payment processing library**

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements [Esto](https://esto.eu) support for Omnipay.

## Installation

Esto driver is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "ophpl/omnipay-esto": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Esto

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

### Example
```php
<?php

include 'vendor/autoload.php';

use GuzzleHttp\Client;
use Omnipay\Common\Http\Client as OmnipayClient;
use Omnipay\Omnipay;

$adapter = new Client();
$httpClient = new OmnipayClient($adapter);

$gateway = Omnipay::create('Esto', $httpClient);
$gateway->setUrl('https://api.esto.ee');
$gateway->setUsername('username');
$gateway->setPassword('password');
$gateway->setTestMode(true);

$request = $gateway->purchase([
    'amount' => 550.50,
    'currency' => 'EUR',
    'transactionReference' => 'ref-1',
    'scheduleType' => 'ESTO_X',
    'returnUrl' => 'http://localhost/return.php',
    'notifyUrl' => 'http://localhost/notify.php',
]);

$data = $request->getData();
$result = $request->sendData($data);

header("Location: ".$result->getRedirectUrl());
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay), so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/ophpl/omnipay-esto/issues),
or better yet, fork the library and submit a pull request.
