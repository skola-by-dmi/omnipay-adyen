<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $livePaymentsEndpoint = 'https://%s-pal-live.adyenpayments.com/pal/servlet/Payment/v52';
    protected $testPaymentsEndpoint = 'https://pal-test.adyen.com/pal/servlet/Payment/v52';
    protected $liveCheckoutEndpoint = 'https://%s-checkout-live.adyenpayments.com/checkout/v52';
    protected $testCheckoutEndpoint = 'https://checkout-test.adyen.com/v52';

    /**
     * Get the gateway  Key.
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
     * Set  key
     *
     * @param  string $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }

    /**
     * Get the gateway Live URL Prefix.
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
     * @return AbstractRequest provides a fluent interface.
     */
    public function setLiveUrlPrefix($value)
    {
        return $this->setParameter('liveUrlPrefix', $value);
    }

    /**
     * Get the endpoint where the request should be made.
     *
     * @return string the URL of the endpoint
     */
    public function getEndpoint()
    {
        // The Adyen's API provides different endpoints
        // for payments (Checkout API and Payments API).
        // Currently, the boleto is only available on
        // Checkout API.
        if ($this->getPaymentMethod() === 'boleto' && $this instanceof AuthorizeRequest) {
            return $this->getTestMode() ? $this->testCheckoutEndpoint : sprintf($this->liveCheckoutEndpoint, $this->getLiveUrlPrefix());
        }

        return $this->getTestMode() ? $this->testPaymentsEndpoint : sprintf($this->livePaymentsEndpoint, $this->getLiveUrlPrefix());
    }

    /**
     * Get the boleto due date
     *
     * @return string boleto due date
     */
    public function getBoletoDueDate($format = 'd/m/Y')
    {
        $value = $this->getParameter('boletoDueDate');

        return $value ? $value->format($format) : null;
    }

    /**
     * Set the boleto due date
     *
     * @param  string $value defaults to atual date + 30 days
     * @return AbstractRequest
     */
    public function setBoletoDueDate($value)
    {
        if ($value) {
            $value = new \DateTime($value, new \DateTimeZone('UTC'));
            $value = new \DateTime($value->format('Y-m-d\T03:00:00'), new \DateTimeZone('UTC'));
        } else {
            $value = null;
        }

        return $this->setParameter('boletoDueDate', $value);
    }

    /**
     * Get Document number (CPF or CNPJ).
     *
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->getParameter('documentNumber');
    }

    /**
     * Set Document Number (CPF or CNPJ)
     *
     * Non-numeric characters are stripped out of the document number, so
     * it's safe to pass in strings such as "224.158.178-40" etc.
     *
     * @param  string $value Parameter value
     * @return AbstractRequest
     */
    public function setDocumentNumber($value)
    {
        // strip non-numeric characters
        return $this->setParameter('documentNumber', preg_replace('/\D/', '', $value));
    }

    /**
     * Get the type of person that is making the payment
     * This allow to a payment be made by a company
     *
     * @return string
     */
    public function getPersonType()
    {
        return $this->getParameter('personType') ?: 'personal';
    }

    /**
     * Set Person Type
     *
     * @param  string $value Person type value
     * @return AbstractRequest
     */
    public function setPersonType($value)
    {
        return $this->setParameter('personType', $value);
    }

    /**
     * Get the company name that is making the payment
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->getParameter('companyName');
    }

    /**
     * Set Company Name
     *
     * @param  string $value Company name value
     * @return AbstractRequest
     */
    public function setCompanyName($value)
    {
        return $this->setParameter('companyName', $value);
    }

    /**
     * Get the split param
     *
     * @return array
     */
    public function getSplit()
    {
        return $this->getParameter('split') ?: [];
    }

    /**
     * Set Split
     *
     * @param  array $value Array containing the required fields to split the payment
     * @return AbstractRequest
     */
    public function setSplit($value = [])
    {
        return $this->setParameter('split', $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string the HTTP method
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $response = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $this->getHeaders(),
            json_encode($data)
        );

        $payload =  json_decode($response->getBody()->getContents(), true);

        return $this->createResponse($payload);
    }

    public function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    public function getHeaders()
    {
        return [
            'x-API-key' => $this->getParameter('key'),
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * A note about the payment.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->getParameter('note');
    }

    /**
     * Set a note about the payment. The value of this parameter
     * will be shown along with payment details.
     *
     * @param  string $value
     * @return AbstractRequest
     */
    public function setNote($value)
    {
        return $this->setParameter('note', $value);
    }

    /**
     * Get the base data.
     *
     * Because the Adyen gateway requires a common of fields for every request
     * this function can be called to this common data in the format that the
     * API requires.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        $data                    = [];
        $data['merchantAccount'] = $this->getMerchantAccount();

        return $data;
    }

    public function setMerchantAccount($value)
    {
        return $this->setParameter('merchantAccount', $value);
    }

    public function getMerchantAccount()
    {
        return $this->getParameter('merchantAccount');
    }

    /**
     * Get the customer data.
     *
     * Because the Adyen gateway uses a common format for passing
     * customer data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    public function getCustomerData()
    {
        $this->validate('card');
        $card    = $this->getCard();

        $data                    = [];
        $data['shopperName']     = $card->getName();
        $data['shopperEmail']    = $card->getEmail();
        $data['telephoneNumber'] = $card->getPhone();

        return $data;
    }

    /**
     * Get the card data.
     *
     * Because the Adyen gateway uses a common format for passing
     * card data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    public function getCardData()
    {
        $card                 = $this->getCard();
        $data                 = [];
        $data['installments'] = $this->getInstallments();

        $card->validate();
        $data['card']['holderName']  = $card->getName();
        $data['card']['number']      = $card->getNumber();
        $data['card']['expiryMonth'] = $card->getExpiryMonth();
        $data['card']['expiryYear']  = $card->getExpiryYear();
        if ($card->getCvv()) {
            $data['card']['cvc']  = $card->getCvv();
        }

        return $data;
    }

    /**
     * Get the boleto data.
     *
     * Because the Adyen gateway uses a common format for passing
     * boleto data to the API, this function can be called to get the
     * data from the associated request object in the format that the
     * API requires.
     *
     * @return array
     */
    public function getBoletoData()
    {
        $this->validate('card', 'transactionId', 'boletoDueDate');
        $card    = $this->getCard();

        $defaultParams = $this->getDefaultParameters();
        $data['reference'] = $this->getTransactionId();
        $data['shopperName'] = [
            'firstName' => $card->getFirstName(),
            'lastName' => $card->getLastName(),
        ];
        $data['shopperEmail']    = $card->getEmail();
        $data['socialSecurityNumber'] = $this->getDocumentNumber();
        $address = array_map('trim', explode(',', $card->getAddress1()));
        $data['billingAddress']['street']            = $address[0];
        $data['billingAddress']['houseNumberOrName'] = isset($address[1]) ? $address[1] : '';
        $data['billingAddress']['stateOrProvince']   = $card->getState();
        $data['billingAddress']['city']              = $card->getCity();
        $data['billingAddress']['country']           = $card->getCountry();
        $data['billingAddress']['postalCode']        = $this->onlyDigits($card->getPostcode());

        $data['paymentMethod'] = ['type' => 'boletobancario_santander'];
        if ($paymentNote = $this->getNote()) {
            $data['shopperStatement'] = $paymentNote;
        }
        $data['deliveryDate'] = $this->getBoletoDueDate('Y-m-d\TH:i:s\Z');

        return array_merge($data, $defaultParams);
    }

    /**
     * Get the address data.
     *
     * Because the Adyen gateway uses a common format for passing
     * address data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    public function getAddressData()
    {
        $card    = $this->getCard();
        $address = array_map('trim', explode(',', $card->getAddress1()));

        $data                  = [];
        $data['billingAddress']['street']            = $address[0];
        $data['billingAddress']['houseNumberOrName'] = isset($address[1]) ? $address[1] : '';
        $data['billingAddress']['city']              = $card->getCity();
        $data['billingAddress']['country']           = $card->getCountry();
        $data['billingAddress']['postalCode']        = $this->onlyDigits($card->getPostcode());

        return $data;
    }

    /**
     * Get the split data.
     *
     * Because the Adyen gateway uses a common format for passing
     * split payment data to the API, this function can be called to get the
     * data from the associated request in the format that the
     * API requires.
     *
     * @return array
     */
    public function getSplitData()
    {
        $split = $this->getSplit();
        return !empty($split) ? ['splits' => $split] : [];
    }

    /**
     * Get the payment data.
     *
     * Because the Adyen gateway uses a common format for passing
     * payment data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    public function getPaymentData($aditionalPaymentData = [])
    {
        $this->validate('transactionId');

        $defaultParams = $this->getDefaultParameters();
        $customerData  = $this->getCustomerData();
        $addressData   = $this->getAddressData();
        $splitData     = $this->getSplitData();

        $paymentData              = [];
        $paymentData['reference'] = $this->getTransactionId();

        if ($notifyUrl = $this->getNotifyUrl()) {
            $paymentData['notificationURL'] = $notifyUrl;
        }

        return array_merge(
            $defaultParams,
            $customerData,
            $addressData,
            $paymentData,
            $splitData,
            $aditionalPaymentData
        );
    }

    /**
     * Get installments.
     *
     * @return integer the number of installments
     */
    public function getInstallments()
    {
        return ['value' => $this->getParameter('installments') ?: 1];
    }

    /**
     * Set Installments.
     *
     * The number must be between 1 and 12.
     * If the payment method is boleto defaults to 1.
     *
     * @param  integer $value
     * @return AuthorizeRequest provides a fluent interface.
     */
    public function setInstallments($value)
    {
        return $this->setParameter('installments', (int) $value);
    }

    private function onlyDigits($value)
    {
        return preg_replace("/[^0-9]/","", $value);
    }
}
