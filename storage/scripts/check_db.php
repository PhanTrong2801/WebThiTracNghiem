<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Nhom Quyen ---\n";
$nhomquyen = DB::table('nhomquyen')->get();
foreach ($nhomquyen as $nq) {
    echo "ID: {$nq->manhomquyen}, Name: {$nq->tennhomquyen}\n";
}

echo "\n--- Chi Tiet Quyen (first 10) ---\n";
$chitiet = DB::table('chitietquyen')->limit(10)->get();
foreach ($chitiet as $ct) {
    echo "GroupID: {$ct->manhomquyen}, Func: {$ct->chucnang}, Action: {$ct->hanhdong}\n";
}

echo "\n--- Users (manhomquyen frequency) ---\n";
$users = DB::table('users')->select('manhomquyen', DB::raw('count(*) as count'))->groupBy('manhomquyen')->get();
foreach ($users as $u) {
    echo "GroupID: {$u->manhomquyen}, Count: {$u->count}\n";
}
