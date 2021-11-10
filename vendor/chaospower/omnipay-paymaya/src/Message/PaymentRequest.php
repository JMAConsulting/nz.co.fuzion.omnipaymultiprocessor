<?php

namespace Omnipay\PayMaya\Message;

class PaymentRequest extends Request
{
    const API = '/payments';

    protected $parameters;

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = [
          'paymentTokenId' => $this->getVar('paymentTokenId'),
          'total_amount' => [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
          ],
          'buyer' => [
            'firstName' => $this->getCard()->getFirstName(),
            'lastName' => $this->getCard()->getLastName(),
            'contact' => [
              'email' => $this->getCard()->getEmail(),
            ],
          ],
          'shippingAddress' => [
            'firstName' => $this->getCard()->getFirstName(),
            'lastName' => $this->getCard()->getLastName(),
            'email' => $this->getCard()->getEmail(),
            'line1' => $this->getCard()->getBillingAddress1(),
            'line2' => $this->getCard()->getBillingAddress2(),
            'city' => $this->getCard()->getBillingCity(),
            'zipCode' => $this->getCard()->getBillingPostcode(),
            'countryCode' => $this->getCard()->getCountry(),
            'shippingType' => 'ST',
          ],
          'billingAddress' => [
            'line1' => $this->getCard()->getBillingAddress1(),
            'line2' => $this->getCard()->getBillingAddress2(),
            'city', => $this->getCard()->getBillingCity(),
            'state' => $this->getCard()->getBillingState(),
            'zipcode' => $this->getCard()->getBillingPostcode(),
            'countryCode' => $this->getCard()->getCountry(),
          ],
          'redirectUrl' => [
            'success' => $this->parameters->get('returnUrl'),
            'cancel' => $this->parameters->get('cancelUrl'),
          ],

        ];

        return $data;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $headers = [
            'Authorization' => 'Basic ' . $this->getParameter('secretApiKey'),
            'Content-Type'  => 'application/json',
        ];

        $request  = $this->createClientRequest($data, $headers);
        $response = $request->send();

        $this->response = new PaymentResponse($this, $response->json());

        return $this->response;
    }

    public function createClientRequest($data, array $headers = null)
    {
        $config      = $this->httpClient->getConfig();
        $curlOptions = $config->get('curl.options');
        $config->set('curl.options', $curlOptions);
        $this->httpClient->setConfig($config);

        $this->httpClient->getEventDispatcher()->addListener('request.error', function ($event) {
            if ($event['response']->isClientError()) {
                $event->stopPropagation();
            }
        });

        $httpRequest = $this->httpClient->createRequest(
            'POST',
            'https://pg-sandbox.paymaya.com/payments/v1' . API,
            $headers,
            $data
        );

        return $httpRequest;
    }
}
