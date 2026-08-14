<?php

return [
    'api_url' => env('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'api_key' => env('ABA_PAYWAY_API_KEY', '1cb54f9442ec911e271b1774a995d39ecbfb28cc'),
    'merchant_id' => env('ABA_PAYWAY_MERCHANT_ID', 'ec463509'),
];
