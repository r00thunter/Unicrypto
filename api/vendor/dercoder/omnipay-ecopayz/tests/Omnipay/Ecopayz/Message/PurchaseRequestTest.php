<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'merchantId'                => '100',
            'merchantAccountNumber'     => '100001',
            'customerIdAtMerchant'      => '1123456789',
            'transactionId'             => 'TX4567890',
            'description'               => 'Free Text Description',
            'amount'                    => '12.34',
            'currency'                  => 'EUR'
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('100', $data['PaymentPageID']);
        $this->assertSame('100001', $data['MerchantAccountNumber']);
        $this->assertSame('1123456789', $data['CustomerIdAtMerchant']);
        $this->assertSame('TX4567890', $data['TxID']);
        $this->assertSame('12.34', $data['Amount']);
        $this->assertSame('EUR', $data['Currency']);
        $this->assertSame('Free Text Description', $data['MerchantFreeText']);
        $this->assertSame('84bbad2a636aa9226c03f17ff813a181', $data['Checksum']);
    }
}
