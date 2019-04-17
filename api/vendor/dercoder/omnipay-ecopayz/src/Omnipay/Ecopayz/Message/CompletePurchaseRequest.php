<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Ecopayz Complete Purchase Request
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.3 Ecopayz API Specification
 */
class CompletePurchaseRequest extends FetchTransactionRequest
{
    private $trustedIps = array(
        '213.129.76.104', '213.129.76.105',
        '217.21.162.163', '217.21.162.164',
        '176.57.42.131', '176.57.42.132'
    );

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

        if (!$this->getTestMode()) {

            if (!in_array($this->httpRequest->getClientIp(), $this->trustedIps)) {
                throw new InvalidRequestException('IP Address is invalid to access this function');
            }

        }

        if ($xml = $this->httpRequest->request->get('XML')) {

            if (!$this->validateChecksum($xml)) {
                throw new InvalidRequestException('Invalid XML checksum');
            }

            return new \SimpleXMLElement($xml);

        } elseif ($xml = $this->httpRequest->getContent()) {
            return new \SimpleXMLElement($xml);

        } else {

            throw new InvalidRequestException('Missing XML');

        }
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed                    $data The data to send
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        if (isset($data->StatusReport)) {

            if (in_array($data->StatusReport->Status, array(1, 2, 3))) {
                $response = $this->createResponse('OK', 0, 'OK');
            } elseif (in_array($data->StatusReport->Status, array(4, 5))) {
                $response = $this->createResponse('Confirmed', 0, 'Confirmed');
            } else {
                $response = $this->createResponse('InvalidRequest', 99, 'Invalid StatusReport/Status');
            }

            header('Content-Type: text/xml; charset=utf-8');
            echo $response;
            die();

        } else {
            return new CompletePurchaseResponse($this, $data);
        }
    }

    /**
     * Respond to Ecopayz confirming or rejecting the payment.
     *
     * One of the following status codes:
     * - InvalidRequest
     * - OK
     * - Confirmed
     * - Cancelled
     *
     * @param  string $status           The ecopayz status code
     * @param  int    $errorCode        The merchant error code
     * @param  string $errorDescription The merchant error description
     * @return string response
     */
    public function createResponse($status, $errorCode, $errorDescription)
    {
        $document = new \DOMDocument('1.0', 'utf-8');
        $document->formatOutput = false;

        $response = $document->appendChild(
            $document->createElement('SVSPurchaseStatusNotificationResponse')
        );

        $result = $response->appendChild(
            $document->createElement('TransactionResult')
        );

        $result->appendChild(
            $document->createElement('Description', $errorDescription)
        );

        $result->appendChild(
            $document->createElement('Code', $errorCode)
        );

        $response->appendChild(
            $document->createElement('Status', $status)
        );

        $authentication = $response->appendChild(
            $document->createElement('Authentication')
        );

        $checksum = $authentication->appendChild(
            $document->createElement('Checksum', $this->getMerchantPassword())
        );

        $checksum->nodeValue = $this->calculateXmlChecksum($document->saveXML());

        return $document->saveXML();
    }

    /**
     * Validate Ecopayz XML message
     *
     * @param  string $string The xml string to validate
     * @return bool   result
     */
    public function validateChecksum($string)
    {
        $xml = new \SimpleXMLElement($string);
        $checksum = (string) $xml->Authentication->Checksum;
        $original = str_replace($checksum, $this->getMerchantPassword(), $string);

        return md5($original) == $checksum;
    }
}
