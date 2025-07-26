<?php

// This script should be run with: ./vendor/bin/sail exec app php quick-token.php

require_once 'vendor/autoload.php';

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if test user exists, create if not
    $user = User::where('email', 'test@example.com')->first();
    
    if (!$user) {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        echo "✅ Created new test user\n";
    } else {
        echo "✅ Using existing test user\n";
    }
    
    // Delete any existing tokens for this user
    PersonalAccessToken::where('tokenable_id', $user->id)->delete();
    
    // Create new token
    $token = $user->createToken('quick-test-token');
    
    echo "\n🎉 API Token Generated Successfully!\n";
    echo "=====================================\n";
    echo "User ID: {$user->id}\n";
    echo "User Email: {$user->email}\n";
    echo "Token: {$token->plainTextToken}\n";
    // Get the app URL from environment
    $appUrl = config('app.url');
    
    echo "\n📝 Usage Examples:\n";
    echo "curl -H \"Authorization: Bearer {$token->plainTextToken}\" {$appUrl}/api/gifs/my\n";
    echo "curl -H \"Authorization: Bearer {$token->plainTextToken}\" {$appUrl}/api/gifs/stats\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n💡 Make sure to run this script with Sail:\n";
    echo "./vendor/bin/sail exec app php quick-token.php\n";
    exit(1);
} 