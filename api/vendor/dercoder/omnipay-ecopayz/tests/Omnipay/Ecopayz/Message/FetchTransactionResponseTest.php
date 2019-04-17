<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;

class FetchTransactionResponseTest extends TestCase
{

    public function testFailure()
    {
        $httpResponse = $this->getMockHttpResponse('FetchTransactionFailure.txt');
        $xmlElement = new \SimpleXMLElement($httpResponse->getBody(true));
        $xmlResponse = $xmlElement
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $response = new FetchTransactionResponse($this->getMockRequest(), $xmlResponse->QueryByCustomerTransactionIDResponse->TransactionResponse);

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(60004, $response->getCode());
        $this->assertSame('Transaction not found', $response->getMessage());
        $this->assertSame('1234', $response->getTransactionId());
    }

    public function testSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('FetchTransactionSuccess.txt');
        $xmlElement = new \SimpleXMLElement($httpResponse->getBody(true));
        $xmlResponse = $xmlElement
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $response = new FetchTransactionResponse($this->getMockRequest(), $xmlResponse->QueryByCustomerTransactionIDResponse->TransactionResponse);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(0, $response->getCode());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame('2064', $response->getTransactionId());
        $this->assertSame('1865010000008316336', $response->getTransactionReference());
        $this->assertSame('7.91', $response->getAmount());
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('110355', $response->getMerchantAccountNumber());
        $this->assertSame('1100185585', $response->getClientAccountNumber());
        $this->assertSame('Payout', $response->getTransactionType());
        $this->assertSame('2014-10-02T13:33:25.587', $response->getProcessingTime());
    }

}
