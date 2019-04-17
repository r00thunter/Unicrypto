<?php
namespace Omnipay\Ecopayz;

use Omnipay\Common\AbstractGateway;

/**
 * Ecopayz Gateway
 *
 * @author Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class Gateway extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Ecopayz';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'merchantPassword' => '',
            'merchantAccountNumber' => '',
            'testMode'  => false
        );
    }

    /**
     * Get the Merchant ID
     *
     * This is the merchant number and is a four digit numeric value, this is entered by EcoPayz staff at import time.
     * If this is known this can be entered on submission
     *
     * @return string merchant id
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * Set the Merchant ID
     *
     * This is the merchant number and is a four digit numeric value, this is entered by EcoPayz staff at import time.
     * If this is known this can be entered on submission
     *
     * @param  string $value merchant id
     * @return self
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * Get the Merchant Password
     *
     * Password provided by ecoPayz.
     * Please note: Merchant password is different parameter than PP password.
     *
     * @return string merchant password
     */
    public function getMerchantPassword()
    {
        return $this->getParameter('merchantPassword');
    }

    /**
     * Set the Merchant Password
     *
     * Password provided by ecoPayz.
     * Please note: Merchant password is different parameter than PP password.
     *
     * @param  string $value merchant password
     * @return self
     */
    public function setMerchantPassword($value)
    {
        return $this->setParameter('merchantPassword', $value);
    }

    /**
     * Get the Merchant Account Number
     *
     * The merchant’s ecoPayz account number, which will be credited by the purchase transaction.
     * The number is provided by ecoPayz.
     *
     * @return string merchant account number
     */
    public function getMerchantAccountNumber()
    {
        return $this->getParameter('merchantAccountNumber');
    }

    /**
     * Set the Merchant Account Number
     *
     * The merchant’s ecoPayz account number, which will be credited by the purchase transaction.
     * The number is provided by ecoPayz.
     *
     * @param  string $value merchant account number
     * @return self
     */
    public function setMerchantAccountNumber($value)
    {
        return $this->setParameter('merchantAccountNumber', $value);
    }

    /**
     * @param  array                                    $parameters
     * @return \Omnipay\Ecopayz\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ecopayz\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param  array                                            $parameters
     * @return \Omnipay\Ecopayz\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ecopayz\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param  array                                  $parameters
     * @return \Omnipay\Ecopayz\Message\PayoutRequest
     */
    public function payout(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ecopayz\Message\PayoutRequest', $parameters);
    }

    /**
     * @param  array                                            $parameters
     * @return \Omnipay\Ecopayz\Message\FetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ecopayz\Message\FetchTransactionRequest', $parameters);
    }
}
