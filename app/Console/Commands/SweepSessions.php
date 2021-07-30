<?php

namespace App\Console\Commands;

use App\Models\Quizzes\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SweepSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep:sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete sessions that created six hours ago or more.';

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
     * @return mixed
     */
    public function handle()
    {
        $current_ts = new \DateTime();

        $this->info('Session sweep has been started.');

        $this->line('Getting sessions that needs be swept...');
        $sessions = Session::where('alive_until', '<', $current_ts->format('Y-m-d H:i:s'))->get();
        $this->line("There are {$sessions->count()} sessions that needs be swept.");

        foreach ($sessions as $session) {
            $this->line("Removing session ID [{$session->id}]...");

            $this->line("Removing temporary [RRWeb] data...");
            Session\WebSession::ofSession($session->id)->forceDelete();

            $this->line("Removing all temporary files...");
            $dir_src  = 'sessions' . DIRECTORY_SEPARATOR . $session->id . DIRECTORY_SEPARATOR;
            Storage::disk('public')->deleteDirectory($dir_src);

            $this->line("Removing session table...");
            $session->forceDelete();

            $this->line("Session ID [{$session->id}] has been removed and cleaned up.");
        }

        $this->info('Session sweep has been ended.');
    }
}
