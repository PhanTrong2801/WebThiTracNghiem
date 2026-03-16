<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- PHANCONG TABLE ---\n";
$phanCongs = DB::table('phancong')->get();
if ($phanCongs->isEmpty()) {
    echo "Table 'phancong' is EMPTY.\n";
} else {
    foreach ($phanCongs as $pc) {
        echo "User: {$pc->manguoidung} | Mon: {$pc->mamonhoc}\n";
    }
}

echo "\n--- MONHOC TABLE ---\n";
$monHocs = DB::table('monhoc')->get();
foreach ($monHocs as $mh) {
    echo "Mon: {$mh->mamonhoc} | Name: {$mh->tenmonhoc} | Khoa: " . ($mh->makhoa ?? 'NULL') . "\n";
}

echo "\n--- SESSIONS (latest) ---\n";
$sessions = DB::table('sessions')->orderBy('last_activity', 'desc')->limit(1)->get();
foreach ($sessions as $s) {
    echo "Latest session user_id: {$s->user_id}\n";
}
