<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class ThrottledMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 10;

    public $mail;
    public $user;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Mailable $mail, User $user)
    {
        $this->user = $user;
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::throttle('mailservice')->allow(2)->every(12)->then(function () {
            Mail::to($this->user)->send($this->mail);
        }, function () {

            return $this->release(5);
        });
    }
}
