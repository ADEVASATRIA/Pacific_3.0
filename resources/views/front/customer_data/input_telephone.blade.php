@extends('main.blank')

@section('content')
    @vite('resources/css/front/beli_ticket.css')
    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title">Beli Tiket</h2>

            <form action="{{ route('check_customer') }}" method="POST" class="ticket-form">
                @csrf
                <div class="form-group">
                    <label for="phone">No Telephone</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="phone">Nama</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-footer">
                    Belum terdaftar? <a href="{{ route('registrasi_new_customer') }}">Registrasi disini</a>
                </div>

                <button type="submit" class="btn-submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
