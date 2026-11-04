<?php

return [

    /*
    | Proxies de confiance : IP/CIDR séparés par des virgules, ou "*" pour tous.
    | Vide = aucun. Lu par le middleware TrustProxies de Laravel.
    */
    'proxies' => env('TRUSTED_PROXIES') ?: null,

];
