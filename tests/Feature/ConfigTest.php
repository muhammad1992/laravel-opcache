<?php
test('shows config', function () {
    Artisan::call('opcache:config', []);

    $output = Artisan::output();

    expect($output)
        ->toContain('Version info')
        ->and($output)
        ->toContain('Configuration info');
});
