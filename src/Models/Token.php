<?php

namespace WebDEV\QuickBooks\Payments\Models;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Class
 *
 * @package WebDEV\QuickBooks\Payments
 */
class Token extends Model {

    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quickbooks_app_tokens';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'access_token_expires_at',
        'refresh_token_expires_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'access_token',
        'access_token_expires_at',
        'realm_id',
        'refresh_token',
        'refresh_token_expires_at'
    ];

    /**
     * Check if access token is valid
     *
     * A token is good for 1 hour, so if it expires greater than 1 hour from now, it is still valid
     *
     * @return bool
     */
    public function getHasValidAccessTokenAttribute() {
        return $this->access_token_expires_at && Carbon::now()->lt($this->access_token_expires_at);
    }

    /**
     * Check if refresh token is valid
     *
     * A token is good for 101 days, so if it expires greater than 101 days from now, it is still valid
     *
     * @return bool
     */
    public function getHasValidRefreshTokenAttribute() {
        return $this->refresh_token_expires_at && Carbon::now()->lt($this->refresh_token_expires_at);
    }

    /**
     * Parse OauthToken.
     *
     * Process the OAuth token & store it in the persistent storage
     *
     * @param array $oauth_token
     * @param string $realm_id
     * @return Token
     */
    public function parseOauthToken(array $oauth_token, string $realm_id)
    {
        if($oauth_token) {
            $this->access_token = $oauth_token['access_token'];
            $this->access_token_expires_at = date('Y-m-d H:i:s', time() + $oauth_token['expires_in']);
            $this->realm_id = $realm_id;
            $this->refresh_token = $oauth_token['refresh_token'];
            $this->refresh_token_expires_at = date('Y-m-d H:i:s', time() + $oauth_token['x_refresh_token_expires_in']);
        }
        return $this;
    }

    /**
     * Remove the token
     *
     * When a token is deleted, we still need a token for the client for the user.
     *
     * @return Token
     * @throws Exception
     */
    public function remove() {
        $this->delete();
        return $this->make();
    }
}
