<?php

namespace WebDEV\QuickBooks\Payments\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use WebDEV\QuickBooks\Payments\Payment;

/**
 * Class Filter
 *
 * @package WebDEV\QuickBooks\Payments
 */
class Filter
{
    /**
     * The QuickBooks payment instance.
     *
     * @var Payment
     */
    protected $quickbooks;

    /**
     * The session instance.
     *
     * @var Session
     */
    protected $session;

    /**
     * The UrlGenerator instance.
     *
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * Create a new QuickBooks filter middleware instance.
     *
     * @param Payment $quickbooks
     * @param Redirector $redirector
     * @param Session $session
     * @param UrlGenerator $url_generator
     */
    public function __construct(
        Payment $quickbooks,
        Redirector $redirector,
        Session $session,
        UrlGenerator $url_generator
    ) {
        $this->quickbooks = $quickbooks;
        $this->redirector = $redirector;
        $this->session = $session;
        $this->url_generator = $url_generator;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request
     * @param Closure $next Closure
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->quickbooks->hasValidRefreshToken()) {
            $this->session->put('url.intended', $this->url_generator->to($request->path()));
            return $this->redirector->route('quickbooks.connect');
        }

        return $next($request);
    }
}
