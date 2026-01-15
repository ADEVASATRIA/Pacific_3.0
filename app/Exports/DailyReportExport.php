<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DailyReportExport implements FromView
{
    protected $transactions;
    protected $staff;
    protected $cashSession;

    public function __construct($transactions, $staff, $cashSession = null)
    {
        $this->transactions = $transactions;
        $this->staff = $staff;
        $this->cashSession = $cashSession;
    }

    public function view(): View
    {
        // Group transactions by payment_label
        $groupedTransactions = $this->transactions->groupBy(function ($item) {
            return $item->payment_label;
        });

        return view('front.admin.exports.daily_report', [
            'groupedTransactions' => $groupedTransactions,
            'staff' => $this->staff,
            'cashSession' => $this->cashSession,
            'totalAll' => $this->transactions->sum('total')
        ]);
    }
}
