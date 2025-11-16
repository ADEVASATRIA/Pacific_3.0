<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase;
use Milon\Barcode\DNS1D;


class PrintReceiptController extends Controller
{
    public function printReceipt($id){
        $purchase = Purchase::with(['customer.clubhouse', 'purchaseDetails', 'staff'])->findOrFail($id);
        // Buat instance DNS1D
        $barcodeGenerator = new DNS1D();
        $barcode = $barcodeGenerator->getBarcodePNG($purchase->invoice_no, 'C39'); // Code39
        return view('front.print_receipt.print_receipt', compact('purchase', 'barcode'));
    }
}
