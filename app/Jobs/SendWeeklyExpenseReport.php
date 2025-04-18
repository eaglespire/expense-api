<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function handle() {
        // Get all the admins
        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                ->whereBetween('created_at', [
                    now()->subWeek(), now()
                ])->get();

            // Send report via email; implement your Mailable class accordingly
            Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReport($expenses));
        }
    }
}
