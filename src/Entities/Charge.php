<?php

namespace WebDEV\QuickBooks\Payments\Entities;

use WebDEV\QuickBooks\Payments\Exceptions\CardOperationErrorException;
use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\Operations\ChargeOperations;
use QuickBooksOnline\Payments\Operations\CardOperations;
use Exception;

/**
 * Class Cards
 *
 * @package WebDEV\QuickBooks\Payments
 */
class Charge extends BaseEntity
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
     * Create new charge with token
     *
     * @param array $data
     * @return mixed
     * @throws CardOperationErrorException
     */
    public function create(array $data) {
        $result = null;
        try {
            // create transaction token
            $card = CardOperations::buildFrom($data['card']);
            $tokenResponse = $this->getClient()->createToken($card);
            if($tokenResponse->failed()) {
                $error = $this->getError($tokenResponse->getBody());
                throw new Exception($error);
            }
            $token = $tokenResponse->getBody();

            // create the authorizaton
            $chargeData = [
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'capture' => false,
                'token' => $token->value,
                'context' => $data['context']
            ];
            $charge = ChargeOperations::buildFrom($chargeData);
            $chargeResponse = $this->getClient()->charge($charge);
            if($chargeResponse->failed()) {
                $error = $this->getError($chargeResponse->getBody());
                throw new Exception($error);
            }
            $authorization = $chargeResponse->getBody();
            if($authorization->status == 'DECLINED') {
                throw new Exception('The card authorization is declined, is rejected by PTS, or returned by the process.');
            }

            // capture the charge
            $captureChargeData = [
                'amount' => $data['amount'],
                'context' => $data['context']
            ];
            $captureCharge = ChargeOperations::buildFrom($captureChargeData);
            $captureResponse = $this->getClient()->captureCharge($captureCharge, $authorization->id);
            if($captureResponse->failed()) {
                $error = $this->getError($captureResponse->getBody());
                throw new Exception($error);
            }
            $result = $captureResponse->getBody();

        } catch(Exception $ex) {
            throw new CardOperationErrorException($ex->getMessage());
        }

        return $result;
    }

    /**
     * Create new charge without token
     *
     * @param array $data
     * @return mixed
     * @throws CardOperationErrorException
     */
    public function createWithoutToken(array $data) {
        $result = null;
        try {
            $charge = ChargeOperations::buildFrom($data);
            $response = $this->getClient()->charge($charge);
            if($response->failed()) {
                $error = $this->getError($response->getBody());
                throw new Exception($error);
            }
            $result = $response->getBody();
            if($result->status == 'DECLINED') {
                throw new Exception('The card authorization is declined, is rejected by PTS, or returned by the process.');
            }
        } catch(Exception $ex) {
            throw new CardOperationErrorException($ex->getMessage());
        }

        return $result;
    }
}
