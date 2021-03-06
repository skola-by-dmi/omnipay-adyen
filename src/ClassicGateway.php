<?php

namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\AuthorizeRequest;
use Omnipay\Adyen\Message\CaptureRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Adyen Gateway
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface purchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 */
class ClassicGateway extends AbstractGateway
{
    public function getName()
    {
        return 'AdyenClassic';
    }

    /**
     * Get the gateway Secret Key.
     *
     * Authentication is by means of a single secret API key set as
     * the integrationKey parameter when creating the gateway object.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getParameter('key');
    }

    /**
     * Set Gateway secret key
     *
     * @param  string $value
     * @return \Omnipay\Adyen\ClassicGateway provides a fluent interface.
     */
    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }

    /**
     * Get the gateway Live URL Prefix.
     *
     * This prefix is the combination of the [random] and [company name] from the live endpoint.
     *
     * For example, if this was your live URL:
     * https://1797a841fbb37ca7-AdyenDemo-checkout-live.adyenpayments.com/checkout/v32/payments
     *
     * Then the live URL prefix would be
     * 1797a841fbb37ca7-AdyenDemo
     *
     * @see https://docs.adyen.com/development-resources/live-endpoints#live-url-prefix
     *
     * @return string
     */
    public function getLiveUrlPrefix()
    {
        return $this->getParameter('liveUrlPrefix');
    }

    /**
     * Set Gateway Live Url Prefix
     *
     * @param  string $value
     * @return \Omnipay\Adyen\ClassicGateway provides a fluent interface.
     */
    public function setLiveUrlPrefix($value)
    {
        return $this->setParameter('liveUrlPrefix', $value);
    }

    /**
     * @return mixed
     */
    public function getMerchantAccount()
    {
        return $this->getParameter('merchantAccount');
    }

    /**
     * Set the merchant account for the current transactions.
     * @param $value
     * @return \Omnipay\Adyen\ClassicGateway
     */
    public function setMerchantAccount($value)
    {
        return $this->setParameter('merchantAccount', $value);
    }

    /**
     * Authorize request.
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function authorize(array $options = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * Capture request.
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function capture(array $options = [])
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface purchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }
}
