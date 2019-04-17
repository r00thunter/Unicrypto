<?php
namespace Omnipay\Ecopayz;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('1765');
        $this->gateway->setMerchantAccountNumber('120345');
        $this->gateway->setMerchantPassword('Y23X05ZS4tsA');
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array(
            'customerIdAtMerchant' => '1123456789',
            'transactionId' => 'TX9997888',
            'amount' => '14.65',
            'currency' => 'EUR'
        ));

        $this->assertSame('1123456789', $request->getCustomerIdAtMerchant());
        $this->assertSame('TX9997888', $request->getTransactionId());
        $this->assertSame('14.65', $request->getAmount());
        $this->assertSame('EUR', $request->getCurrency());
    }

    public function testPayout()
    {
        $request = $this->gateway->payout(array(
            'clientAccountNumber' => '9912345678',
            'transactionId' => 'TX8889777',
            'amount' => '12.43',
            'currency' => 'EUR'
        ));

        $this->assertSame('9912345678', $request->getClientAccountNumber());
        $this->assertSame('TX8889777', $request->getTransactionId());
        $this->assertSame('12.43', $request->getAmount());
        $this->assertSame('EUR', $request->getCurrency());
    }

    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction(array(
            'transactionId' => 'TX5557666',
            'transactionReference' => 'XXAACCD3231232'
        ));

        $this->assertSame('TX5557666', $request->getTransactionId());
        $this->assertSame('XXAACCD3231232', $request->getTransactionReference());
    }

}
