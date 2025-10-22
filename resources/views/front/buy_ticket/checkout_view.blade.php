@extends('main.blank')
@section('content')
    @vite('resources/css/front/checkout_view.css')

    <div class="checkout-container">
        <div class="ads-box">
            <div class="ads-slider">
                @foreach ($sponsor as $item)
                    @if ($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="Sponsor {{ $item->name }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    @endif
                @endforeach
            </div>
        </div>

        <div class="checkout-wrapper">
            <div class="checkout-card">
                <div class="checkout-header">
                    <h1 class="checkout-title">💳 Halaman Pembayaran</h1>
                    <p class="checkout-subtitle">Pastikan informasi pemesanan sudah benar sebelum melanjutkan.</p>
                </div>

                <div class="checkout-steps">
                    <div class="step active">1. Review</div>
                    <div class="step">2. Pembayaran</div>
                    <div class="step">3. Selesai</div>
                </div>

                <form id="checkoutForm" action="{{ route('do_checkout') }}" method="POST">
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
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $it['id'] }}">
                                    <input type="hidden" name="items[{{ $i }}][name]"
                                        value="{{ $it['name'] }}">
                                    <input type="hidden" name="items[{{ $i }}][price]"
                                        value="{{ $it['price'] }}">
                                    <input type="hidden" name="items[{{ $i }}][qty]"
                                        value="{{ $it['qty'] }}">
                                    <input type="hidden" name="items[{{ $i }}][type_purchase]"
                                        value="{{ $it['type_purchase'] }}">
                                @endforeach
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                                <input type="hidden" name="promo_id" value="{{ $promoId ?? '' }}">
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="totals-section">
                        <div class="total-box"><span>Sub Total</span><strong>Rp
                                {{ number_format($subTotal, 0, ',', '.') }}</strong></div>
                        <div class="total-box highlight"><span>Total Bayar</span><strong>Rp
                                {{ number_format($total, 0, ',', '.') }}</strong></div>
                    </div>

                    {{-- Hidden totals --}}
                    <input type="hidden" name="sub_total" value="{{ $subTotal }}">
                    <input type="hidden" name="tax" value="{{ $tax }}">
                    <input type="hidden" name="discount" value="{{ $discount ?? 0 }}">
                    <input type="hidden" name="total" value="{{ $total }}">

                    {{-- Promo Code --}}
                    <div class="promo-section">
                        <h2 class="section-title">🎟️ Gunakan Kode Promo</h2>
                        <div class="promo-box">
                            <input type="text" id="promo_code" name="promo_code" placeholder="Masukkan kode promo">
                            <button type="button" id="applyPromo" class="apply-btn">Terapkan</button>
                        </div>
                        <div id="promoMessage" class="promo-message"></div>
                    </div>


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
                                <div data-value="5"><img src="/aset/img/logo-mandiri.png" alt="Mandiri"> Debit Mandiri
                                </div>
                                <div data-value="6"><span>🏦</span> Transfer Bank</div>
                                <div data-value="7"><img src="/aset/img/logo-bri.png" alt="BRI"> QRIS BRI</div>
                                <div data-value="8"><img src="/aset/img/logo-bri.png" alt="BRI"> Debit BRI</div>
                            </div>
                        </div>

                        <input type="hidden" name="payment" id="payment-method">
                        <input type="hidden" name="payment_info" id="payment-info">
                    </div>

                    {{-- Payment Details --}}
                    <div class="payment-details">
                        <div id="approval-code-box" class="input-box hidden">
                            <label for="approval_code">✅ Kode Approval</label>
                            <input type="text" name="approval_code" id="approval_code"
                                placeholder="Masukkan kode approval">
                        </div>

                        {{-- Cash --}}
                        <div id="money-box" class="grid-2">
                            <div class="input-box">
                                <label for="uangDiterima">💵 Uang Diterima</label>
                                <input type="text" id="uangDiterima" name="uangDiterima_display" placeholder="Rp. 0">
                                <input type="hidden" id="uangDiterima_hidden" name="uangDiterima" value="0">
                            </div>

                            <div class="input-box">
                                <label for="kembalian">↩️ Kembalian</label>
                                <input type="text" name="kembalian_display" id="kembalian" readonly
                                    placeholder="Rp. 0">
                                <input type="hidden" name="kembalian" id="kembalian_hidden" value="0">
                            </div>
                        </div>

                        <div id="card-box" class="input-box hidden">
                            <label for="number_card">💳 Nomor Kartu</label>
                            <input type="text" id="number_card" placeholder="**** **** **** 1234"
                                oninput="document.getElementById('payment-info').value = this.value">
                        </div>

                        {{-- Staff --}}
                        <div id="staff-box" class="input-box">
                            <label for="staff_pin">🔑 PIN Staff</label>
                            <input type="password" name="staff_pin" id="staff_pin" placeholder="Masukkan PIN Staff"
                                required>
                        </div>
                    </div>

                    <div class="checkout-submit">
                        <button type="submit" class="submit-btn">Konfirmasi & Bayar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT VALIDASI + ALERT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // === HANDLE PROMO ===
            const applyPromoBtn = document.getElementById('applyPromo');
            const promoCodeInput = document.getElementById('promo_code');
            const promoMessage = document.getElementById('promoMessage');

            applyPromoBtn.addEventListener('click', function() {
                const code = promoCodeInput.value.trim();
                if (!code) {
                    showAlert('error', 'Silakan masukkan kode promo terlebih dahulu.');
                    return;
                }

                fetch('{{ route('validate_promo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            promo_code: code,
                            items: @json($items),
                            sub_total: {{ $subTotal }},
                            total: {{ $total }}
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        promoMessage.innerHTML = '';
                        if (data.success) {
                            showAlert('success', data.message);
                            promoMessage.innerHTML =
                                `<div class="promo-success">🎉 ${data.message}</div>`;

                            // Update hidden inputs
                            const promoIdInput = document.querySelector('input[name="promo_id"]');
                            const discountInput = document.querySelector('input[name="discount"]');
                            const totalInput = document.querySelector('input[name="total"]');

                            if (promoIdInput) promoIdInput.value = data.promo_id;
                            if (discountInput) discountInput.value = data.discount;
                            if (totalInput) totalInput.value = data.new_total;

                            // Update tampilan subtotal/total
                            const totalsSection = document.querySelector('.totals-section');
                            if (totalsSection) {
                                totalsSection.innerHTML = `
                        <div class="total-box"><span>Sub Total</span><strong>Rp ${data.formatted_subtotal}</strong></div>
                        <div class="total-box"><span>Diskon</span><strong>- Rp ${data.formatted_discount}</strong></div>
                        <div class="total-box highlight"><span>Total Bayar</span><strong>Rp ${data.formatted_total}</strong></div>
                    `;
                            }

                            // 🔹 Pastikan DOM selesai di-update sebelum hitung ulang
                            setTimeout(() => {
                                console.log('%c=== Recalculate After Promo ===',
                                    'color:green; font-weight:bold');
                                hitungKembalian();
                            }, 50);

                        } else {
                            showAlert('error', data.message);
                            promoMessage.innerHTML =
                            `<div class="promo-error">⚠️ ${data.message}</div>`;
                            document.querySelector('input[name="promo_id"]').value = '';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showAlert('error', 'Terjadi kesalahan saat memproses promo.');
                    });
            });

            // === ALERT FUNCTION ===
            function showAlert(type, message) {
                const existing = document.querySelector('.alert-slide');
                if (existing) existing.remove();

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert-slide ${type}`;
                alertDiv.innerHTML = `
            <div style="font-weight:600; margin-right:.4rem;">${type === 'error' ? 'Gagal!' : 'Berhasil!'}</div>
            <div style="flex:1;">${message}</div>
            <button class="alert-close" aria-label="close">&times;</button>
        `;
                document.body.appendChild(alertDiv);

                alertDiv.querySelector('.alert-close').addEventListener('click', () => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 250);
                });

                setTimeout(() => alertDiv.classList.add('show'), 50);
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 300);
                }, 4000);
            }

            // === SESSION ALERT ===
            @if (session('error'))
                showAlert('error', @json(session('error')));
            @endif
            @if (session('success'))
                showAlert('success', @json(session('success')));
            @endif

            // === HANDLE PEMBAYARAN ===
            const paymentInput = document.getElementById('payment-method');
            const approvalBox = document.getElementById('approval-code-box');
            const cardBox = document.getElementById('card-box');
            const moneyBox = document.getElementById('money-box');
            const selected = document.querySelector('.custom-select .selected');
            const options = document.querySelectorAll('.custom-select .options div');

            options.forEach(opt => {
                opt.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent.trim();

                    selected.textContent = text;
                    paymentInput.value = value;

                    approvalBox.classList.add('hidden');
                    cardBox.classList.add('hidden');
                    moneyBox.classList.add('hidden');

                    if (value === '1') {
                        moneyBox.classList.remove('hidden');
                    } else if (['2', '3', '4', '5', '7', '8'].includes(value)) {
                        approvalBox.classList.remove('hidden');
                        cardBox.classList.remove('hidden');
                    }
                });
            });

            // === VALIDASI SEBELUM SUBMIT ===
            const form = document.getElementById('checkoutForm');
            form.addEventListener('submit', function(e) {
                const payment = paymentInput.value;
                const staffPin = document.getElementById('staff_pin').value.trim();

                if (!payment) {
                    e.preventDefault();
                    showAlert('error', 'Silakan pilih metode pembayaran terlebih dahulu.');
                    return;
                }

                if (!staffPin) {
                    e.preventDefault();
                    showAlert('error', 'PIN staff wajib diisi.');
                    return;
                }

                if (payment === '1') {
                    const uangDiterima = parseFloat(document.getElementById('uangDiterima_hidden').value);
                    if (!uangDiterima || uangDiterima <= 0) {
                        e.preventDefault();
                        showAlert('error', 'Masukkan jumlah uang yang diterima untuk pembayaran tunai.');
                        return;
                    }
                }

                if (['2', '3', '4', '5', '7', '8'].includes(payment)) {
                    const approval = document.getElementById('approval_code').value.trim();
                    if (!approval) {
                        e.preventDefault();
                        showAlert('error', 'Kode approval wajib diisi untuk metode non-tunai.');
                        return;
                    }
                }
            });

            // === HITUNG KEMBALIAN OTOMATIS + FORMAT UANG ===
            const uangDiterimaInput = document.getElementById('uangDiterima');
            const uangDiterimaHidden = document.getElementById('uangDiterima_hidden');
            const kembalianInput = document.getElementById('kembalian');
            const kembalianHidden = document.getElementById('kembalian_hidden');

            function cleanNumber(str) {
                return parseFloat(str.replace(/[^\d]/g, '')) || 0;
            }

            function formatRupiah(num) {
                return 'Rp ' + num.toLocaleString('id-ID');
            }

            function hitungKembalian() {
                const uang = cleanNumber(uangDiterimaInput.value);
                const totalInput = document.querySelector('input[name="total"]');
                const total = totalInput ? parseFloat(totalInput.value) || 0 : 0;
                const kembali = uang - total;

                uangDiterimaHidden.value = uang;
                kembalianHidden.value = kembali > 0 ? kembali : 0;
                kembalianInput.value = formatRupiah(kembalianHidden.value);

                console.log('=== DEBUG KEMBALIAN ===');
                console.log('Total Input:', total);
                console.log('Uang Diterima:', uang);
                console.log('Kembalian:', kembali);
                console.log('Kembalian Hidden:', kembalianHidden.value);
            }

            uangDiterimaInput.addEventListener('input', () => {
                let raw = uangDiterimaInput.value.replace(/[^\d]/g, '');
                if (raw === '') {
                    uangDiterimaInput.value = '';
                    uangDiterimaHidden.value = 0;
                    kembalianHidden.value = 0;
                    kembalianInput.value = 'Rp 0';
                    return;
                }

                const num = parseInt(raw);
                uangDiterimaInput.value = formatRupiah(num);
                uangDiterimaHidden.value = num;
                hitungKembalian();
            });
        });
    </script>
@endsection
