<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Ecopayz Payout Request
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.3 Ecopayz API Specification
 */
class PayoutRequest extends AbstractRequest
{
    protected $endpoint = 'https://secure.ecopayz.com/services/MerchantAPI/MerchantAPIService.asmx';

    /**
     * Get the Client Account Number
     *
     * Client account number (who receive funds) in the ecoPayz system.
     *
     * @return string client account number
     */
    public function getClientAccountNumber()
    {
        return $this->getParameter('clientAccountNumber');
    }

    /**
     * Set the Client Account Number
     *
     * Client account number (who receive funds) in the ecoPayz system.
     *
     * @param  string $value client account number
     * @return self
     */
    public function setClientAccountNumber($value)
    {
        return $this->setParameter('clientAccountNumber', $value);
    }

    /**
     * Get the Client Account Number at Merchant
     *
     * Client account number at merchant.
     *
     * @return string client account number at merchant
     */
    public function getClientAccountNumberAtMerchant()
    {
        return $this->getParameter('clientAccountNumberAtMerchant');
    }

    /**
     * Set the Client Account Number at Merchant
     *
     * Client account number at merchant.
     *
     * @param  string $value client account number at merchant
     * @return self
     */
    public function setClientAccountNumberAtMerchant($value)
    {
        return $this->setParameter('clientAccountNumberAtMerchant', $value);
    }

    /**
     * Get the data for this request.
     *
     * @return \DOMDocument request data
     */
    public function getData()
    {
        $this->validate(
            'merchantId',
            'merchantPassword',
            'merchantAccountNumber',
            'clientAccountNumber',
            'transactionId',
            'amount',
            'currency'
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

        $payout = $body->appendChild(
            $document->createElement('q0:Payout')
        );

        $request = $payout->appendChild(
            $document->createElement('q0:PayoutRequest')
        );

        $request->appendChild(
            $document->createElement('q0:MerchantID', $this->getMerchantId())
        );

        $request->appendChild(
            $document->createElement('q0:MerchantPassword', $this->getMerchantPassword())
        );

        $request->appendChild(
            $document->createElement('q0:MerchantAccountNumber', $this->getMerchantAccountNumber())
        );

        $request->appendChild(
            $document->createElement('q0:ClientAccountNumber', $this->getClientAccountNumber())
        );

        $request->appendChild(
            $document->createElement('q0:Amount', $this->getAmountInteger())
        );

        $request->appendChild(
            $document->createElement('q0:TxID', $this->getTransactionId())
        );

        $request->appendChild(
            $document->createElement('q0:Currency', strtoupper($this->getCurrency()))
        );

        if ($clientAccountNumberAtMerchant = $this->getClientAccountNumberAtMerchant()) {
            $request->appendChild(
                $document->createElement('q0:ClientAccountNumberAtMerchant', $clientAccountNumberAtMerchant)
            );
        }

        if ($description = $this->getDescription()) {
            $request->appendChild(
                $document->createElement('q0:TransactionDescription', $description)
            );
        }

        return $document->saveXML();
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed                    $data The data to send
     * @throws InvalidResponseException
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        $headers = array(
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => 'http://www.ecocard.com/merchantAPI/Payout'
        );

        $httpRequest = $this->httpClient->createRequest('POST', $this->endpoint, $headers, $data);
        $httpResponse = $httpRequest->send();
        $xmlResponse = $httpResponse->xml()
            ->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->children('http://www.ecocard.com/merchantAPI/');

        if (!isset($xmlResponse->PayoutResponse)) {
            throw new InvalidResponseException('Missing element in XML response');
        }

        if (!isset($xmlResponse->PayoutResponse->TransactionResponse)) {
            throw new InvalidResponseException('Missing element in XML response');
        }

        return new FetchTransactionResponse($this, $xmlResponse->PayoutResponse->TransactionResponse);
    }
}
