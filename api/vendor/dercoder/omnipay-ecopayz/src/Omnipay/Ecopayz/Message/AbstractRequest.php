<?php
namespace Omnipay\Ecopayz\Message;

/**
 * Ecopayz Abstract Request
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.3 Ecopayz API Specification
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * Get the Merchant ID
     *
     * This is the merchant number and is a four digit numeric value,
     * this is entered by EcoPayz staff at import time.
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
     * This is the merchant number and is a four digit numeric value,
     * this is entered by EcoPayz staff at import time.
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
     * The merchant’s ecoPayz account number,
     * which will be credited by the purchase transaction.
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
     * The merchant’s ecoPayz account number,
     * which will be credited by the purchase transaction.
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
     * Get calculated checksum
     *
     * The purpose of the checksum is to authenticate the communicating parties
     * and to ensure the integrity of the data they send each other.
     * The checksum is an MD5 hash, which is 128 bits or 16 bytes long.
     * The value is expressed as a string of hexadecimal digits in lowercase
     *
     * @param  array  $data data to calculate checksum
     * @return string checksum
     */
    protected function calculateArrayChecksum(array $data)
    {
        return md5(implode('', $data) . $this->getMerchantPassword());
    }

    /**
     * Get calculated checksum
     *
     * 1. Prepare the whole XML document. Store the Merchant password instead of a checksum.
     * 2. If the XML has been created as a DOM, serialize it into a string.
     * 3. Convert the string to an array of bytes, using the UTF8 encoding.
     * 4. Compute the MD5 hash of the byte array.
     * 5. Convert the hash to a string, using lowercase hexadecimal digits.
     * 6. Replace the Merchant password in XML’s element Checksum with the hash string.
     * 7. Send the response AS IS: do not re-format it in any way.
     *
     * @param  string $string xml string to calculate checksum
     * @return string checksum
     */
    protected function calculateXmlChecksum($string)
    {
        return md5(str_replace(array("\r\n", "\r", "\n"), '', trim($string)));
    }
}
