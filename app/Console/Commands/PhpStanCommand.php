<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhpStanCommand extends Command
{
    protected $signature = 'phpstan:run';
    protected $description = 'Run PHPStan analysis';

    public function handle(): void
    {
        $output = [];
        $exitCode = 0;

        exec('vendor/bin/phpstan analyse', $output, $exitCode);

        $this->line(implode(PHP_EOL, $output));
    }
}
