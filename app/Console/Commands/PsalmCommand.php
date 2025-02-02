<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PsalmCommand extends Command
{
    protected $signature = 'psalm:run {--path= : path to files}';
    protected $description = 'Run psalm command';

    public function handle()
    {

        $output = [];
        $exitCode = 0;

        $path = $this->option('path') ?? './app';

        exec('./vendor/bin/psalm ' . $path, $output, $exitCode);
        ;
        $this->line(implode(PHP_EOL, $output));
    }
}
