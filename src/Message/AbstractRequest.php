<?php

namespace Omnipay\Esto\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
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
     * Get locale.
     *
     * @return string locale
     */
    public function getLocale()
    {
        return $this->getParameter('locale');
    }

    /**
     * Set locale.
     *
     * @param string $value locale
     *
     * @return $this
     */
    public function setLocale($value)
    {
        return $this->setParameter('locale', $value);
    }

    /**
     * Get language, if not set fallback to locale.
     *
     * @return string language
     */
    public function getLanguage()
    {
        $language = $this->getParameter('language');

        if (empty($language)) {
            $locale = $this->getLocale();

            if (empty($locale)) {
                return '';
            }

            // convert to IETF locale tag if other style is provided and then get first part, primary language
            $language = strtok(str_replace('_', '-', $locale), '-');
        }

        return strtolower($language);
    }

    /**
     * Set language.
     *
     * @param string $value language
     *
     * @return $this
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }
}
