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
                $paymentData = $this->getBoletoData();
                break;
            default:
                throw new Exception(sprintf("The Payment method '%s' is not supported on this omnipay driver.", $this->getPaymentMethod()));
                break;
        }

        $data = array_merge($data, $paymentData);

        return $data;
    }

    public function getEndpoint()
    {
        if ($this->getPaymentMethod() === 'boleto' && $this instanceof AuthorizeRequest) {
            return parent::getEndpoint() . '/payments';
        }

        return parent::getEndpoint() . '/authorise';
    }
}
