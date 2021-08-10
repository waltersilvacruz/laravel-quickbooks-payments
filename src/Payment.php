<?php

namespace WebDEV\QuickBooks\Payments;

use Exception;
use WebDEV\QuickBooks\Payments\Models\Token;
use QuickBooksOnline\Payments\OAuth\OAuth2Authenticator;
use QuickBooksOnline\Payments\PaymentClient;

/**
 * Class Payment
 *
 * @package WebDEV\QuickBooks\Payments
 */
class Payment {
    /**
     * The configs to set up service
     *
     * @var array
     */
    protected $configs;

    /**
     * The Token instance
     *
     * @var Token
     */
    protected $token;

    /**
     * Oauth2 Helper
     * @var
     */
    protected $oauth2;

    /**
     * Payment Client
     *
     * @var PaymentClient
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param array $configs
     * @param Token $token
     */
    public function __construct(array $configs, Token $token) {
        $this->configs = $configs;
        $this->token = $token;
        $this->oauth2 = OAuth2Authenticator::create([
            'client_id' => $this->configs['data_service']['client_id'],
            'client_secret' => $this->configs['data_service']['client_secret'],
            'redirect_uri' => route('quickbooks_payments.token'),
            'environment' => $this->configs['data_service']['base_url'],
        ]);
        $this->client = new PaymentClient();
    }

    /**
     * Check to see if the token has a valid access token
     *
     * @return boolean
     */
    public function hasValidAccessToken() {
        return $this->token->hasValidAccessToken;
    }

    /**
     * Check to see if the token has a valid refresh token
     *
     * @return boolean
     */
    public function hasValidRefreshToken() {
        return $this->token->hasValidRefreshToken;
    }

    /**
     * Build URI to request authorization
     *
     * @return String
     * @throws SdkException
     * @throws ServiceException
     */
    public function authorizationUri() {
        return $this->oauth2->generateAuthCodeURL($this->configs['data_service']['scope']);
    }

    /**
     * Exchange code for token
     *
     * @param $code
     * @param $realm_id
     * @throws Exception
     */
    public function exchangeCodeForToken($code, $realm_id) {
        $request = $this->oauth2->createRequestToExchange($code);
        $response = $this->client->send($request);
        if($response->failed()) {
            $errorMessage = $response->getBody();
            throw new Exception($errorMessage);
        } else {
            $array = json_decode($response->getBody(), true);
            $this->token->parseOauthToken($array, $realm_id)->save();
        }
    }

    /**
     * Delete the token
     *
     * @return $this
     * @throws Exception
     */
    public function deleteToken()
    {
        $this->setToken($this->token->remove());
        return $this;
    }

    /**
     * Allow setting a token to switch "user"
     *
     * @param Token $token
     *
     * @return $this
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
        return $this;
    }
}
