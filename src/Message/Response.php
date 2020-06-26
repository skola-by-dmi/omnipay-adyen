<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Adyen Response
 *
 * This is the response class for all Adyen requests.
 *
 * @see \Omnipay\Adyen\ClassicGateway
 */
class Response extends AbstractResponse
{

    /**
     * Possible resultCode values from Adyen docs:
     *  AuthenticationFinished – The payment has been successfully authenticated with 3D Secure 2. Returned for 3D Secure 2 authentication-only transactions.
     *  Authorised – The payment was successfully authorised. This state serves as an indicator to proceed with the delivery of goods and services. This is a final state.
     *  Cancelled – Indicates the payment has been cancelled (either by the shopper or the merchant) before processing was completed. This is a final state.
     *  ChallengeShopper – The issuer requires further shopper interaction before the payment can be authenticated. Returned for 3D Secure 2 transactions.
     *  Error – There was an error when the payment was being processed. The reason is given in the refusalReason field. This is a final state.
     *  IdentifyShopper – The issuer requires the shopper's device fingerprint before the payment can be authenticated. Returned for 3D Secure 2 transactions.
     *  Pending – Indicates that it is not possible to obtain the final status of the payment. This can happen if the systems providing final status information for the payment are unavailable, or if the shopper needs to take further action to complete the payment.
     *  PresentToShopper – Indicates that the response contains additional information that you need to present to a shopper, so that they can use it to complete a payment.
     *  Received – Indicates the payment has successfully been received by Adyen, and will be processed. This is the initial state for all payments.
     *  RedirectShopper – Indicates the shopper should be redirected to an external web page or app to complete the authorisation.
     *  Refused – Indicates the payment was refused. The reason is given in the refusalReason field. This is a final state.
     * @return bool
     */
    public function isSuccessful()
    {
        if (!isset($this->data['resultCode'])) {
            return false;
        }

        return $this->data['resultCode'] == "Authorised" || $this->data['resultCode'] == "Received";
    }

    /**
     * Is the transaction a redirect?
     *
     * @return bool
     */
    public function isRedirect()
    {
        if (!isset($this->data['resultCode'])) {
            return false;
        }

        return $this->data['resultCode'] == "RedirectShopper";
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        if (!isset($this->data['pspReference'])) {
            return null;
        }

        return $this->data['pspReference'];
    }
    /**
     * Get the transaction id.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        if (!isset($this->data['authCode'])) {
            return null;
        }

        return $this->data['authCode'];
    }

    public function getMessage()
    {
        if (!isset($this->data['message'])) {
            return null;
        }

        return $this->data['message'];
    }
}
