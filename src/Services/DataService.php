<?php

namespace WebDEV\QuickBooks\Payments\Services;

use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\OAuth\OAuth2Authenticator;
use WebDEV\QuickBooks\Payments\Entities\Charge;
use WebDEV\QuickBooks\Payments\Models\Token;
use WebDEV\QuickBooks\Payments\Exceptions\InvalidRefreshTokenException;
use WebDEV\QuickBooks\Payments\Exceptions\RefreshTokenErrorException;
use Exception;

/**
 * Class DataService
 *
 * @package WebDEV\QuickBooks\Payments
 */
class DataService
{
    /**
     * Token object
     *
     * @var Token
     */
    protected $token;

    /**
     * Payment Client object
     *
     * @var PaymentClient
     */
    protected $client;

    /**
     * Oauth2 object
     *
     * @var OAuth2Authenticator
     */
    protected $oauth2;

    /**
     * Charge Entity
     *
     * @var Charge
     */
    protected $chargeEntity;

    /**
     * Constructor
     *
     * @param PaymentClient $client
     * @param OAuth2Authenticator $oauth2
     * @param Token $token
     * @throws InvalidRefreshTokenException
     */
    public function __construct(PaymentClient $client, OAuth2Authenticator $oauth2, Token $token) {
        $this->client = $client;
        $this->oauth2 = $oauth2;
        $this->token = $token;
        if(!$this->token->getHasValidAccessTokenAttribute()) {
            $this->renewAccessToken();
        }
        return $this;
    }

    /**
     * Renew access token
     */
    protected function renewAccessToken() {
        if(!$this->token->getHasValidRefreshTokenAttribute()) {
            throw new InvalidRefreshTokenException('The refresh token is no longer valid!');
        }

        try {
            $request = $this->oauth2->createRequestToRefresh($this->token->refresh_token);
            $response = $this->client->send($request);
            if ($response->failed()) {
                $message = $response->getBody();
                throw new RefreshTokenErrorException($message);
            }

            $array = json_decode($response->getBody(), true);
            $this->token->parseOauthToken($array)->save();
        } catch(Exception $ex) {
            throw new RefreshTokenErrorException($ex->getMessage());
        }
    }

    /**
     * Get card entity
     *
     * @return Charge
     */
    public function Charge() {
        if(!$this->chargeEntity) {
            $this->chargeEntity = new Charge($this->client);
        }
        return $this->chargeEntity;
    }
}
