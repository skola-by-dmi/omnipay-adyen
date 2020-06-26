<?php

namespace Omnipay\Adyen\Message;

use Exception;

/**
 * Class AuthorizeRequest
 * @package Omnipay\Adyen\Message
 */
class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount');

        $data = [
            'amount' => [
                'currency' => $this->getCurrency(),
                'value' => $this->getAmountInteger(),
            ]
        ];

        switch ($this->getPaymentMethod()) {
            case 'creditcard':
                $paymentData = $this->getPaymentData($this->getCardData());
                break;
            case 'boleto':
                throw new Exception('The boleto payment method was not implemented yet.');
                break;
            default:
                throw new Exception('Payment method not supported');
                break;
        }

        $data = array_merge($data, $paymentData);

        return $data;
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/authorise';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [];
    }
}
