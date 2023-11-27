<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAchievementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:achievement {name} {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new achievement class stub';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the content of the achievement stub file
        $stub = file_get_contents(app_path('Console/Commands/achievement.stub'));

        // Replace placeholders in the stub with provided arguments
        $find = array("{{CLASS}}", "{{TYPE}}");
        $replace   = array($this->argument('name'), $this->argument('type'));
        $stub = str_replace($find, $replace, $stub);

        // Define the path to save the new achievement class file
        $path = app_path('Achievements/Types/' . $this->argument('name').'.php');

        // Check if the file already exists
        if (!file_exists($path)) {
            file_put_contents($path, $stub);
            $this->info($path. ' was created');
        } else {
            $this->info($path. ' was already exists');
        }

        return 0; // Return 0 for successful execution
    }
}
