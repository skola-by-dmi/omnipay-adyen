<?php


namespace Omnipay\Adyen\Message;


class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency', 'merchantAccount', 'transactionReference');

        /**
         * The amount that needs to be captured. The currency must match the currency used in authorisation,
         * the value must be smaller than or equal to the authorised amount.
         */
       return [
            'modificationAmount' => [
                'currency' => $this->getCurrency(),
                'value' => $this->getAmountInteger(),
            ],
            'merchantAccount' => $this->getMerchantAccount(),
            'originalReference' => $this->getTransactionReference(),
        ];
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/capture';
    }
}
