<?php

namespace App\Http\Controllers\Back\ManagementPackage;

use App\Http\Controllers\Controller;
use App\Models\PackageComboDetail;
use App\Models\Ticket;
use App\Models\TicketType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\PackageComboRedeem;
use Illuminate\Support\Facades\DB;
use App\Models\TicketEntry;

class ManagementPackageCustomerController extends Controller
{
    public function index(Request $request)
    {
        $phone = $request->input("phone");
        $viewData = collect(); // Default empty collection
        $customer = null;
        $error = null;

        // Hanya cari data jika ada input phone
        if ($phone) {
            $customer = Customer::where("phone", $phone)->first();

            if (!$customer) {
                $error = "Customer dengan nomor telepon '{$phone}' tidak ditemukan.";
            } else {
                $redeemHistory = PackageComboRedeem::with(['purchaseDetail', 'packageCombo', 'details'])
                    ->where('customer_id', $customer->id)
                    ->get();

                $viewData = $redeemHistory->map(function ($redeem) {
                    $remainingQty = $redeem->details->pluck('qty_redeemed')->first();
                    return [
                        'id' => $redeem->id,
                        'purchase_date' => Carbon::parse($redeem->purchaseDetail->created_at)->format('Y-m-d'),
                        'invoice_no' => $redeem->purchaseDetail->invoice_no,
                        'package_name' => $redeem->name,
                        'total_redeemed' => $redeem->details->sum('qty_printed') == null ? 'Belom Dilakukan Redeem' : $redeem->details->sum('qty_printed'),
                        'remaining_qty' => $remainingQty == 0 ? 'Sudah Habis' : $remainingQty,
                        'expired_date' => Carbon::parse($redeem->expired_date)->format('Y-m-d'),
                    ];
                });
            }
        }

        return view("back.management_package.updateDataPackage", compact("viewData", "customer", "phone", "error"));
    }

    public function viewDetailPackage($id)
    {
        $tickets = Ticket::where('package_combo_redeem_detail_id', $id)
            ->orderBy('is_active', 'desc')
            ->get();
        $customer = Customer::find($tickets->first()->customer_id);
        if (empty($tickets)) {
            return redirect()->back()->with('error', 'Data tickets tidak ditemukan');
        } elseif (!empty($tickets)) {
            $viewDetailsData = $tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'status_ticket' => $ticket->is_active == 1 ? 'Aktif' : 'Tidak Aktif',
                ];
            });
        }

        return view('back.management_package.detailPackage', compact('viewDetailsData', 'customer'));
    }

    // Logika show form edit package
    public function getEditPackage($id)
    {
        $package = PackageComboRedeem::with(['purchaseDetail', 'packageCombo', 'details'])->find($id);

        if (!$package) {
            return response()->json(['error' => 'Package Tidak Ditemukan'], 404);
        }
        $customer = Customer::find($package->purchaseDetail->customer_id);

        $remainingQty = $package->details->pluck('qty_redeemed')->first();

        return response()->json([
            'id' => $package->id,
            'package_name' => $package->name,
            'total_redeemed' => $package->details->sum('qty_printed') == null ? '0' : $package->details->sum('qty_printed'),
            'remaining_qty' => $remainingQty == 0 ? '0' : $remainingQty,
            'expired_date' => Carbon::parse($package->expired_date)->format('Y-m-d'),
        ]);
    }

    // Logika edit package
    public function editPackage(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'total_redeemed' => 'required|integer|min:0',
                'remaining_qty' => 'required|integer|min:0',
                'expired_date' => 'required|date',
            ], [
                'name.required' => 'Nama Package harus diisi!',
                'total_redeemed.required' => 'Total Redeemed harus diisi!',
                'remaining_qty.required' => 'Sisa Qty harus diisi!',
                'expired_date.required' => 'Tanggal Expired harus diisi!',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $package = PackageComboRedeem::with(['packageCombo', 'details', 'customer'])->find($id);
        
            if (!$package) {
                throw new \Exception('Package Tidak Ditemukan');
            }

            if ($package->fully_redeemed_at) {
                $package->fully_redeemed_at = null;
            }

            $package->name = $request->name;
            $package->expired_date = $request->expired_date;
            $package->push();

            $detail = $package->details->first();
            if ($detail) {
                $maxQty = $detail->qty;
                $totalRedeemed = (int) $request->total_redeemed;
                $remainingQty = (int) $request->remaining_qty;

                if ($totalRedeemed > $maxQty || $remainingQty > $maxQty) {
                    throw new \Exception('Total Redeem atau Sisa Redeem Melebihi Qty.');
                }

                $detail->qty_printed = $totalRedeemed;
                $detail->qty_redeemed = $remainingQty;
                // dd('masuk sini', $detail);

                if ($remainingQty == 0) {
                    $package->fully_redeemed_at = now();
                }

                if (!$package->save() || !$detail->save()) {
                     throw new \Exception("Gagal memperbarui data Package.");
                }
                
                // dd('masuk sini', $package, $detail);
            }

            // Nonaktifkan tiket lama
            Ticket::where('package_combo_redeem_detail_id', $id)
                            ->update(['is_active' => 0]);

            // --- Buat tiket baru ---
            $packageComboDetail = PackageComboDetail::where('package_combo_id', $package->packageCombo->id)->first();
            $ticketType = TicketType::find($packageComboDetail->item_id);
            $customerId = $package->customer->id;
            $today = now()->toDateString();

            $ticketsToInsert = [];

            for ($i = 0; $i < $request->remaining_qty; $i++) {
                $ticketsToInsert[] = [
                    'package_combo_redeem_detail_id' => $id,
                    'customer_id' => $customerId,
                    'code' => Ticket::generateCodeFast($ticketType->ticket_kode_ref),
                    'ticket_kode_ref' => $ticketType->ticket_kode_ref,
                    'date_start' => $today,
                    'date_end' => $request->expired_date,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Ticket::insert($ticketsToInsert);
            
            // $ticketsBaru = Ticket::active()
            //     ->where('customer_id', $customerId)
            //     ->whereDate('date_start', '<=', $today)
            //     ->whereDate('date_end', '>=', $today)
            //     ->with(['entries', 'purchaseDetail', 'packageComboRedeemDetail'])
            //     ->get();

            $ticketsBaru = Ticket::where('package_combo_redeem_detail_id', $id)
                ->where('is_active', 1)
                ->where('customer_id', $customerId)
                ->whereDate('date_start', '<=', $today)
                ->whereDate('date_end', '>=', $today)
                ->with(['entries', 'purchaseDetail', 'packageComboRedeemDetail'])
                ->get();
            // dd('masuk sini', $ticketsBaru);
            
            $entriesToInsert = [];
            foreach ($ticketsBaru as $ticket) {
                if ($ticket->entries->count() == 0) {
                    $qtyPrint = 0;

                    if ($ticket->purchaseDetail) {
                        $qtyPrint = $ticket->purchaseDetail->qty + $ticket->purchaseDetail->qty_extra;
                    } elseif (!empty($ticket->packageComboRedeemDetail)) {
                        $qtyPrint = 1 + ($ticket->packageComboRedeemDetail->qty_extra ?? 0);
                    }
                    for ($i = 0; $i < $qtyPrint; $i++) {
                        $entriesToInsert[] = [
                            'ticket_id' => $ticket->id,
                            'code' => TicketEntry::generateCodeFast($ticket->id),
                            'status' => TicketEntry::STATUS_NEW,
                            'date_valid' => $today,
                            'type' => ($i % 2 == 0) ? 1 : 2,
                            'created_at' => now(),
                        ];
                    }
                    // dd($entriesToInsert);
                    // dd('masuk sini', $entriesToInsert);
                }
            }

            if (!empty($entriesToInsert)) {
                TicketEntry::insert($entriesToInsert);
            }

            DB::commit();

            $phone = optional($package->customer)->phone;

            return redirect()
                ->route('view-update-package-home', compact('phone'))
                ->with([
                    'success' => true,
                    'action' => 'edit'
                ]);


        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Logika delete package
    public function deletePackage($id)
    {
        $package = PackageComboRedeem::with('details.tickets')->find($id);

        if (!$package) {
            return redirect()->back()->with('error', 'Package Tidak Ditemukan');
        }

        foreach ($package->details as $detail) {
            foreach ($detail->tickets as $ticket) {
                // (b) kalau cuma nonaktifkan
                $ticket->is_active = 0;
                $ticket->save();
            }

            $detail->delete();
        }

        $package->delete();

        return redirect()->route('view-update-package-home')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }

}
