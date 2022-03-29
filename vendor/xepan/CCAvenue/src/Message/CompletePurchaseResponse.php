<?php

namespace Omnipay\CCAvenue\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * New Complete Purchase response
 */
class CompletePurchaseResponse extends AbstractResponse
{
  public function isSuccessful()
  {
    return isset($this->data) && 'Success' === $this->data['order_status'];
  }

  public function getOrderStatus(){
    return $this->data['order_status'];
  }

  function getTransactionStatus(){
    return $this->getOrderStatus();
  }

  public function getTransactionReference()
  {
    return isset($this->data['tracking_id']) ? $this->data['tracking_id'] : null;
  }

  public function getMessage()
  {
    return isset($this->data['message']) ? $this->data['message'] : null;
  }


  public function getData(){
    return $this->data;
  }

}
