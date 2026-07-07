@extends('layouts.app')

@section('title', 'Dashboard - Tracer Study')
@section('header', 'Dashboard Overview')

@section('content')


<!-- Stat Cards -->
<div class="row mb-4" style="margin-top: 1.5rem;">
    <!-- Stat Card 1 -->
    <div class="col-md-4 mb-4">
        <div class="card card-cascade narrower stat-card">
            <div class="view view-cascade gradient-card-header maroon-gradient d-flex justify-content-between align-items-center px-4 py-3">
                <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.75rem; letter-spacing: 0.1em;">Total Alumni</h6>
                <div class="icon-wrapper-new">
                    <i class="fas fa-users fa-lg text-white"></i>
                </div>
            </div>
            <div class="card-body card-body-cascade text-center py-4">
                <h2 class="font-weight-bold mb-0 text-dark">{{ number_format($totalAlumni) }}</h2>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="col-md-4 mb-4">
        <div class="card card-cascade narrower stat-card">
            <div class="view view-cascade gradient-card-header green-gradient d-flex justify-content-between align-items-center px-4 py-3">
                <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.75rem; letter-spacing: 0.1em;">Response Rate (Alumni)</h6>
                <div class="icon-wrapper-new">
                    <i class="fas fa-check-circle fa-lg text-white"></i>
                </div>
            </div>
            <div class="card-body card-body-cascade text-center py-4">
                <h2 class="font-weight-bold mb-0 text-dark">{{ $responseRate }}%</h2>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="col-md-4 mb-4">
        <div class="card card-cascade narrower stat-card">
            <div class="view view-cascade gradient-card-header blue-gradient-custom d-flex justify-content-between align-items-center px-4 py-3">
                <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.75rem; letter-spacing: 0.1em;">Kuesioner Aktif</h6>
                <div class="icon-wrapper-new">
                    <i class="fas fa-file-alt fa-lg text-white"></i>
                </div>
            </div>
            <div class="card-body card-body-cascade text-center py-4">
                <h2 class="font-weight-bold mb-0 text-dark">
                    {{ $activeForms }} <span style="font-size: 1rem; font-weight: normal; opacity: 0.7;">/ {{ $totalForms }}</span>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Career Analytics Section -->
<div class="mb-5">
    <h4 class="font-weight-bold text-dark mb-1">Analitik Karir Alumni</h4>
    <p class="text-muted small">Ringkasan performa lulusan berdasarkan tracer study</p>

    <div class="row">
        <!-- 1. Waktu Tunggu Pekerjaan -->
        <div class="col-lg-6 mb-4">
            <div class="card card-cascade narrower mb-4">
                <div class="view view-cascade gradient-card-header purple-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold mb-0">Waktu Tunggu Mendapatkan Pekerjaan</h5>
                        <span class="text-white-50 small">Bulan menunggu setelah lulus</span>
                    </div>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade">
                    <div class="position-relative" style="height:300px">
                        <canvas id="waktuTungguChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Skala Tempat Kerja -->
        <div class="col-lg-6 mb-4">
            <div class="card card-cascade narrower mb-4">
                <div class="view view-cascade gradient-card-header aqua-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold mb-0">Skala Tempat Kerja</h5>
                        <span class="text-white-50 small">Sebaran skala perusahaan tempat bekerja</span>
                    </div>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-globe-asia fa-lg"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade">
                    <div class="position-relative d-flex align-items-center justify-content-center" style="height:300px">
                        <canvas id="skalaTempatChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Distribusi Pendapatan -->
        <div class="col-lg-6 mb-4">
            <div class="card card-cascade narrower mb-4">
                <div class="view view-cascade gradient-card-header peach-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold mb-0">Distribusi Pendapatan</h5>
                        <span class="text-white-50 small">Rentang gaji per bulan (Rupiah)</span>
                    </div>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade">
                    <div class="position-relative" style="height:300px">
                        <canvas id="pendapatanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Kesesuaian Pekerjaan -->
        <div class="col-lg-6 mb-4">
            <div class="card card-cascade narrower mb-4">
                <div class="view view-cascade gradient-card-header blue-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold mb-0">Kesesuaian dengan Prodi</h5>
                        <span class="text-white-50 small">Relevansi bidang studi dengan pekerjaan</span>
                    </div>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-award fa-lg"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade">
                    <div class="position-relative d-flex align-items-center justify-content-center" style="height:300px">
                        <canvas id="kesesuaianChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Responses Table -->
<div class="card table-container">
    <div class="card-body p-0">
        <div class="px-4 py-4 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="font-weight-bold text-dark mb-0">Respons Kuesioner Terbaru</h5>
            <a href="{{ route('questionnaires.index') }}" class="font-weight-bold text-primary" style="font-size: 0.9rem; text-decoration: none;">Lihat Semua &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light text-muted" style="font-size: 0.8rem; text-transform: uppercase;">
                    <tr>
                        <th class="border-0 font-weight-bold px-4">Responden</th>
                        <th class="border-0 font-weight-bold">Role</th>
                        <th class="border-0 font-weight-bold">Judul Form</th>
                        <th class="border-0 font-weight-bold">Tanggal Submit</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem;">
                    @forelse($recentResponses as $response)
                    @php
                        $roleTarget = $response->form->target_role ?? '-';
                        $namaResponden = $response->user->name ?? '-';
                        $prodi = '-';
                        if ($roleTarget === 'alumni' && $response->user->student) {
                            $namaResponden = $response->user->student->nama_student;
                            $prodi = $response->user->student->prodi->nama_prodi ?? '-';
                        }
                    @endphp
                    <tr>
                        <td class="px-4 py-3 align-middle">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mr-3 font-weight-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($namaResponden, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $namaResponden }}</div>
                                    @if($roleTarget === 'alumni')
                                        <small class="text-muted">{{ $prodi }}</small>
                                    @else
                                        <small class="text-muted">{{ $response->user->email ?? '-' }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="badge px-3 py-1.5 rounded-pill text-capitalize" style="font-size: 9px; font-weight: 700;
                                {{ $roleTarget === 'alumni' ? 'background-color: rgba(5, 150, 105, 0.15) !important; color: #059669 !important;' : 'background-color: rgba(79, 70, 229, 0.15) !important; color: #4f46e5 !important;' }}">
                                {{ $roleTarget }}
                            </span>
                        </td>
                        <td class="align-middle font-weight-bold text-muted">
                            {{ $response->form->title ?? '-' }}
                        </td>
                        <td class="align-middle">
                            <div>
                                <span class="font-weight-bold text-dark" style="font-size: 0.8rem;">{{ $response->created_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $response->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-5 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 d-block text-muted"></i>
                            Belum ada respons kuesioner yang masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color palette
    const maroon = '#800000';
    const emerald = '#059669';
    const amber = '#d97706';
    const violet = '#7c3aed';
    const rose = '#e11d48';
    const cyan = '#0891b2';
    
    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.color = '#6b7280';

    // 1. Waktu Tunggu (Bar)
    new Chart(document.getElementById('waktuTungguChart'), {
        type: 'bar',
        data: {
            labels: @json($waktuTungguLabels),
            datasets: [{
                label: 'Jumlah Alumni',
                data: @json($waktuTungguData),
                backgroundColor: [emerald, cyan, amber, violet, rose],
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.5,
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1f2937', cornerRadius: 8, padding: 12 }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f3f4f6' }, border: { display: false } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 2. Skala Tempat Kerja (Doughnut)
    const skalaTempatRaw = @json($skalaTempat);
    const skalaLabels = Object.keys(skalaTempatRaw).map(l => l.charAt(0).toUpperCase() + l.slice(1));
    const skalaData   = Object.values(skalaTempatRaw);
    const skalaEmpty  = skalaData.every(v => v === 0);
    new Chart(document.getElementById('skalaTempatChart'), {
        type: 'doughnut',
        data: {
            labels: skalaEmpty ? ['Belum ada data'] : skalaLabels,
            datasets: [{
                data: skalaEmpty ? [1] : skalaData,
                backgroundColor: skalaEmpty ? ['#e5e7eb'] : [emerald, cyan, violet],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, pointStyleWidth: 10, font: { size: 12 }, padding: 16 } },
                tooltip: { backgroundColor: '#1f2937', cornerRadius: 8, padding: 12 }
            }
        }
    });

    // 3. Pendapatan (Bar horizontal)
    const pendapatanRaw = @json($pendapatanData);
    const orderedSalaryLabels = ['< 1.000.000','1.000.000 - 5.000.000','5.000.000 - 10.000.000','10.000.000 - 20.000.000','> 20.000.000'];
    const pendapatanLabels = orderedSalaryLabels.filter(l => pendapatanRaw[l] !== undefined);
    const pendapatanValues = pendapatanLabels.map(l => pendapatanRaw[l]);
    new Chart(document.getElementById('pendapatanChart'), {
        type: 'bar',
        data: {
            labels: pendapatanLabels.length ? pendapatanLabels : ['Belum ada data'],
            datasets: [{
                label: 'Jumlah Alumni',
                data: pendapatanValues.length ? pendapatanValues : [0],
                backgroundColor: [maroon, '#b91c1c', '#dc2626', '#ef4444', '#f87171'],
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.5,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1f2937', cornerRadius: 8, padding: 12 }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f3f4f6' }, border: { display: false } },
                y: { ticks: { font: { size: 11 } }, grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 4. Kesesuaian Pekerjaan (Pie)
    const kesesuaianRaw = @json($kesesuaianData);
    const kesesuaianOrder = ['Sangat Sesuai', 'Sesuai', 'Kurang Sesuai', 'Tidak Sesuai'];
    const kesesuaianColors = [emerald, cyan, amber, rose];
    const kesesuaianLabels = kesesuaianOrder.filter(l => kesesuaianRaw[l] !== undefined);
    const kesesuaianValues = kesesuaianLabels.map(l => kesesuaianRaw[l]);
    const kesesuaianEmpty  = kesesuaianValues.every(v => v === 0) || kesesuaianLabels.length === 0;
    new Chart(document.getElementById('kesesuaianChart'), {
        type: 'doughnut',
        data: {
            labels: kesesuaianEmpty ? ['Belum ada data'] : kesesuaianLabels,
            datasets: [{
                data: kesesuaianEmpty ? [1] : kesesuaianValues,
                backgroundColor: kesesuaianEmpty ? ['#e5e7eb'] : kesesuaianColors.slice(0, kesesuaianLabels.length),
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, pointStyleWidth: 10, font: { size: 12 }, padding: 16 } },
                tooltip: { backgroundColor: '#1f2937', cornerRadius: 8, padding: 12 }
            }
        }
    });
});
</script>
@endpush
