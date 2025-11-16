@extends('main.blank')
@section('content')
    @vite('resources/css/front/input-package.css')
    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title text-center">Cetak Member</h2>

            <form action="{{ route('check_member') }}" method="POST" class="ticket-form">
                @csrf
                <div class="form-group">
                    <label for="phone">No Telephone</label>
                    <input type="text" id="phone" name="phone" required>
                </div>

                <button type="submit" class="btn-submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Modal Expired -->
    <div id="memberExpiredModal" class="member-modal" style="display:none;">
        <div class="member-modal-content">
            <h3>Ticket Anda sudah expired</h3>
            <p>Silakan perpanjang membership Anda untuk tetap bisa digunakan.</p>
            <div class="member-modal-actions">
                <form action="{{ route('member.extend') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ session('customer_id') }}">
                    <button type="submit" class="member-btn member-btn-primary">Perpanjang Sekarang</button>
                </form>

                <a href="{{ route('main') }}" class="member-btn member-btn-secondary">Kembali ke Home</a>
            </div>
        </div>
    </div>

    <!-- Modal Renewal H-7 -->
    <div id="memberRenewalModal" class="member-modal" style="display:none;">
        <div class="member-modal-content">
            <h3>Membership Anda akan segera berakhir</h3>
            <p>{{ session('renewal') }}</p>
            <div class="member-modal-actions">
                <form action="{{ route('member.extend') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ session('customer_id') ?? '' }}">
                    <button type="submit" class="member-btn member-btn-primary">Perpanjang Sekarang</button>
                </form>

                <!-- anchor default tanpa memanggil route() di server -->
                <a id="memberRenewLaterBtn" href="#" class="member-btn member-btn-secondary">Nanti Saja</a>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            @if (session('error'))
                showAlert('error', @json(session('error')));
            @endif

            @if (session('success'))
                showAlert('success', @json(session('success')));
            @endif

            @if (session('expired'))
                document.getElementById('memberExpiredModal').style.display = 'flex';
            @endif

            @if (session('renewal'))
                document.getElementById('memberRenewalModal').style.display = 'flex';
            @endif

            @if (session('customer_id'))
                const laterBtn = document.getElementById('memberRenewLaterBtn');
                if (laterBtn) {
                    laterBtn.setAttribute('href',
                        "{{ route('member.print_member', ['customerID' => session('customer_id')]) }}");
                }
            @else
                const laterBtn = document.getElementById('memberRenewLaterBtn');
                if (laterBtn) {
                    laterBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                    });
                }
            @endif
        });
    </script>
@endsection
