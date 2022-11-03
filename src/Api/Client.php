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
     * @param float  $amount
     * @param string $currency
     * @param string $reference
     * @param string $returnURL
     * @param string $notificationURL
     * @param string $connectionMode         live or test
     * @param mixed  $customer
     * @param mixed  $items
     * @param string $schedule_type          Application type that will be selected for the customer in the application flow. Defaults to REGULAR.
     * @param array  $display_schedule_types Array of schedule types to choose from that will be displayed to the client during the application flow. Leave it empty to display no options.
     * @param array  $allowed_customer_types If you want to specify, which types of customers can apply for the loan. Leave blank for all types!
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function purchase($amount, $currency, $reference, $returnURL, $notificationURL, $connectionMode, $customer, $items, $schedule_type = 'REGULAR', $display_schedule_types = [], $allowed_customer_types = [])
    {
        $request = [
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'return_url' => $returnURL,
            'notification_url' => $notificationURL,
            'connection_mode' => $connectionMode,
            'items' => $items,
            'customer' => $customer,
        ];

        if (!empty($schedule_type)) {
            $request['schedule_type'] = $schedule_type;
        }

        if (!empty($display_schedule_types)) {
            $request['display_schedule_types'] = $display_schedule_types;
        }

        if (!empty($allowed_customer_types)) {
            $request['allowed_customer_types'] = $allowed_customer_types;
        }

        return $this->send('v2/purchase/redirect', $request);
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
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
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
