<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$sqlFile = __DIR__.'/../membership_form_with_data.sql';
if (!file_exists($sqlFile)) {
    echo "SQL File not found: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
try {
    DB::unprepared($sql);
    echo "SQL data executed successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
