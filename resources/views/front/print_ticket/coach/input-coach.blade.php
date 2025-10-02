@extends('main.blank')
@section('content')
    @vite('resources/css/front/input-package.css')
    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title">Masukkan No Telepon</h2>

            <form action="" method="POST" class="ticket-form">
                @csrf
                <div class="form-group">
                    <label for="telepon">No Telephon</label>
                    <input type="text" id="telepon" name="telepon" required>
                </div>

                <button type="submit" class="btn-submit">Submit</button>
            </form>
        </div>
    </div>
@endsection