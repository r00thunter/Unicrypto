<?php
namespace Omnipay\Ecopayz\Message;

/**
 * Ecopayz Purchase Request
 *
 * When a client connects to the merchant's WEB store and chooses something to purchase,
 * the merchant's application needs to obtain the money amount of this purchase operation,
 * so it offers various payment methods to the client (for example, ecoPayz).
 * When the client chooses ecoPayz, the next step can be executed.
 *
 * @author Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.0.3 Ecopayz API Specification
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * Get the Customer ID at Merchant
     *
     * The client's account number at the merchant site.
     *
     * @return string customer id at merchant
     */
    public function getCustomerIdAtMerchant()
    {
        return $this->getParameter('customerIdAtMerchant');
    }

    /**
     * Set the Customer ID at Merchant
     *
     * The client's account number at the merchant site.
     *
     * @param  string $value customer id at merchant
     * @return self
     */
    public function setCustomerIdAtMerchant($value)
    {
        return $this->setParameter('customerIdAtMerchant', $value);
    }

    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        $this->validate(
            'merchantId',
            'merchantAccountNumber',
            'customerIdAtMerchant',
            'transactionId',
            'amount',
            'currency'
        );

        $data = array();
        $data['PaymentPageID'] = $this->getMerchantId();
        $data['MerchantAccountNumber'] = $this->getMerchantAccountNumber();
        $data['CustomerIdAtMerchant'] = $this->getCustomerIdAtMerchant();
        $data['TxID'] = $this->getTransactionId();
        $data['Amount'] = $this->getAmount();
        $data['Currency'] = $this->getCurrency();
        $data['MerchantFreeText'] = $this->getDescription();
        $data['Checksum'] = $this->calculateArrayChecksum($data);

        return $data;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed            $data The data to send
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        return new PurchaseResponse($this, $data);
    }
}
