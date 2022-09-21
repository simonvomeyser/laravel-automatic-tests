<?php

namespace SimonVomEyser\LaravelAutomaticTests\Commands;

use Illuminate\Console\Command;

class LaravelAutomaticTestsCommand extends Command
{
    public $signature = 'laravel-automatic-tests';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
