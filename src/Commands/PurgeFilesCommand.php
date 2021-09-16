<?php

namespace LaravelDuskReporter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaravelDuskReporter\LaravelDuskReporter;

class PurgeFilesCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk-reporter:purge {--path=} {--y|yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge dusk report directory';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('path')) {
            $this->purgeDebuggingFiles($this->option('path'));

            return 0;
        }

        $this->purgeDebuggingFiles(
            LaravelDuskReporter::storeBuildAt()
        );

        return 0;
    }

    /**
     * Purge report folder.
     *
     * @param string $path
     * @param string $patterns
     *
     * @return void
     */
    protected function purgeDebuggingFiles($path)
    {
        if (!is_dir($path)) {
            $this->warn("Unable to purge missing directory [{$path}].");

            return;
        }


        if ($this->option('yes') || $this->confirm("Do you wish to remove directory with all content? {$path}")) {
            File::deleteDirectory($path);
            $this->info("Removed directory [{$path}].");

            return;
        }

        $this->warn("Skip removing directory [{$path}].");
    }
}
