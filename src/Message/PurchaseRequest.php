<?php

namespace Omnipay\Esto\Message;

use Omnipay\Esto\Api\Client;

class PurchaseRequest extends AbstractRequest
{
    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        $this->validate(
            'amount',
            'currency',
            'transactionReference',
            'returnUrl',
            'notifyUrl'
        );

        $request = [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'transaction_reference' => $this->getTransactionReference(),
            'return_url' => $this->getReturnUrl(),
            'notification_url' => $this->getNotifyUrl(),
            'customer' => [],
            'items' => [],
        ];

        if (!empty($this->getCard())) {
            $request['customer'] = [
                'first_name' => $this->getCard()->getFirstName(),
                'last_name' => $this->getCard()->getLastName(),
                'email' => $this->getCard()->getEmail(),
                'phone' => $this->getCard()->getPhone(),
                'address' => trim(sprintf('%s %s', $this->getCard()->getAddress1(), $this->getCard()->getAddress2())),
                'city' => $this->getCard()->getCity(),
                'post_code' => $this->getCard()->getPostcode(),
            ];
        }

        if (!empty($this->getItems())) {
            foreach ($this->getItems() as $item) {
                /* @var \Omnipay\Common\ItemInterface $item */
                $request['items'][] = [
                    'name' => $item->getName(),
                    'unit_price' => $item->getPrice(),
                    'quantity' => $item->getQuantity(),
                ];
            }
        }

        return $request;
    }

    /**
     * {@inheritDoc}
     */
    public function sendData($data)
    {
        $client = new Client($this->getUrl(), $this->getUsername(), $this->getPassword(), $this->httpClient);

        $result = $client->purchase(
            $data['amount'],
            $data['currency'],
            $data['transaction_reference'],
            $data['return_url'],
            $data['notification_url'],
            $this->getTestMode() ? 'test' : 'live',
            $data['customer'],
            $data['items'],
        );

        return $this->response = new PurchaseResponse($this, $result);
    }
}
