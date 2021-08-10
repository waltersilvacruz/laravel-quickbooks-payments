<?php

namespace WebDEV\QuickBooks\Payments;

use WebDEV\QuickBooks\Payments\Models\Token;

trait HasQuickBooksToken
{
    /**
     * Have a quickBooksToken.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quickBooksToken()
    {
        return $this->hasOne(Token::class);
    }
}
