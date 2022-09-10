<?php

namespace Omnipay\Esto\Api;

use Omnipay\Common\Http\ClientInterface;

/**
 * @docs https://esto.docs.apiary.io/
 */
class Client
{
    protected $httpClient;
    protected $url;
    protected $username;
    protected $password;

    public function __construct($url, $username, $password, ClientInterface $httpClient = null)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->httpClient = $httpClient ?: $this->getDefaultHttpClient();
    }

    public function calculatePayments($amount)
    {
        $params = [
            'amount' => $amount,
            'shop_id' => $this->username,
            'down_payment' => 0,
        ];

        return $this->fetch('v2/calculate/all-payments'.'?'.http_build_query($params));
    }

    public function getAllowedPeriods($amount)
    {
        $params = [
            'amount' => $amount,
            'shop_id' => $this->username,
        ];

        return $this->fetch('v2/calculate/periods'.'?'.http_build_query($params));
    }

    public function calculatePaymentForPeriod($amount, $period_months)
    {
        $params = [
            'amount' => $amount,
            'period_months' => $period_months,
            'down_payment' => 0,
        ];

        return $this->fetch('v2/calculate/payment'.'?'.http_build_query($params));
    }

    /**
     * @param $amount
     * @param $currency
     * @param $reference
     * @param $returnURL
     * @param $notificationURL
     * @param $connectionMode string live or test
     * @param $customer
     * @param $items
     * @return mixed
     * @throws \Exception
     */
    public function purchase($amount, $currency, $reference, $returnURL, $notificationURL, $connectionMode, $customer, $items)
    {
        return $this->send('v2/purchase/redirect', array(
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'return_url' => $returnURL,
            'notification_url' => $notificationURL,
            'connection_mode' => $connectionMode,
            'items' => $items,
            'customer' => $customer,
        ));
    }

    public function fetch($endpoint)
    {
        $response = $this->request($endpoint);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function send($endpoint, $request)
    {
        $response = $this->request($endpoint, 'POST', json_encode($request));
        $content = json_decode($response->getBody()->getContents(), true);

        $verification = strtoupper(hash('sha512', sprintf('%s%s', $content['data'], $this->password)));

        if ($content['mac'] != $verification) {
            throw new \Exception(sprintf('invalid mac %s != %s', $verification, $content['mac']));
        }

        return json_decode($content['data'], true);
    }

    /**
     * @param $endpoint
     * @param $method
     * @param $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Http\Client\Exception
     */
    protected function request($endpoint, $method = 'GET', $body = null)
    {
        $response = $this->httpClient->request(
            $method,
            sprintf('%s/%s', $this->url, $endpoint),
            [
                'Authorization' => 'Basic '.base64_encode($this->username.':'.$this->password),
                'Content-Type' => 'application/json',
            ],
            $body
        );

        if (200 !== $response->getStatusCode()) {
            throw new \Exception(sprintf('request failed (%d): %s', $response->getStatusCode(), $response->getBody()->getContents()), $response->getStatusCode());
        }

        return $response;
    }

    /**
     * Get the global default HTTP client.
     *
     * @return ClientInterface
     */
    protected function getDefaultHttpClient()
    {
        return new \Omnipay\Common\Http\Client();
    }
}
