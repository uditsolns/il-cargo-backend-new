<?php

namespace App\Jobs;

use App\Mail\FinalReportMail;
use App\Models\CargoDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the final report (see FinalReportMail) exactly once per dispatch,
 * when pending_servey flips to 1 (trip completed). final_report_sent_at
 * gates the resend - this job itself is scheduled every minute
 * (Console\Kernel::schedule()), and previously had no such guard, so it
 * would otherwise re-email the same dispatch on every run for as long as
 * pending_servey stayed at 1. Also previously only looked at the single
 * *latest* matching dispatch (->latest()->first()); now processes every
 * one that hasn't been notified yet.
 */
class CargoCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $completedDispatches = CargoDetail::where('pending_servey', 1)
            ->whereNull('final_report_sent_at')
            ->with('group')
            ->get();

        if ($completedDispatches->isEmpty()) {
            Log::info('CargoCompleted: no newly-completed dispatches to email.');
            return;
        }

        foreach ($completedDispatches as $cargoDetail) {
            $emails = $cargoDetail->group?->additional_emails ?? [];

            if (empty($emails)) {
                Log::info("CargoCompleted: no additional_emails configured for group {$cargoDetail->group_id}, dispatch {$cargoDetail->dispatch_id} - marking processed without sending.");
                $cargoDetail->update(['final_report_sent_at' => now()]);
                continue;
            }

            foreach ($emails as $email) {
                Mail::to($email)->send(new FinalReportMail($cargoDetail));
                Log::info("CargoCompleted: final report sent to {$email} for dispatch {$cargoDetail->dispatch_id}.");
            }

            $cargoDetail->update(['final_report_sent_at' => now()]);
        }
    }
}
