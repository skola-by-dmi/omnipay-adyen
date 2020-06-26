<?php

namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\AuthorizeRequest;
use Omnipay\Tests\GatewayTestCase;

class ClassicGatewayTest extends GatewayTestCase
{
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new ClassicGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(
            [
                'amount' => '10.00',
                'merchantAccount' => 'AdyenAccount',
                'reference' => 'UniqueID',
            ]
        );

        $this->assertInstanceOf(AuthorizeRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }
}
