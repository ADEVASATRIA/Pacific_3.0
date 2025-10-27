<?php

namespace App\Http\Controllers\API\Back\Report;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportVisitorController extends Controller
{
    /**
     * Fungsi privat untuk mengambil tipe tiket berdasarkan tipe_khusus.
     * Ini adalah inti dari logic yang sama di semua fungsi grouping.
     *
     * @param string $tipeKhusus Nilai 'tipe_khusus' (1, 2, 3, atau 4).
     * @return JsonResponse
     */
    private function _getTicketsByTipeKhusus(string $tipeKhusus): JsonResponse
    {           
        $tickets = TicketType::where('tipe_khusus', $tipeKhusus)
            ->where('is_active', '1')
            ->get('id');

        // 2. Perbaikan Logika: Menggunakan ->isEmpty() untuk memeriksa jika Collection kosong.
        if ($tickets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404); // Tambahkan status HTTP 404
        } else {
            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);
        }
    }
    
    // Grouping Tiket (Menggunakan fungsi privat di atas)
    public function groupingTicketRegular(): JsonResponse
    {
        return $this->_getTicketsByTipeKhusus('1');
    }

    public function groupingTicketPengantar(): JsonResponse
    {
        return $this->_getTicketsByTipeKhusus('2');
    }

    public function groupingTicketCoach(): JsonResponse
    {
        return $this->_getTicketsByTipeKhusus('3');
    }

    public function groupingTicketMember(): JsonResponse
    {
        return $this->_getTicketsByTipeKhusus('4');
    }
}
