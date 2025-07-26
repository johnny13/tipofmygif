<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:token {email} {--name=API Token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an API token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $tokenName = $this->option('name');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        $token = $user->createToken($tokenName);

        $this->info("API Token created successfully!");
        $this->info("Token: {$token->plainTextToken}");
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Token Name: {$tokenName}");

        return 0;
    }
}
