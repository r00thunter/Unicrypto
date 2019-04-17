<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;

class PayoutResponseTest extends TestCase
{

    public function testFailure()
    {
        $httpResponse = $this->getMockHttpResponse('PayoutFailure.txt');
        $xmlElement = new \SimpleXMLElement($httpResponse->getBody(true));
        $xmlResponse = $xmlElement
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $response = new FetchTransactionResponse($this->getMockRequest(), $xmlResponse->PayoutResponse->TransactionResponse);

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(11007, $response->getCode());
        $this->assertSame('Not enough money for the withdrawal operation.', $response->getMessage());
        $this->assertSame('2252', $response->getTransactionId());
        $this->assertSame('1865010000008350800', $response->getTransactionReference());
        $this->assertSame('72.56', $response->getAmount());
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('110355', $response->getMerchantAccountNumber());
        $this->assertSame('Payout', $response->getTransactionType());
        $this->assertSame('2014-10-09T10:57:22.8547409+00:00', $response->getProcessingTime());
    }

    public function testSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PayoutSuccess.txt');
        $xmlElement = new \SimpleXMLElement($httpResponse->getBody(true));
        $xmlResponse = $xmlElement
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        $response = new FetchTransactionResponse($this->getMockRequest(), $xmlResponse->PayoutResponse->TransactionResponse);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(0, $response->getCode());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame('2251', $response->getTransactionId());
        $this->assertSame('1865010000008350760', $response->getTransactionReference());
        $this->assertSame('0.79', $response->getAmount());
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('110355', $response->getMerchantAccountNumber());
        $this->assertSame('1100185585', $response->getClientAccountNumber());
        $this->assertSame('Payout', $response->getTransactionType());
        $this->assertSame('2014-10-09T10:49:25.8609586+00:00', $response->getProcessingTime());
    }

}
