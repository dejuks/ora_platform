<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * One-time cleanup for the "force existing users to verify too"
 * change: every account created before email verification existed
 * has email_verified_at = null but was never actually sent a
 * verification email (that notification only fires on new
 * registration). Run this once after deploying that migration so
 * existing users get a link in their inbox instead of discovering
 * they're locked out with nothing to click but "Resend".
 *
 *     php artisan users:send-verification-emails
 */
class SendVerificationEmailsToExistingUsers extends Command
{
    protected $signature = 'users:send-verification-emails';

    protected $description = 'Send a verification email to every currently-unverified user';

    public function handle(): int
    {
        $users = User::whereNull('email_verified_at')->get();

        if ($users->isEmpty()) {
            $this->info('No unverified users found.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($users->count());
        $sent = 0;
        $skipped = 0;

        foreach ($users as $user) {
            if (! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $user->sendEmailVerificationNotification();
            $sent++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Sent: {$sent}. Skipped (invalid/missing email): {$skipped}.");

        return self::SUCCESS;
    }
}
