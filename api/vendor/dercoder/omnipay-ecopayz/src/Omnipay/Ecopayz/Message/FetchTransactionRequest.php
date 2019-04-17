<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Ecopayz Fetch Transaction Request
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.3 Ecopayz API Specification
 */
class FetchTransactionRequest extends AbstractRequest
{
    protected $endpoint = 'https://secure.ecopayz.com/services/MerchantAPI/MerchantAPIService.asmx';

    /**
     * Get the data for this request.
     *
     * @throws InvalidRequestException
     * @return string                  request data
     */
    public function getData()
    {
        $this->validate(
            'merchantId',
            'merchantPassword'
        );

        $document = new \DOMDocument('1.0', 'utf-8');
        $document->formatOutput = false;

        $envelope = $document->appendChild(
            $document->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope')
        );

        $envelope->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $envelope->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $envelope->setAttribute('xmlns:q0', 'http://www.ecocard.com/merchantAPI/');

        $body = $envelope->appendChild(
            $document->createElement('soapenv:Body')
        );

        if ($transactionReference = $this->getTransactionReference()) {

            $query = $body->appendChild(
                $document->createElement('q0:QueryBySVSTransactionID')
            );

            $request = $query->appendChild(
                $document->createElement('q0:QueryBySVSTransactionIDRequest')
            );

            $request->appendChild(
                $document->createElement('q0:MerchantID', $this->getMerchantId())
            );

            $request->appendChild(
                $document->createElement('q0:MerchantPassword', $this->getMerchantPassword())
            );

            $request->appendChild(
                $document->createElement('q0:SVSTxID', $transactionReference)
            );

        } elseif ($transactionId = $this->getTransactionId()) {

            $query = $body->appendChild(
                $document->createElement('q0:QueryByCustomerTransactionID')
            );

            $request = $query->appendChild(
                $document->createElement('q0:QueryByCustomerTransactionIDRequest')
            );

            $request->appendChild(
                $document->createElement('q0:MerchantID', $this->getMerchantId())
            );

            $request->appendChild(
                $document->createElement('q0:MerchantPassword', $this->getMerchantPassword())
            );

            $request->appendChild(
                $document->createElement('q0:TxID', $transactionId)
            );

        } else {

            throw new InvalidRequestException('The transactionId or transactionReference parameter is required');

        }

        return $document->saveXML();
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed                    $data The data to send
     * @throws InvalidResponseException
     * @throws InvalidRequestException
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        if (strpos($data, 'QueryBySVSTransactionID') !== false) {

            $headers = array(
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://www.ecocard.com/merchantAPI/QueryBySVSTransactionID'
            );

            $httpRequest = $this->httpClient->createRequest('POST', $this->endpoint, $headers, $data);
            $httpResponse = $httpRequest->send();
            $xmlResponse = $httpResponse->xml()
                ->children('http://schemas.xmlsoap.org/soap/envelope/')
                ->children('http://www.ecocard.com/merchantAPI/');

            if (!isset($xmlResponse->QueryBySVSTransactionIDResponse)) {
                throw new InvalidResponseException('Missing element in XML response');
            }

            if (!isset($xmlResponse->QueryBySVSTransactionIDResponse->TransactionResponse)) {
                throw new InvalidResponseException('Missing element in XML response');
            }

            return new FetchTransactionResponse(
                $this,
                $xmlResponse
                    ->QueryBySVSTransactionIDResponse
                    ->TransactionResponse
            );

        } elseif (strpos($data, 'QueryByCustomerTransactionID') !== false) {

            $headers = array(
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://www.ecocard.com/merchantAPI/QueryByCustomerTransactionID'
            );

            $httpRequest = $this->httpClient->createRequest('POST', $this->endpoint, $headers, $data);
            $httpResponse = $httpRequest->send();
            $xmlResponse = $httpResponse->xml()
                ->children('http://schemas.xmlsoap.org/soap/envelope/')
                ->children('http://www.ecocard.com/merchantAPI/');

            if (!isset($xmlResponse->QueryByCustomerTransactionIDResponse)) {
                throw new InvalidResponseException('Missing element in XML response');
            }

            if (!isset($xmlResponse->QueryByCustomerTransactionIDResponse->TransactionResponse)) {
                throw new InvalidResponseException('Missing element in XML response');
            }

            return new FetchTransactionResponse(
                $this,
                $xmlResponse
                    ->QueryByCustomerTransactionIDResponse
                    ->TransactionResponse
            );

        } else {

            throw new InvalidRequestException('The transactionId or transactionReference parameter is required');

        }
    }
}
