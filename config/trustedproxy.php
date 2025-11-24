<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set trusted proxy IP addresses. You can use CIDR notation to trust
    | networks of proxy servers. Setting this to "*" will trust all proxies
    | that present the proper headers defined in $headers.
    |
    */

    'proxies' => env('TRUSTED_PROXIES', null),

    /*
    |--------------------------------------------------------------------------
    | Trusted Headers
    |--------------------------------------------------------------------------
    |
    | The headers that should be used to detect proxies. Each header will be
    | trusted when you have trusted proxies via the proxies configuration
    | value above. If your load balancer doesn't send one of these headers but
    | does send others, you may add them to the array.
    |
    */

    'headers' => \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                 \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                 \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                 \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO,

];