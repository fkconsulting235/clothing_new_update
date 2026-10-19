<?php

return [
    'name' => env('STORE_NAME', 'Mi Tienda'),
    'currency_symbol' => env('STORE_CURRENCY_SYMBOL', 'C$'),
    'whatsapp_number' => env('WHATSAPP_NUMBER', ''),
    'reservation_hours' => (int) env('RESERVATION_HOURS', 24),
    'notification_email' => env('STORE_NOTIFICATION_EMAIL', ''),
];
