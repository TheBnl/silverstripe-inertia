<?php

namespace Inertia;

use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Control\HTTPRequest;

class Middleware implements HTTPMiddleware
{
    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);

        // If we have no X-Inertia header continue
        if (!$request->getHeader('X-Inertia')) {
            return $response;
        }

        $response->addHeader('Vary', 'Accept');
        $response->addHeader('X-Inertia', 'true');

        // Don't forget to the return the response!
        return $response;
    }
}
