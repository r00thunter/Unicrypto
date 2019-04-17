<?php
namespace Omnipay\Ecopayz\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Ecopayz Response
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.3 Ecopayz API Specification
 */
class FetchTransactionResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return boolean is successful
     */
    public function isSuccessful()
    {
        return $this->getCode() === 0;
    }

    /**
     * Get the response code.
     *
     * @return int code
     */
    public function getCode()
    {
        return (int) $this->data->ErrorCode;
    }

    /**
     * Get the response message.
     *
     * @return string message
     */
    public function getMessage()
    {
        return (string) $this->data->Message;
    }

    /**
     * Get the Merchant’s transaction ID.
     *
     * @return string transaction reference
     */
    public function getTransactionId()
    {
        return (string) $this->data->TxID;
    }

    /**
     * Get the unique ID that identifies the transaction in the Ecopayz system.
     *
     * @return string transaction reference
     */
    public function getTransactionReference()
    {
        return (string) $this->data->SVSTxID;
    }

    /**
     * Get the transaction type.
     *
     * @return string transaction type
     */
    public function getTransactionType()
    {
        return (string) $this->data->TransactionType;
    }

    /**
     * Get the transaction description.
     *
     * @return string transaction description
     */
    public function getTransactionDescription()
    {
        return (string) $this->data->TransactionDescription;
    }

    /**
     * Get the total amount of the transfer as it was requested.
     *
     * The amount will be greater than 0.01 units of currency in the supported currency
     * and may have decimal places, but no currency symbols.
     *
     * @return string amount
     */
    public function getAmount()
    {
        return (string) number_format($this->data->Amount / 100, 2, '.', '');
    }

    /**
     * Get the currency of the transfer as it was requested.
     *
     * @return string amount
     */
    public function getCurrency()
    {
        return (string) $this->data->Currency;
    }

    /**
     * Get the merchant account number.
     *
     * @return string merchant account number
     */
    public function getMerchantAccountNumber()
    {
        return (string) $this->data->MerchantAccountNumber;
    }

    /**
     * Get the client account number.
     *
     * @return string client account number
     */
    public function getClientAccountNumber()
    {
        return (string) $this->data->ClientAccountNumber;
    }

    /**
     * Get the processing time.
     *
     * @return string processing time
     */
    public function getProcessingTime()
    {
        return (string) $this->data->ProcessingTime;
    }
}
