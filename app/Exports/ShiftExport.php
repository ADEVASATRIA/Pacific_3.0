<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ShiftExport implements FromView
{
    protected $shift;
    protected $staff;

    public function __construct($shift, $staff)
    {
        $this->shift = $shift;
        $this->staff = $staff;
    }

    public function view(): View
    {
        return view('front.admin.exports.shift', [
            'shift' => $this->shift,
            'staff' => $this->staff,
        ]);
    }
}
