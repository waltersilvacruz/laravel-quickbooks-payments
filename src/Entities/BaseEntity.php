<?php

namespace WebDEV\QuickBooks\Payments\Entities;

use QuickBooksOnline\Payments\PaymentClient;

/**
 * Class BaseEntity
 */
class BaseEntity
{
    /**
     * Payment Client object
     *
     * @var PaymentClient
     */
    protected $client;

    /**
     * @return PaymentClient
     */
    public function getClient(): PaymentClient {
        return $this->client;
    }

    /**
     * Constructor
     *
     * @param PaymentClient $client
     */
    public function __construct(PaymentClient $client) {
        $this->client = $client;
    }

    /**
     * Get error
     *
     * @param string $error
     * @return string
     */
    public function getError(string $error) {
        $json = json_decode($error);
        if($json) {
            $msg = '';
            if($json->errors && is_array($json->errors)) {
                foreach($json->errors as $error) {
                    $msg .= $error->message;
                    if(isset($error->moreInfo)) {
                        $msg .= " {$error->moreInfo}";
                    }
                    $msg .= ' ';
                }
            }
            return $msg;
        } else {
            return $error;
        }
    }
}
