<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PhpcbfCommand extends Command
{
    protected $signature = 'phpcbf:run {--path= : path to files}';
    protected $description = 'Run phpcbf command';

    public function handle()
    {

        $output = [];
        $exitCode = 0;

        $path = $this->option('path') ?? './app';

        exec('./vendor/bin/phpcbf ' . $path, $output, $exitCode);
        ;
        $this->line(implode(PHP_EOL, $output));
    }
}
