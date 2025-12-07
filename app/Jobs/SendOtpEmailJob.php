<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\UserConfirm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;


    public User $user;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
          try {
        Mail::to($this->user->email)->send(new UserConfirm($this->user));
    } catch (\Throwable $e) {
        Log::error('OTP mail failed', ['user_id' => $this->user->id, 'error' => $e->getMessage()]);
        throw $e;
    }
    }
}
