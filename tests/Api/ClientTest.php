<?php

namespace Omnipay\Esto\Tests\Api;

use Omnipay\Esto\Api\Client;
use Omnipay\Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = new Client("https://api.esto.ee", "username", "password", $this->getHttpClient());
    }

    public function testCalculatePayments()
    {
        $this->setMockHttpResponse('GetAllPayments.txt');
        $data = $this->client->calculatePayments(545.50);

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data['periods']);
    }

    public function testGetAllowedPeriods()
    {
        $this->setMockHttpResponse('GetCalculatePeriods.txt');
        $data = $this->client->getAllowedPeriods(545.50);

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data['periods']);
    }

    public function testCalculatePaymentForPeriod()
    {
        $this->setMockHttpResponse('GetCalculatePayment.txt');
        $data = $this->client->calculatePaymentForPeriod(545.50, 6);

        $this->assertNotEmpty($data);
        $this->assertEquals(92.74, $data['monthly_payment']);
        $this->assertEquals(556.41, $data['total_expected']);
        $this->assertEquals(6, $data['period_months']);
    }

    public function testPurchaseFailed()
    {
        $this->expectExceptionCode(422);
        $this->setMockHttpResponse('PostPurchaseRedirect_Failed.txt');
        $data = $this->client->purchase(
            545.50,
            'EUR',
            'ref-'.time(),
            'return-url',
            'notification-url',
            'test',
            [
                'first_name' => 'First name',
                'last_name' => 'Last name',
                'email' => 'some@mail.com',
                'phone' => '56756756',
                'address' => 'Address',
                'city' => 'City',
                'post_code' => 'Postal code',
            ],
            [
                [
                    'name' => 'Item name',
                    'unit_price' => 10.50,
                    'quantity' => 1,
                ],
            ],
        );

        $this->assertNotEmpty($data);
        $this->assertEquals(92.74, $data['monthly_payment']);
        $this->assertEquals(556.41, $data['total_expected']);
        $this->assertEquals(6, $data['period_months']);
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PostPurchaseRedirect_Success.txt');
        $data = $this->client->purchase(
            545.50,
            'EUR',
            'ref',
            'return-url',
            'notification-url',
            'test',
            [
                'first_name' => 'First name',
                'last_name' => 'Last name',
                'email' => 'some@mail.com',
                'phone' => '56756756',
                'address' => 'Address',
                'city' => 'City',
                'post_code' => 'Postal code',
            ],
            [
                [
                    'name' => 'Item name',
                    'unit_price' => 10.50,
                    'quantity' => 1,
                ],
            ],
        );

        $this->assertNotEmpty($data);
        $this->assertEquals('purchase-id', $data['id']);
        $this->assertEquals('CREATED', $data['status']);
        $this->assertEquals('https://merchant.esto.ee/application/purchase-id', $data['purchase_url']);
        $this->assertEquals('ref', $data['merchant_reference']);
        $this->assertEquals(545.50, $data['amount']);
        $this->assertEquals('EUR', $data['currency']);
        $this->assertEquals('return-url', $data['return_url']);
        $this->assertEquals('notification-url', $data['notification_url']);
    }
}
