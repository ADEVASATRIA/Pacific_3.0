<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::where('is_active', 1)
            ->whereNotNull('img_thumbnail')
            ->orderBy('payment_method_type_id', 'asc')
            ->get();

        // Alur Show sponsor di home
        $sponsor = Sponsor::where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        // Kita filter koleksinya:
        // Jika tipe-nya QRIS (ID: 2), kita buat unik. 
        // Jika tipe-nya selain itu (Debit, dll), kita biarkan tampil semua.
        $filteredMethods = $paymentMethods->unique(function ($item) {
            // Daftar ID tipe yang logonya harus digabung (misal: QRIS = 2)
            $genericTypes = [2];

            if (in_array($item->payment_method_type_id, $genericTypes)) {
                return 'type_' . $item->payment_method_type_id;
                // Semua yang tipenya 2 akan dianggap punya "key" yang sama, 
                // sehingga hanya 1 yang diambil.
            }

            return 'id_' . $item->id;
            // Untuk yang lain (Debit dll), gunakan ID unik masing-masing 
            // supaya tidak ada yang terbuang.
        });

        return view('main.main', ['paymentMethod' => $filteredMethods, 'sponsor' => $sponsor]);
    }
}
