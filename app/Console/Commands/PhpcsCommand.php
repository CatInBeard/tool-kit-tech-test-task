<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PhpcsCommand extends Command
{
    protected $signature = 'phpcs:run {--path= : path to files}';
    protected $description = 'Run PHP_CodeSniffer';

    public function handle()
    {

        $output = [];
        $exitCode = 0;

        $path = $this->option('path') ?? './app';

        exec('./vendor/bin/phpcs ' . $path, $output, $exitCode);
        ;
        $this->line(implode(PHP_EOL, $output));
    }
}
