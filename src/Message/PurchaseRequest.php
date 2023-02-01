<?php

namespace Omnipay\Esto\Message;

use Omnipay\Esto\Api\Client;

class PurchaseRequest extends AbstractRequest
{
    /**
     * Get schedule type.
     *
     * @return string schedule type
     */
    public function getScheduleType()
    {
        return $this->getParameter('scheduleType');
    }

    /**
     * Set schedule type.
     *
     * Application type that will be selected for the customer in the application flow. Defaults to REGULAR.
     *
     * @param string $value schedule type
     *
     * @return $this
     */
    public function setScheduleType($value)
    {
        return $this->setParameter('scheduleType', $value);
    }

    /**
     * Get display schedule types.
     *
     * @return string display schedule types
     */
    public function getDisplayScheduleTypes()
    {
        return $this->getParameter('displayScheduleTypes');
    }

    /**
     * Set display schedule types.
     *
     * Array of schedule types to choose from that will be displayed to the client during the application flow.
     * Leave it empty to display no options. At least 2 options must be specified in order to show options to the client.
     * Only options that are enable for you can be displayed.
     *
     * @param string $value display schedule types
     *
     * @return $this
     */
    public function setDisplayScheduleTypes($value)
    {
        return $this->setParameter('displayScheduleTypes', $value);
    }

    /**
     * Get allowed customer types
     *
     * @return string allowed customer types
     */
    public function getAllowedCustomerTypes()
    {
        return $this->getParameter('allowedCustomerTypes');
    }

    /**
     * Set allowed customer types.
     *
     * If you want to specify, which types of customers can apply for the loan. Leave blank for all types!
     *
     * @param string $value allowed customer types
     *
     * @return $this
     */
    public function setAllowedCustomerTypes($value)
    {
        return $this->setParameter('allowedCustomerTypes', $value);
    }

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
            'connection_mode' => ($this->getTestMode() ? 'test' : 'live'),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'transaction_reference' => $this->getTransactionReference(),
            'return_url' => $this->getReturnUrl(),
            'notification_url' => $this->getNotifyUrl(),
            'customer' => [],
            'items' => [],
            'schedule_type' => $this->getScheduleType(),
            'display_schedule_types' => $this->getDisplayScheduleTypes(),
            'allowed_customer_types' => $this->getallowedCustomerTypes(),
        ];

        if (!empty($this->getCard())) {
            if (!empty($this->getCard())) {
                $request['customer'] = [
                    'first_name' => $this->getCard()->getFirstName(),
                    'last_name' => $this->getCard()->getLastName(),
                    'email' => $this->getCard()->getEmail(),
                    'address' => trim(sprintf('%s %s', $this->getCard()->getAddress1(), $this->getCard()->getAddress2())),
                    'city' => $this->getCard()->getCity(),
                    'post_code' => $this->getCard()->getPostcode(),
                ];

                if (!empty($this->getCard()->getPhone())) {
                    $request['customer']['phone'] = $this->getCard()->getPhone();
                }
            }
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
            $data['connection_mode'],
            $data['customer'],
            $data['items'],
            $data['schedule_type'],
            $data['display_schedule_types'],
            $data['allowed_customer_types'],
        );

        return $this->response = new PurchaseResponse($this, $result);
    }
}
