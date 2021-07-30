<?php

namespace App\Console\Commands;

use App\Mail\Testing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {address : Email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending emails.';

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
        $address = $this->argument('address');

        $this->info('Email test has been started.');

        Mail::to($address)->send(new Testing());

        $this->line('Email sent.');

        $this->info('Email test has been ended.');
    }
}
