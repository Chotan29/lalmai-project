<?php
require __DIR__ . '/bootstrap/app.php';
use Illuminate\Support\Facades\DB;

$app->make(Illuminate\Contracts\Http\Kernel::class);

// Check all permissions with 'registration' in name
$perms = DB::table('permissions')->where('name', 'LIKE', '%registration%')->get();
echo "=== All registration permissions ===\n";
foreach ($perms as $p) {
    echo "ID: {$p->id}, Name: {$p->name}\n";
}

echo "\n=== Checking web-setting-registration-* permissions ===\n";
$target_perms = ['web-setting-registration-index', 'web-setting-registration-add', 'web-setting-registration-edit'];
foreach ($target_perms as $pname) {
    $p = DB::table('permissions')->where('name', $pname)->first();
    echo "$pname: " . ($p ? "EXISTS (id={$p->id})" : "NOT FOUND") . "\n";
}

echo "\n=== Checking superadmin user ===\n";
$user = DB::table('users')->where('email', 'superadmin@edufirm.com')->first();
if ($user) {
    echo "User ID: {$user->id}, Email: {$user->email}, Active: {$user->is_active}\n";
    $roles = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id')->toArray();
    echo "Roles: " . json_encode($roles) . "\n";
} else {
    echo "User NOT FOUND\n";
}

echo "\n=== Checking superadmin role ===\n";
$role = DB::table('roles')->where('name', 'super-admin')->first();
if ($role) {
    echo "Role ID: {$role->id}, Name: {$role->name}\n";
} else {
    echo "super-admin role NOT FOUND\n";
}
?>
