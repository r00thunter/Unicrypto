<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;

class FetchTransactionRequestTest extends TestCase
{
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetDataByTransactionId()
    {
        $this->request->initialize(array(
            'merchantId' => '100',
            'merchantAccountNumber' => '100001',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'transactionId' => 'TX4567890'
        ));

        $data = $this->request->getData();
        $xml = new \SimpleXMLElement($data);
        $request = $xml
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $this->assertSame('100', (string) $request->QueryByCustomerTransactionID->QueryByCustomerTransactionIDRequest->MerchantID);
        $this->assertSame('Y23X05ZS4TDA', (string) $request->QueryByCustomerTransactionID->QueryByCustomerTransactionIDRequest->MerchantPassword);
        $this->assertSame('TX4567890', (string) $request->QueryByCustomerTransactionID->QueryByCustomerTransactionIDRequest->TxID);
    }

    public function testGetDatabyTransactionReference()
    {
        $this->request->initialize(array(
            'merchantId' => '100',
            'merchantAccountNumber' => '100001',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'transactionReference' => '1865010000008350800'
        ));

        $data = $this->request->getData();
        $xml = new \SimpleXMLElement($data);
        $request = $xml
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $this->assertSame('100', (string) $request->QueryBySVSTransactionID->QueryBySVSTransactionIDRequest->MerchantID);
        $this->assertSame('Y23X05ZS4TDA', (string) $request->QueryBySVSTransactionID->QueryBySVSTransactionIDRequest->MerchantPassword);
        $this->assertSame('1865010000008350800', (string) $request->QueryBySVSTransactionID->QueryBySVSTransactionIDRequest->SVSTxID);
    }
}
