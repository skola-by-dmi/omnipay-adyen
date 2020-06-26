<?php


namespace Omnipay\Adyen\Message;


class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('merchantAccount', 'transactionId');

        return [
            'merchantAccount' => $this->getMerchantAccount(),
            'originalReference' => $this->getTransactionId(),
        ];
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/capture';
    }
}
