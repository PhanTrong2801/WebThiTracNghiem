<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\UserModel;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Since this is a CLI run, Auth::id() will be null. 
// Let's just dump the entire phancong table to see what's in there.
echo "--- Phan Cong Table ---\n";
$phanCongs = DB::table('phancong')->get();
foreach ($phanCongs as $pc) {
    echo "User: {$pc->manguoidung} | Mon: {$pc->mamonhoc}\n";
}

echo "\n--- Users Table (ID only) ---\n";
$users = DB::table('users')->select('id', 'hoten')->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->hoten}\n";
}
