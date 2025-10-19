@extends('main.blank')

@section('content')
    @vite('resources/css/front/registrasi_customer.css')
    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title">Registrasi New Customer</h2>

            <form action="{{ route('register_data_customer') }}" method="POST" class="ticket-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="phone">No phone</label>
                    <input type="text" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="dob">Tanggal Lahir</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

                <div class="form-group">
                    <label for="type_customer">Tipe Customer</label>
                    <select name="type_customer" id="type_customer" required>
                        <option value="">Pilih Tipe Customer</option>
                        <option value="1">Pria</option>
                        <option value="2">Wanita</option>
                        <option value="3">Anak</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_customer">Kategori Customer</label>
                    <select name="category_customer" id="category_customer" required>
                        <option value="">Pilih Kategori Customer</option>
                        <option value="1">Biasa</option>
                        <option value="3">Private</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="clubhouse_id">Clubhouse</label>
                    <select name="clubhouse_id" id="clubhouse_id">
                        <option value="">Pilih Clubhouse</option>
                        @foreach ($clubhouses as $clubhouse)
                            <option value="{{ $clubhouse->id }}">{{ $clubhouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="catatan">Catatan</label>
                    <input type="text" id="catatan" name="catatan">
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

                // show
                setTimeout(() => alertDiv.classList.add('show'), 50);

                // auto hide
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
        });
    </script>
@endsection
