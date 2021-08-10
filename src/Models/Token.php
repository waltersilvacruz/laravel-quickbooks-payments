<?php

namespace WebDEV\QuickBooks\Payments\Models;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Class
 *
 * @package WebDEV\QuickBooks\Payments
 */
class Token extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quickbooks_tokens';

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
        'refresh_token_expires_at',
        'user_id',
    ];

    /**
     * Check if access token is valid
     *
     * A token is good for 1 hour, so if it expires greater than 1 hour from now, it is still valid
     *
     * @return bool
     */
    public function getHasValidAccessTokenAttribute(): bool {
        return $this->access_token_expires_at && Carbon::now()->lt($this->access_token_expires_at);
    }

    /**
     * Check if refresh token is valid
     *
     * A token is good for 101 days, so if it expires greater than 101 days from now, it is still valid
     *
     * @return bool
     */
    public function getHasValidRefreshTokenAttribute(): bool {
        return $this->refresh_token_expires_at && Carbon::now()->lt($this->refresh_token_expires_at);
    }

    /**
     * Remove the token
     *
     * When a token is deleted, we still need a token for the client for the user.
     *
     * @return Token
     * @throws Exception
     */
    public function remove(): Token {
        $user = $this->user;
        $this->delete();
        return $user->quickBooksToken()->make();
    }

    /**
     * Belongs to user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        $config = config('quickbooks.user');
        return $this->belongsTo($config['model'], $config['keys']['foreign'], $config['keys']['owner']);
    }
}
