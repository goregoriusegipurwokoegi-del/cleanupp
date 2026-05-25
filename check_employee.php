<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::where('role','employee')->get();
foreach($users as $u) {
    echo "Email: {$u->email}, Verified: ".($u->email_verified_at ? 'YES ('.$u->email_verified_at.')' : 'NO')."\n";
}

$allUsers = App\Models\User::all();
echo "\n--- All Users ---\n";
foreach($allUsers as $u) {
    echo "Role: {$u->role}, Email: {$u->email}, Verified: ".($u->email_verified_at ? 'YES' : 'NO')."\n";
}
