<div class="detail-info-section">
    <div class="detail-info-header">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        <h3>Informasi Promo</h3>
    </div>

    <div class="detail-info-content">
        <div class="detail-info-grid">
            {{-- Kode Promo --}}
            <div class="detail-info-item">
                <div class="detail-info-icon blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <div class="detail-info-text">
                    <div class="detail-info-label">Kode Promo</div>
                    <div class="detail-info-value">{{ $promo->code }}</div>
                </div>
            </div>

            {{-- Status --}}
            <div class="detail-info-item">
                <div class="detail-info-icon {{ $promo->is_active ? 'green' : 'red' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="detail-info-text">
                    <div class="detail-info-label">Status</div>
                    <div class="detail-info-value">
                        @if ($promo->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tipe & Nilai --}}
            <div class="detail-info-item">
                <div class="detail-info-icon yellow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="detail-info-text">
                    <div class="detail-info-label">Nilai Promo</div>
                    <div class="detail-info-value">
                        @if ($promo->type == 1)
                            {{ $promo->value }}%
                        @else
                            Rp {{ number_format($promo->value, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kuota --}}
            <div class="detail-info-item">
                <div class="detail-info-icon purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="detail-info-text">
                    <div class="detail-info-label">Sisa Kuota</div>
                    <div class="detail-info-value">{{ $promo->quota }}</div>
                </div>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="detail-info-item">
                <div class="detail-info-icon blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="detail-info-text">
                    <div class="detail-info-label">Tanggal Mulai</div>
                    <div class="detail-info-value">
                        {{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            {{-- Tanggal Berakhir --}}
            <div class="detail-info-item">
                <div class="detail-info-icon red">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="detail-info-text">
                    <div class="detail-info-label">Tanggal Berakhir</div>
                    <div class="detail-info-value">
                        {{ \Carbon\Carbon::parse($promo->expired_date)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="detail-info-section mt-4">
    <div class="detail-info-header">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3>Deskripsi & Ketentuan</h3>
    </div>
    <div class="detail-info-content">
        <div class="detail-info-grid">
            <div class="detail-info-item w-full">
                <div class="detail-info-text">
                    <div class="detail-info-label">Deskripsi</div>
                    <div class="detail-info-value text-base font-normal">
                        {{ $promo->description ?? 'Tidak ada deskripsi' }}
                    </div>
                </div>
            </div>

            <div class="detail-info-item">
                <div class="detail-info-text">
                    <div class="detail-info-label">Minimal Pembelian</div>
                    <div class="detail-info-value">
                        Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="detail-info-item">
                <div class="detail-info-text">
                    <div class="detail-info-label">Maksimal Diskon</div>
                    <div class="detail-info-value">
                        Rp {{ number_format($promo->max_discount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="detail-info-section mt-4">
    <div class="detail-info-header">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
        <h3>Tiket yang Berlaku</h3>
    </div>
    <div class="detail-info-content">
        @if (count($ticketNames) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach ($ticketNames as $name)
                    <span class="badge bg-primary fs-6">{{ $name }}</span>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">Tidak ada tiket spesifik (Berlaku untuk semua?)</p>
        @endif
    </div>
</div>
