<?php

namespace App\Jobs;

use App\User;
use App\Mail\EmailnotifQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;
    protected $title;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailableUsers = User::where('binnacleNotifications', true)->get();
        foreach ($mailableUsers as $mailableUser) {
            $email = new EmailnotifQueue($mailableUser->name, $this->data, $this->title);
            Mail::to($mailableUser->email)->send($email);
        }
    }
}
