<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;

class PayoutRequestTest extends TestCase
{
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new PayoutRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'merchantId' => '100',
            'merchantAccountNumber' => '100001',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'clientAccountNumber' => '1123456789',
            'clientAccountNumberAtMerchant' => '9876543210',
            'description' => 'Free Text Description',
            'transactionId' => 'TX4567890',
            'amount' => '12.34',
            'currency' => 'EUR'
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $xml = new \SimpleXMLElement($data);
        $request = $xml
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $this->assertSame('100', (string) $request->Payout->PayoutRequest->MerchantID);
        $this->assertSame('100001', (string) $request->Payout->PayoutRequest->MerchantAccountNumber);
        $this->assertSame('Y23X05ZS4TDA', (string) $request->Payout->PayoutRequest->MerchantPassword);
        $this->assertSame('1123456789', (string) $request->Payout->PayoutRequest->ClientAccountNumber);
        $this->assertSame('TX4567890', (string) $request->Payout->PayoutRequest->TxID);
        $this->assertSame('1234', (string) $request->Payout->PayoutRequest->Amount);
        $this->assertSame('EUR', (string) $request->Payout->PayoutRequest->Currency);
        $this->assertSame('9876543210', (string) $request->Payout->PayoutRequest->ClientAccountNumberAtMerchant);
        $this->assertSame('Free Text Description', (string) $request->Payout->PayoutRequest->TransactionDescription);
    }
}
