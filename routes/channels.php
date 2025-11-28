<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('Byte-Test', function () {
    // Allow any authenticated user (e.g., admin in Byte-backend)
    // Adjust based on your auth setup
    // return auth()->check();
    return 'Test Succes';
});
