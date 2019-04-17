<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class CompletePurchaseRequestTest extends TestCase
{

    public function testChecksumValidationSuccess()
    {
        $string = '<?xml version="1.0" encoding="utf-8"?><SVSPurchaseStatusNotificationRequest><StatusReport><StatusDescription></StatusDescription><Status>4</Status><SVSTransaction><SVSCustomerAccount>1100185585</SVSCustomerAccount><ProcessingTime>2014-10-09 12:18:17</ProcessingTime><Result><Description></Description><Code></Code></Result><BatchNumber>6639037</BatchNumber><Id>1865010000008351204</Id></SVSTransaction><SVSCustomer><IP></IP><PostalCode>123456</PostalCode><Country>DE</Country><LastName>Test</LastName><FirstName>Soft Cube International Doo</FirstName></SVSCustomer></StatusReport><Request><MerchantFreeText>Deposit: 2257</MerchantFreeText><CustomerIdAtMerchant>100723</CustomerIdAtMerchant><MerchantAccountNumber>110355</MerchantAccountNumber><Currency>EUR</Currency><Amount>0.78</Amount><TxBatchNumber>0</TxBatchNumber><TxID>2257</TxID></Request><Authentication><Checksum>0e8622d9e32836e2e540245e81b22d64</Checksum></Authentication></SVSPurchaseStatusNotificationRequest>';
        $request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'testMode' => true
        ));

        $this->assertTrue($request->validateChecksum($string));
    }

    public function testChecksumValidationFailure()
    {
        $string = '<?xml version="1.0" encoding="utf-8"?><SVSPurchaseStatusNotificationRequest><StatusReport><StatusDescription></StatusDescription><Status>4</Status><SVSTransaction><SVSCustomerAccount>1100185585</SVSCustomerAccount><ProcessingTime>2014-10-09 12:18:17</ProcessingTime><Result><Description></Description><Code></Code></Result><BatchNumber>6639037</BatchNumber><Id>1865010000008351204</Id></SVSTransaction><SVSCustomer><IP></IP><PostalCode>123456</PostalCode><Country>DE</Country><LastName>Test</LastName><FirstName>Soft Cube International Doo</FirstName></SVSCustomer></StatusReport><Request><MerchantFreeText>Deposit: 2257</MerchantFreeText><CustomerIdAtMerchant>100723</CustomerIdAtMerchant><MerchantAccountNumber>110355</MerchantAccountNumber><Currency>EUR</Currency><Amount>0.78</Amount><TxBatchNumber>0</TxBatchNumber><TxID>2257</TxID></Request><Authentication><Checksum>0e8622d9e32836e2e540245e81b22d64</Checksum></Authentication></SVSPurchaseStatusNotificationRequest>';
        $request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDB',
            'testMode' => true
        ));

        $this->assertFalse($request->validateChecksum($string));
    }

    public function testNotificationGetData()
    {
        $httpRequest = new HttpRequest(array(), array('XML' => '<?xml version="1.0" encoding="utf-8"?><SVSPurchaseStatusNotificationRequest><StatusReport><StatusDescription></StatusDescription><Status>4</Status><SVSTransaction><SVSCustomerAccount>1100185585</SVSCustomerAccount><ProcessingTime>2014-10-09 12:18:17</ProcessingTime><Result><Description></Description><Code></Code></Result><BatchNumber>6639037</BatchNumber><Id>1865010000008351204</Id></SVSTransaction><SVSCustomer><IP></IP><PostalCode>123456</PostalCode><Country>DE</Country><LastName>Test</LastName><FirstName>Soft Cube International Doo</FirstName></SVSCustomer></StatusReport><Request><MerchantFreeText>Deposit: 2257</MerchantFreeText><CustomerIdAtMerchant>100723</CustomerIdAtMerchant><MerchantAccountNumber>110355</MerchantAccountNumber><Currency>EUR</Currency><Amount>0.78</Amount><TxBatchNumber>0</TxBatchNumber><TxID>2257</TxID></Request><Authentication><Checksum>0e8622d9e32836e2e540245e81b22d64</Checksum></Authentication></SVSPurchaseStatusNotificationRequest>'));
        $request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'testMode' => true
        ));

        $data = $request->getData();

        $this->assertSame('4', (string) $data->StatusReport->Status);
        $this->assertSame('0e8622d9e32836e2e540245e81b22d64', (string) $data->Authentication->Checksum);
    }

    public function testCallbackGetData()
    {
        $httpRequest = new HttpRequest(array(), array(), array(), array(), array(), array(), '<?xml version="1.0" encoding="utf-8"?><TransactionResult xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><ErrorCode>0</ErrorCode><Message>OK</Message><SvsTxID>1865010000008351204</SvsTxID><TransactionType>Purchase</TransactionType><ProcessingTime>20141009 12:18:17</ProcessingTime><Amount>78</Amount><Currency>EUR</Currency><MerchantAccountNumber>110355</MerchantAccountNumber><ClientAccountNumber>1100185585</ClientAccountNumber><TransactionDescription /><ClientTransactionID>2257</ClientTransactionID></TransactionResult>');
        $request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'testMode' => true
        ));

        $data = $request->getData();

        $this->assertSame('0', (string) $data->ErrorCode);
        $this->assertSame('OK', (string) $data->Message);
        $this->assertSame('1865010000008351204', (string) $data->SvsTxID);
        $this->assertSame('Purchase', (string) $data->TransactionType);
        $this->assertSame('20141009 12:18:17', (string) $data->ProcessingTime);
        $this->assertSame('78', (string) $data->Amount);
        $this->assertSame('EUR', (string) $data->Currency);
        $this->assertSame('110355', (string) $data->MerchantAccountNumber);
        $this->assertSame('1100185585', (string) $data->ClientAccountNumber);
        $this->assertSame('', (string) $data->TransactionDescription);
        $this->assertSame('2257', (string) $data->ClientTransactionID);
    }

    public function testNotificationResponse()
    {
        $httpRequest = new HttpRequest(array(), array('XML' => '<?xml version="1.0" encoding="utf-8"?><SVSPurchaseStatusNotificationRequest><StatusReport><StatusDescription></StatusDescription><Status>4</Status><SVSTransaction><SVSCustomerAccount>1100185585</SVSCustomerAccount><ProcessingTime>2014-10-09 12:18:17</ProcessingTime><Result><Description></Description><Code></Code></Result><BatchNumber>6639037</BatchNumber><Id>1865010000008351204</Id></SVSTransaction><SVSCustomer><IP></IP><PostalCode>123456</PostalCode><Country>DE</Country><LastName>Test</LastName><FirstName>Soft Cube International Doo</FirstName></SVSCustomer></StatusReport><Request><MerchantFreeText>Deposit: 2257</MerchantFreeText><CustomerIdAtMerchant>100723</CustomerIdAtMerchant><MerchantAccountNumber>110355</MerchantAccountNumber><Currency>EUR</Currency><Amount>0.78</Amount><TxBatchNumber>0</TxBatchNumber><TxID>2257</TxID></Request><Authentication><Checksum>f3141998202d7ebfee31c416d34a9691</Checksum></Authentication></SVSPurchaseStatusNotificationRequest>'));
        $request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $request->initialize(array(
            'merchantId' => '100',
            'merchantPassword' => 'Y23X05ZS4TDA',
            'testMode' => true
        ));

        $response = $request->createResponse('Confirmed', 0, 'Free Text Description');
        $xml = new \SimpleXMLElement($response);

        $this->assertSame('0', (string) $xml->TransactionResult->Code);
        $this->assertSame('Free Text Description', (string) $xml->TransactionResult->Description);
        $this->assertSame('Confirmed', (string) $xml->Status);
        $this->assertSame('7cb93657fb3efd32c4be855574ea87d8', (string) $xml->Authentication->Checksum);
    }

}
