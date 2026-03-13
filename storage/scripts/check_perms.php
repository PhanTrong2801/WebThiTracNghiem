<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('manhomquyen', 1)->first();
if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "Group ID: " . $user->manhomquyen . "\n";
    $perms = $user->getRolePermissions();
    echo "Permissions JSON: " . json_encode($perms, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "No user with manhomquyen=1 found.\n";
}
