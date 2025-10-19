<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionsExport implements FromView
{
    protected $transactions;
    protected $staff;
    
    public function __construct($transactions, $staff)
    {
        $this->transactions = $transactions;
        $this->staff = $staff;
    }

    public function view(): View
    {
        return view('front.admin.exports.transaction', [
            'transactions' => $this->transactions,
            'staff' => $this->staff,
        ]);
    }

}
