<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class CompletePurchaseResponseTest extends TestCase
{

    public function testFailure()
    {
        $httpRequest = new HttpRequest(array(), array(), array(), array(), array(), array(), '<?xml version="1.0" encoding="utf-8"?><TransactionResult xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><ErrorCode>11007</ErrorCode><Message>Merchant did not process the transaction and returned response "Error".</Message><SvsTxID>1865010000008351204</SvsTxID><TransactionType>Purchase</TransactionType><ProcessingTime>20141009 12:18:17</ProcessingTime><Amount>78</Amount><Currency>EUR</Currency><MerchantAccountNumber>110355</MerchantAccountNumber><ClientAccountNumber>1100185585</ClientAccountNumber><TransactionDescription /><ClientTransactionID>2257</ClientTransactionID></TransactionResult>');
        $request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'testMode' => true
        ));

        $response = $request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(11007, $response->getCode());
        $this->assertSame('Merchant did not process the transaction and returned response "Error".', $response->getMessage());
        $this->assertSame('2257', $response->getTransactionId());
        $this->assertSame('1865010000008351204', $response->getTransactionReference());
        $this->assertSame('0.78', $response->getAmount());
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('110355', $response->getMerchantAccountNumber());
        $this->assertSame('Purchase', $response->getTransactionType());
        $this->assertSame('20141009 12:18:17', $response->getProcessingTime());
    }

    public function testSuccess()
    {
        $httpRequest = new HttpRequest(array(), array(), array(), array(), array(), array(), '<?xml version="1.0" encoding="utf-8"?><TransactionResult xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><ErrorCode>0</ErrorCode><Message>OK</Message><SvsTxID>1865010000008351205</SvsTxID><TransactionType>Purchase</TransactionType><ProcessingTime>20141009 12:18:17</ProcessingTime><Amount>78</Amount><Currency>EUR</Currency><MerchantAccountNumber>110355</MerchantAccountNumber><ClientAccountNumber>1100185585</ClientAccountNumber><TransactionDescription /><ClientTransactionID>2258</ClientTransactionID></TransactionResult>');
        $request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'testMode' => true
        ));

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(0, $response->getCode());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame('2258', $response->getTransactionId());
        $this->assertSame('1865010000008351205', $response->getTransactionReference());
        $this->assertSame('0.78', $response->getAmount());
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('110355', $response->getMerchantAccountNumber());
        $this->assertSame('Purchase', $response->getTransactionType());
        $this->assertSame('20141009 12:18:17', $response->getProcessingTime());
    }

}
