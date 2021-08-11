<?php

namespace WebDEV\QuickBooks\Payments\Entities;

use WebDEV\QuickBooks\Payments\Exceptions\CardOperationErrorException;
use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\Operations\ChargeOperations;
use Exception;

/**
 * Class Cards
 *
 * @package WebDEV\QuickBooks\Payments
 */
class CreditCard extends BaseEntity
{
    /**
     * Constructor
     *
     * @param PaymentClient $client
     */
    public function __construct(PaymentClient $client) {
        parent::__construct($client);
        return $this;
    }

    /**
     * Charges a credit card
     *
     * @param array $data
     * @return mixed
     * @throws CardOperationErrorException
     */
    public function charge(array $data) {
        $result = null;
        try {
            $charge = ChargeOperations::buildFrom($data);
            $response = $this->getClient()->charge($charge);
            if($response->failed()) {
                $error = $this->getError($response->getBody());
                throw new Exception($error);
            }

            $result = $response->getBody();
        } catch(Exception $ex) {
            throw new CardOperationErrorException($ex->getMessage());
        }

        return $result;
    }
}
