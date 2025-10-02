@extends('main.blank')
@section('content')
    @vite('resources/css/front/checkout_view.css')
    <div class="checkout-container">
        <div class="ads-box">
            {{-- Slider sponsor (opsional) --}}
            {{-- <div class="ads-slider">
                @foreach ($sponsor as $item)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="Promo {{ $loop->iteration }}">
                @endforeach
            </div> --}}
        </div>
        <div class="checkout-wrapper">
            <div class="checkout-card">
                <div class="checkout-header">
                    <h1 class="checkout-title">💳 Halaman Pembayaran</h1>
                    <p class="checkout-subtitle">Pastikan informasi pemesanan sudah benar sebelum melanjutkan.</p>
                </div>

                {{-- Step indicator --}}
                <div class="checkout-steps">
                    <div class="step active">1. Review</div>
                    <div class="step">2. Pembayaran</div>
                    <div class="step">3. Selesai</div>
                </div>

                <form action="{{ route('do_checkout') }}" method="POST">
                    @csrf
                
                    {{-- Items --}}
                    <div class="items-box">
                        <h2 class="section-title">🛒 Ringkasan Pesanan</h2>
                        <table class="checkout-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $i => $it)
                                    <tr>
                                        <td>{{ $it['name'] }}</td>
                                        <td>{{ $it['qty'] }}</td>
                                        <td>Rp {{ number_format($it['price'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($it['qty'] * $it['price'], 0, ',', '.') }}</td>
                                    </tr>
                
                                    {{-- hidden inputs --}}
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $it['id'] }}">
                                    <input type="hidden" name="items[{{ $i }}][name]" value="{{ $it['name'] }}">
                                    <input type="hidden" name="items[{{ $i }}][price]" value="{{ $it['price'] }}">
                                    <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $it['qty'] }}">
                                    <input type="hidden" name="items[{{ $i }}][type_purchase]" value="{{ $it['type_purchase'] }}">
                                @endforeach
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                                <input type="hidden" name="promo_id" value="{{ $promoId ?? '' }}">
                            </tbody>
                        </table>
                        @error('items')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                        @error('customer_id')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                        @error('promo_id')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                
                    {{-- Totals --}}
                    <div class="totals-section">
                        <div class="total-box"><span>Sub Total</span><strong>Rp
                                {{ number_format($subTotal, 0, ',', '.') }}</strong></div>
                        <div class="total-box highlight"><span>Total Bayar</span><strong>Rp
                                {{ number_format($total, 0, ',', '.') }}</strong></div>
                    </div>
                
                    {{-- hidden totals --}}
                    <input type="hidden" name="sub_total" value="{{ $subTotal }}">
                    <input type="hidden" name="tax" value="{{ $tax }}">
                    <input type="hidden" name="discount" value="{{ $discount ?? 0 }}">
                    <input type="hidden" name="total" value="{{ $total }}">
                    @error('sub_total') <span class="error-text">{{ $message }}</span> @enderror
                    @error('tax') <span class="error-text">{{ $message }}</span> @enderror
                    @error('discount') <span class="error-text">{{ $message }}</span> @enderror
                    @error('total') <span class="error-text">{{ $message }}</span> @enderror
                
                    {{-- Payment Method --}}
                    <div class="payment-method">
                        <h2 class="section-title">💰 Pilih Metode Pembayaran</h2>
                
                        <div class="custom-select">
                            <div class="selected">-- Pilih Metode --</div>
                            <div class="options">
                                <div data-value="1"><span>💵</span> Cash</div>
                                <div data-value="2"><img src="/aset/img/logo-bca.png" alt="BCA"> QRIS BCA</div>
                                <div data-value="3"><img src="/aset/img/logo-mandiri.png" alt="Mandiri"> QRIS Mandiri</div>
                                <div data-value="4"><img src="/aset/img/logo-bca.png" alt="BCA"> Debit BCA</div>
                                <div data-value="5"><img src="/aset/img/logo-mandiri.png" alt="Mandiri"> Debit Mandiri</div>
                                <div data-value="6"><span>🏦</span> Transfer Bank</div>
                                <div data-value="7"><img src="/aset/img/logo-bri.png" alt="BRI"> QRIS BRI</div>
                                <div data-value="8"><img src="/aset/img/logo-bri.png" alt="BRI"> Debit BRI</div>
                            </div>
                        </div>
                
                        <input type="hidden" name="payment" id="payment-method">
                        @error('payment')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                
                        <input type="hidden" name="payment_info" id="payment-info">
                        @error('payment_info')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                
                    {{-- Payment Details --}}
                    <div class="payment-details">
                        {{-- Approval Code (qris, transfer, debit) --}}
                        <div id="approval-code-box" class="input-box hidden">
                            <label for="approval_code">✅ Kode Approval</label>
                            <input type="text" name="approval_code" id="approval_code" placeholder="Masukkan kode approval">
                            @error('approval_code')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                
                        {{-- Cash --}}
                        <div id="money-box" class="grid-2">
                            <div class="input-box">
                                <label for="uangDiterima">💵 Uang Diterima</label>
                                <input type="number" name="uangDiterima" id="uangDiterima" placeholder="0">
                                @error('uangDiterima')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="input-box">
                                <label for="kembalian">↩️ Kembalian</label>
                                <input type="number" name="kembalian" id="kembalian" readonly>
                                @error('kembalian')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                
                        {{-- Debit / Credit --}}
                        <div id="card-box" class="input-box hidden">
                            <label for="number_card">💳 Nomor Kartu</label>
                            <input type="text" id="number_card" placeholder="**** **** **** 1234"
                                   oninput="document.getElementById('payment-info').value = this.value">
                        </div>
                
                        {{-- Staff --}}
                        <div id="staff-box" class="input-box">
                            <label for="staff_pin">🔑 PIN Staff</label>
                            <input type="password" name="staff_pin" id="staff_pin" placeholder="Masukkan PIN Staff" required>
                            @error('staff_pin')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                
                    <div class="checkout-submit">
                        <button type="submit" class="submit-btn">Konfirmasi & Bayar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
