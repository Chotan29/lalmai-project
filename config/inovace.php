<?php

return [
    // switch between test/live via .env
    'base_url'  => env('INOVACE_BASE_URL', 'https://test.api-inovace360.com/api/v1'),
    'api_token' => env('INOVACE_API_TOKEN', ''),

    // pagination when fetching logs
    'per_page'  => (int) env('PAGINATION_LIMIT', 500),

    // where we keep our cursors like "logs:last_sync_time"
    'cursor_key_logs' => 'inovace.logs.last_sync_time',
];
