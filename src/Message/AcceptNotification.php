<?php

namespace Omnipay\Esto\Message;

use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Esto\Api\Client;

class AcceptNotification extends AbstractRequest implements NotificationInterface
{

    protected $data;

    /**
     * Initialize the object with parameters, and try to parse the notification payload.
     *
     * If any unknown parameters passed, they will be ignored.
     * If payload was parsed correctly and signature is valid, then response will contain the parsed payload data.
     *
     * @param array $parameters An associative array of parameters
     *
     * @return $this
     * @throws RuntimeException
     */
    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);

        $request = [
            'json' => $this->httpRequest->get('json'),
            'mac' => $this->httpRequest->get('mac'),
        ];

        $client = new Client($this->getUrl(), $this->getUsername(), $this->getPassword(), $this->httpClient);

        if ($client->verifyMac($request)) {
            $this->data = $client->extractRequestData($request);
        }

        return $this;
    }

    /**
     * Get the raw data array for this message.
     * The raw data is from the notification payload.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * There is nothing to send in order to response to this webhook.
     * The merchant site just needs to return a HTTP 200.
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionReference()
    {
        return $this->data['reference'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionStatus()
    {
        return match ($this->getCode()) {
            'APPROVED' => NotificationInterface::STATUS_COMPLETED,
            'REJECTED' => NotificationInterface::STATUS_FAILED,
            default => NotificationInterface::STATUS_FAILED,
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function getCode()
    {
        return $this->data['status'] ?? null;
    }

}
