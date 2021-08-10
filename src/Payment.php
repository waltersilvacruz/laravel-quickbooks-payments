<?php

namespace WebDEV\QuickBooks\Payments;

use Exception;
use WebDEV\QuickBooks\Payments\Models\Token;

/**
 * Class Payment
 *
 * @package WebDEV\QuickBooks\Payments
 */
class Payment
{
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
     * Client constructor.
     *
     * @param array $configs
     * @param Token $token
     */
    public function __construct(array $configs, Token $token)
    {
        $this->configs = $configs;
        $this->token = $token;
    }
}
