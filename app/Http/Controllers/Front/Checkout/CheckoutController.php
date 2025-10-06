<?php

namespace App\Http\Controllers\Front\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Checkout\CheckoutService;
use App\Services\Member\ExtendsService;
use App\Models\Purchase;
use Milon\Barcode\DNS1D;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $extendsService;

    public function __construct(CheckoutService $checkoutService, ExtendsService $extendsService)
    {
        $this->checkoutService = $checkoutService;
        $this->extendsService = $extendsService;
    }

    public function submitFormTicket(Request $request)
    {
        try {
            $data = $this->checkoutService->prepareCheckoutData($request);

            $token = bin2hex(random_bytes(16));
            $data['checkout_token'] = $token;

            session(['checkout_token' => $token]);


            return redirect()->route('checkout_ticket')->with($data);

        } catch (\Throwable $e) {
            // kembali dengan input dan flash error
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function submitFormMember(Request $request)
    {
        try {
            $data = $this->extendsService->prepareCheckoutData($request);

            $token = bin2hex(random_bytes(16));
            $data['checkout_token'] = $token;

            session(['checkout_token' => $token]);


            return redirect()->route('checkout_ticket')->with($data);

        } catch (\Throwable $e) {
            // kembali dengan input dan flash error
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function doCheckout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'customer_id' => 'required|exists:customers,id',

            // Promo Validate if data promo is valid
            'promo_id' => 'nullable|exists:promos,id',

            // Validate the staff that doing purchase complete
            'staff_pin' => 'required|string',

            // Validate payment Method
            'payment' => 'required|integer',
            'payment_info' => 'nullable|string',
            'approval_code' => 'nullable|string',

            // Uang kembalian maupun uang yang diterima
            'uangDiterima' => 'nullable|numeric|min:0',
            'kembalian' => 'nullable|numeric|min:0',

            // Input sub_total, tax, dan discount(jika ada)
            'sub_total' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);


        try {
            $purchase = $this->checkoutService->processCheckout($request);

            return redirect()->route('checkout_success', $purchase->id)
                ->with('success', 'Order berhasil dibuat. Nomor Invoice: ' . $purchase->invoice);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat purchase: ' . $e->getMessage());
        }
    }

    public function checkoutSuccess($id)
    {
        // Ambil purchase dengan relasi
        $purchase = Purchase::with(['customer.clubhouse', 'purchaseDetails', 'staff'])->findOrFail($id);

        // Buat instance DNS1D
        $barcodeGenerator = new DNS1D();
        $barcode = $barcodeGenerator->getBarcodePNG($purchase->invoice_no, 'C39'); // Code39

        return view('front.buy_ticket.checkout_finish', compact('purchase', 'barcode'));
    }

}
