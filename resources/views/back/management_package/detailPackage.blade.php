@extends('main.back_blank')
@section('title', 'Detail Data Package Customer')

@section('content')
    <div class="ticket-types-page">
        <h2 class="page-title mb-4">Detail Data Package Customer</h2>
        @if(isset($customer))
            <div class="detail-info-section">
                <div class="detail-info-header">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <h3>Detail Customer</h3>
                </div>
                <div class="detail-info-content">
                    <div class="detail-info-grid">
                        <!-- Nama -->
                        <div class="detail-info-item">
                            <div class="detail-info-icon blue">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="detail-info-text">
                                <p class="detail-info-label">Nama Lengkap</p>
                                <p class="detail-info-value">{{ $customer->name }}</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="detail-info-item">
                            <div class="detail-info-icon green">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="detail-info-text">
                                <p class="detail-info-label">Nomor Telepon</p>
                                <p class="detail-info-value">{{ $customer->phone }}</p>
                            </div>
                        </div>

                        <!-- DOB -->
                        <div class="detail-info-item">
                            <div class="detail-info-icon purple">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="detail-info-text">
                                <p class="detail-info-label">Tanggal Lahir</p>
                                <p class="detail-info-value">
                                    {{ \Carbon\Carbon::parse($customer->DOB)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(isset($viewDetailsData) && count($viewDetailsData) > 0)
            <div class="table-section mt-2 relative">
                <div class="table-scroll-container">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">Id Tiket</th>
                                    <th class="text-center">Status Tiket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewDetailsData as $data)
                                    <tr>
                                        <td class="text-center">{{ $data['id'] }}</td>
                                        <td class="text-center">
                                            {{ $data['status_ticket'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <div class="mt-4">
                    {{ $viewDetailsData->links() }}
                </div> --}}
            </div>
        @else
            <p class="text-center mt-4">Tidak ada data untuk ditampilkan.</p>
        @endif
    </div>
@endsection