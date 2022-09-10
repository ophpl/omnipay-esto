<?php

namespace Omnipay\Esto;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Esto\Message\PurchaseRequest;

/**
 * Class Gateway.
 *
 * @phan-file-suppress PhanClassContainsAbstractMethod
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Esto';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'url' => '',
            'username' => '',
            'password' => '',
            'testMode' => false,
        ];
    }

    /**
     * Get api url.
     *
     * @return string url
     */
    public function getUrl()
    {
        return $this->getParameter('url');
    }

    /**
     * Set api url.
     *
     * @param string $value url
     *
     * @return $this
     */
    public function setUrl($value)
    {
        return $this->setParameter('url', $value);
    }

    /**
     * Get api username.
     *
     * @return string username
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * Set api username.
     *
     * @param string $value username
     *
     * @return $this
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * Get api password.
     *
     * @return string password
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * Set api password.
     *
     * @param string $value password
     *
     * @return $this
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return AbstractRequest|PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Esto\Message\PurchaseRequest', $parameters);
    }
}
