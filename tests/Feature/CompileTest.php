<?php
test('optimizes', function () {
    Artisan::call('opcache:compile --force', []);

    $output = Artisan::output();

    expect($output)->toContain('files compiled');
});
