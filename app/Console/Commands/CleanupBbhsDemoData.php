<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupBbhsDemoData extends Command
{
    protected $signature = 'bbhs:demo-cleanup {--force : Skip the confirmation prompt}';
    protected $description = 'Remove BBHS demonstration accounts and their cascading related records.';

    public function handle(): int
    {
        $query = User::where('email', 'like', 'demo.%@bbhs-demo.test');
        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('No BBHS demo accounts found.');
            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Delete {$count} BBHS demo accounts and their related records?")) {
            $this->warn('Cleanup cancelled.');
            return self::SUCCESS;
        }

        $query->delete();

        $this->info("Deleted {$count} BBHS demo accounts. Related profiles, appointments, branch affiliations, verifications and audit records are removed by database cascades.");
        return self::SUCCESS;
    }
}
