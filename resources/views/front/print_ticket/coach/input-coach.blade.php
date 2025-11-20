@extends('main.blank')
@section('content')
    @vite('resources/css/front/input-package.css')
    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title text-center">Cetak Tiket Pelatih</h2>

            <form action="{{ route('check_coach') }}" method="POST" class="ticket-form">
                @csrf
                <div class="form-group">
                    <label for="phone">No Telephone</label>
                    <input type="text" id="phone" name="phone" required>
                </div>

                <button type="submit" class="btn-submit">Submit</button>
            </form>
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

            // ✅ Tangkap error validasi dari Laravel
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showAlert('error', @json($error));
                @endforeach
            @endif

            // ✅ Tangkap session success / custom alert
            @if (session('success'))
                showAlert('success', @json(session('success')));
            @endif

            // ✅ Modal expired jika perlu
            @if (session('expired'))
                document.getElementById('memberExpiredModal').style.display = 'flex';
            @endif
        });
    </script>
@endsection
