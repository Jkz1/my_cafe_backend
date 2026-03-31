<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

use App\Mail\PromotionEmail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Models\User;
use Mail;

#[Signature('app:send-monthly-promotion')]
#[Description('Command description')]
class SendMonthlyPromotion extends Command
{
    /**
     * Execute the console command.
     */
    protected $description = "Send monthly promotion emails to customers";
    public function handle()
    {
        $users = User::all();
        $this->info('Starting to queue ' . $users->count() . ' promotion emails...');
        $delayed = 0;
        foreach ($users as $user) {
            $this->info('Queueing email for user: ' . $user->email);
            $this->info('Queueing name for user: ' . $user->name);

            // Mail::to($user->email)->send(new PromotionEmail($user));

            //since we still using free mailtrap, we will delay the email sending to avoid hitting rate limits
            Mail::to($user->email)->later(now()->addSeconds($delayed), new PromotionEmail($user));
            $delayed += 30; 
            }
        $this->info('All emails have been sent to the queue!');
    }
}
