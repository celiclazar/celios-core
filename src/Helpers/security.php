<?php

if (! function_exists('csp_nonce')) {
    /**
     * Get the current Content Security Policy cryptographic nonce.
     */
    function csp_nonce(): ?string
    {
        return app()->has('csp-nonce') ? (string) app('csp-nonce') : null;
    }
}
