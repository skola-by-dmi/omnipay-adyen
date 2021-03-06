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
     * Possible resultCode values from Adyen docs (authorise request):
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
     *
     * In the case of a /capture request the response has a different way to parse.
     * {    "pspReference": "852593379043958E",    "response": "[capture-received]"    }
     * @return bool
     */
    public function isSuccessful()
    {
        // Has error?
        if (isset($this->data['errorCode']) && !empty($this->data['errorCode'])) {
            return false;
        }

        // authorize response
        if (isset($this->data['resultCode'])) {
            return $this->data['resultCode'] == "Authorised" ||
                $this->data['resultCode'] == "Received" ||
                $this->data['resultCode'] == "PresentToShopper" ||
                $this->data['resultCode'] == "RedirectShopper" ||
                $this->data['resultCode'] == "AuthenticationFinished";
        }

        // capture response
        if (isset($this->data['response']) && $this->data['response'] == "[capture-received]") {
            return true;
        }

        return false;
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

    public function getPaymentData()
    {
        return $this->getData();
    }

    public function getRiskAnalysis()
    {
        if (!isset($this->data['fraudResult'])) {
            return null;
        }

        return $this->data['fraudResult'];
    }

    public function getTransactionStatus()
    {
        // the authorize status
        if (isset($this->data['resultCode'])) {
            return $this->data['resultCode'];
        }

        // the capture status
        if (isset($this->data['response'])) {
            return $this->data['response'];
        }

        // When the Adyen's API returns an error response
        if (isset($this->data['errorType'])) {
            return $this->data['errorType'];
        }

        return null;
    }

    public function getPaymentStatus()
    {
        // the authorize status
        if (isset($this->data['resultCode'])) {
            return $this->data['resultCode'];
        }

        // the capture status
        if (isset($this->data['response'])) {
            return $this->data['response'];
        }

        return null;
    }

    /**
     * Get the boleto_url, boleto_barcode and boleto_expiration_date in the
     * transaction object.
     *
     * @return array|null the boleto_url, boleto_barcode and boleto_expiration_date
     */
    public function getBoleto()
    {
        $data = null;

        if (isset($this->data['outputDetails']['boletobancario.url'])) {
            $data = [
                'boleto_url'             => $this->data['outputDetails']['boletobancario.url'],
                'boleto_barcode'         => $this->data['outputDetails']['boletobancario.barCodeReference'],
                'boleto_expiration_date' => $this->data['outputDetails']['boletobancario.expirationDate'],
            ];
        }

        return $data;
    }

    public function getOutputDetails()
    {
        if (!isset($this->data['outputDetails'])) {
            return null;
        }

        return $this->data['outputDetails'];
    }
}
