<?php
test('is cleared', function () {
    Artisan::call('opcache:clear', []);

    $output = Artisan::output();

    expect($output)->toContain('cleared');
});
