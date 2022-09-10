<?php

namespace Omnipay\Esto\Tests\Message;

use Omnipay\Esto\Message\PurchaseRequest;
use Omnipay\Esto\Message\PurchaseResponse;
use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    protected $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testRedirect()
    {
        $response = new PurchaseResponse(
            $this->request,
            [
                'id' => 'purchase-id',
                'purchase_url' => 'https://merchant.esto.ee/application/purchase-id',
            ]
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getTransactionId());
        $this->assertSame('purchase-id', $response->getTransactionReference());
        $this->assertSame('https://merchant.esto.ee/application/purchase-id', $response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertEmpty($response->getRedirectData());
    }
}
