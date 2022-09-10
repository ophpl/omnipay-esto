<?php

namespace Omnipay\Esto\Tests;

use Omnipay\Esto\Gateway;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    public $gateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setUrl('url');
        $this->gateway->setUsername('username');
        $this->gateway->setPassword('password');
        $this->gateway->setTestMode(true);
    }

    public function testGateway()
    {
        $this->assertSame('url', $this->gateway->getUrl());
        $this->assertSame('username', $this->gateway->getUsername());
        $this->assertSame('password', $this->gateway->getPassword());
        $this->assertTrue($this->gateway->getTestMode());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase();
        $this->assertInstanceOf('Omnipay\Esto\Message\PurchaseRequest', $request);
    }
}
