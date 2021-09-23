<?php

namespace Omnipay\CCAvenue\Message;

/**
 * CCAvenue Authorize Request
 */
class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {

        $this->validate('currency', 'amount');
        $data = $this->getBaseData();

        $data['signed_date_time'] = gmdate("Y-m-d\TH:i:s\Z");
        $data['unsigned_field_names'] = 'card_type,card_number,card_expiry_date';
        $data['signed_field_names'] = implode(',', array_keys($data)) . ',signed_field_names';
        $data['signature'] = $this->signData($data);
        return $data;
    }

    public function signData($data)
    {
        return base64_encode(hash_hmac('sha256', $this->buildDataToSign($data), $this->getSecretKey(), true));
    }

    public function buildDataToSign($data)
    {
        $signedFieldNames = explode(",", $data["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
            $dataToSign[] = $field . "=" . $data[$field];
        }
        return implode(",", $dataToSign);
    }

    public function getRequiredFields() {
        $extraFields = array();
        return array_merge(array(
            'amount',
            'city',
            'country',
            'address1',
            'email',
            'firstName',
            'lastName',
            'currency',
        ), $extraFields);
    }

    public function getRequiredFieldsUsAndCanada() {
        return array(
            'postcode',
            'billingState',
        );
    }

    public function getTransactionData()
    {
        return array(
            'reference_number' => $this->getTransactionId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'payment_method' => $this->getPaymentMethod(),
            'bill_to_forename' => $this->getCard()->getFirstName(),
            'bill_to_surname' => $this->getCard()->getLastName(),
            'bill_to_email' => $this->getCard()->getEmail(),
            'bill_to_phone' => $this->getCard()->getBillingPhone(),
            'bill_to_address_line1' => $this->getCard()->getAddress1(),
            'bill_to_address_line2' => $this->getCard()->getAddress2(),
            'bill_to_address_city' => $this->getCard()->getCity(),
            'bill_to_address_state' => $this->getCard()->getBillingState(),
            'bill_to_address_country' => strtoupper($this->getCard()->getCountry()),
            'bill_to_address_postal_code' => $this->getCard()->getPostcode(),
            'bill_to_company_name' => $this->getCard()->getCompany(),
        );
    }

    /**
     * @return array
     */
    public function getBaseData() {
        return array(
            'access_key' => $this->getAccessKey(),
            'profile_id' => $this->getProfileId(),
            'working_key' => $this->getSecretKey(),
            'locale' => 'en',
            'transaction_uuid' => $this->getUniqueID(),
            'transaction_type' => $this->getTransactionType(),
            'merchant_id' => $this->getProfileId(),
            'card_number' => $this->getCard()->getNumber(),
            'card_name' => $this->getCard()->getBillingName(),
            'card_type' => $this->getPaymentType(),
            'payment_type' => 'OPT' . $this->getPaymentType(),
            'expiry_month' => $this->getCard()->getExpiryMonth(),
            'expiry_year' => $this->getCard()->getExpiryYear(),
            'cvv_number' => $this->getCard()->getCvv(),
            'tid' => $this->parameters->get('transactionId'),
            'order_id' => $this->parameters->get('orderId') ?? $this->getUniqueID(),
            'amount' => $this->parameters->get('amount'),
            'currency' => $this->parameters->get('currency'),
            'redirect_url' => $this->parameters->get('returnUrl'),
            'cancel_url' => $this->parameters->get('cancelUrl'),
            'language'=> $this->parameters->get('language'),
            'billing_name'=> $this->getCard()->getBillingName(),
            'billing_address'=> $this->getCard()->getAddress1() . '  ' . $this->getCard()->getAddress2(),
            'billing_city'=> $this->getCard()->getCity(),
            'billing_state'=> $this->getCard()->getBillingState(),
            'billing_zip'=> $this->getCard()->getPostcode(),
            'billing_country'=> $this->getCard()->getCountry(),
            'billing_email'=> $this->getCard()->getEmail(),
            'billing_tel'=> $this->getCard()->getBillingPhone(),
            'delivery_name'=> $this->getCard()->getShippingName(),
            'delivery_address'=> $this->getCard()->getShippingAddress1() . '  ' . $this->getCard()->getShippingAddress2(),
            'delivery_city'=> $this->getCard()->getShippingCity(),
            'delivery_state'=> $this->parameters->get('delivery_state'),
            'delivery_zip'=> $this->parameters->get('delivery_zip'),
            'delivery_country'=> $this->parameters->get('delivery_country'),
            'delivery_tel'=> $this->parameters->get('delivery_tel'),
            'delivery_email'=> $this->parameters->get('delivery_email'),
            'issuing_bank' => $this->getIssuer(),
        );
    }

    /**
     * @return string
     */
    public function getUniqueID()
    {
        return uniqid();
    }

    public function getEndpoint()
    {
        return parent::getEndpoint();
    }

    public function getPaymentMethod()
    {
        return 'card';
    }

    public function getTransactionType()
    {
        return 'authorization';
    }

    /**
     * Get the order ID.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * Sets the order ID.
     *
     * @param string $value
     *
     * @return $this Provides a fluent interface
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

}
