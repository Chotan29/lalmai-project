<?php
// app/Console/Commands/GenerateApiToken.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiToken;
use Illuminate\Support\Str;

class GenerateApiToken extends Command
{
    protected $signature = 'api:token {name} {--expires=}';
    protected $description = 'Generate a new API token';

    public function handle()
    {
        $token = Str::random(40);

        ApiToken::create([
            'name' => $this->argument('name'),
            'token' => hash('sha256', $token),
            'expires_at' => $this->option('expires')
        ]);

        $this->info('API token created successfully:');
        $this->line($token); // Show the plain-text token
    }
}