<?php

namespace App\Filters;

use CodeIgniter\Filters\SecureHeaders;

class SecureHeadersExt extends SecureHeaders
{
    protected $headers = [
        // https://owasp.org/www-project-secure-headers/#x-frame-options
        'X-Frame-Options' => 'SAMEORIGIN',

        // https://owasp.org/www-project-secure-headers/#x-content-type-options
        'X-Content-Type-Options' => 'nosniff',

        // https://docs.microsoft.com/en-us/previous-versions/windows/internet-explorer/ie-developer/compatibility/jj542450(v=vs.85)#the-noopen-directive
        'X-Download-Options' => 'noopen',

        // https://owasp.org/www-project-secure-headers/#x-permitted-cross-domain-policies
        'X-Permitted-Cross-Domain-Policies' => 'none',

        // https://owasp.org/www-project-secure-headers/#referrer-policy
        'Referrer-Policy' => 'strict-origin-when-cross-origin',

        // https://owasp.org/www-project-secure-headers/#x-xss-protection
        // If you do not need to support legacy browsers, it is recommended that you use
        // Content-Security-Policy without allowing unsafe-inline scripts instead.
        // 'X-XSS-Protection' => '1; mode=block',
    ];

//    public function before(RequestInterface $request, $arguments = null)
//    {
//
//    }
//
//    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
//    {
//        // Do something here
//    }
}
