<?php

namespace Omnipay\Esto\Tests\Message;

use Omnipay\Esto\Message\PurchaseRequest;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'url' => 'api-url',
            'username' => 'username',
            'password' => 'password',
            'transactionReference' => 'ref',
            'amount' => 15.34,
            'currency' => 'EUR',
            'description' => 'Test',
            'returnUrl' => 'https://www.example.com/return.html',
            'notifyUrl' => 'https://www.example.com/notify.html',
        ]);
    }

    public function testGetDataBasic()
    {
        $data = $this->request->getData();

        $this->assertSame('15.34', $data['amount']);
        $this->assertSame('EUR', $data['currency']);
        $this->assertSame('ref', $data['transaction_reference']);
        $this->assertSame('https://www.example.com/return.html', $data['return_url']);
        $this->assertSame('https://www.example.com/notify.html', $data['notification_url']);
        $this->assertEmpty($data['customer']);
        $this->assertEmpty($data['items']);
    }
}
