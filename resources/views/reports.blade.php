@extends('layouts.app')

@section('title', 'Reports & Analytics - Tracer Study')
@section('header', 'Reports & Analytics')

@section('content')


<div class="container-fluid">

    {{-- ══════════════════════════════════════════════
         FILTER PANEL
    ══════════════════════════════════════════════ --}}
    <div class="filter-panel" style="margin-top:1.5rem;">
        <div class="filter-title">
            <i class="fas fa-filter"></i> Filter Data
        </div>

        <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
            <div class="row align-items-end" style="gap-y:0.5rem;">

                {{-- Universitas --}}
                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                    <label class="filter-label" for="sel_univ">Universitas</label>
                    <select id="sel_univ" name="univ_id" class="form-control">
                        <option value="0">-- Semua Universitas --</option>
                        @foreach($univs as $u)
                            <option value="{{ $u->id }}" {{ $univId == $u->id ? 'selected' : '' }}>
                                {{ $u->nama_univ }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fakultas --}}
                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                    <label class="filter-label" for="sel_fakultas">Fakultas</label>
                    <select id="sel_fakultas" name="fakultas_id" class="form-control" {{ !$univId ? 'disabled' : '' }}>
                        <option value="0">-- Semua Fakultas --</option>
                        @foreach($fakultasList as $f)
                            <option value="{{ $f->id }}" {{ $fakultasId == $f->id ? 'selected' : '' }}>
                                {{ $f->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Program Studi --}}
                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                    <label class="filter-label" for="sel_prodi">Program Studi</label>
                    <select id="sel_prodi" name="prodi_id" class="form-control" {{ !$fakultasId ? 'disabled' : '' }}>
                        <option value="0">-- Semua Prodi --</option>
                        @foreach($prodiList as $p)
                            <option value="{{ $p->id }}" {{ $prodiId == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_prodi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Form --}}
                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                    <label class="filter-label" for="sel_form">Jenis Form</label>
                    <select id="sel_form" name="form_group" class="form-control">
                        <option value="">-- Semua Jenis --</option>
                        @foreach($formGroups as $fg)
                            <option value="{{ $fg }}" {{ $formGroup == $fg ? 'selected' : '' }}>
                                {{ $fg }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun Angkatan --}}
                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                    <label class="filter-label" for="sel_angkatan">Angkatan</label>
                    <select id="sel_angkatan" name="angkatan" class="form-control">
                        <option value="">-- Semua Angkatan --</option>
                        @foreach($angkatans as $ang)
                            <option value="{{ $ang }}" {{ $angkatan == $ang ? 'selected' : '' }}>
                                {{ $ang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-12 col-sm-6 col-lg-4 mb-2 d-flex align-items-end" style="gap:0.5rem;">
                    <button type="submit" class="btn-filter-apply flex-grow-1">
                        <i class="fas fa-search mr-1"></i> Terapkan
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn-filter-reset">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Active filter badges --}}
        @if($activeUniv || $activeFakultas || $activeProdi)
        <div class="mt-2 pt-2" style="border-top:1px dashed #f0e6e6;">
            <small class="text-muted mr-2" style="font-size:0.73rem;">Filter aktif:</small>
            @if($activeUniv)
                <span class="filter-active-badge"><i class="fas fa-university"></i> {{ $activeUniv->nama_univ }}</span>
            @endif
            @if($activeFakultas)
                <span class="filter-active-badge"><i class="fas fa-building"></i> {{ $activeFakultas->nama_fakultas }}</span>
            @endif
            @if($activeProdi)
                <span class="filter-active-badge"><i class="fas fa-graduation-cap"></i> {{ $activeProdi->nama_prodi }}</span>
            @endif
        </div>
        @endif
    </div>

    <!-- Overview Stat Cards -->
    <div class="row mb-4">
        <!-- Total Alumni -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-cascade narrower stat-card h-100">
                <div class="view view-cascade gradient-card-header maroon-gradient d-flex justify-content-between align-items-center px-4 py-3">
                    <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Alumni</h6>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-user-graduate fa-lg text-white"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade d-flex flex-column justify-content-between py-3">
                    <div>
                        <h3 class="font-weight-bold mb-0 text-dark text-center py-2">{{ number_format($totalStudents) }}</h3>
                    </div>
                    <div class="mt-2 text-muted small text-center">
                        <i class="fas fa-info-circle mr-1"></i> Mahasiswa terdaftar
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-cascade narrower stat-card h-100">
                <div class="view view-cascade gradient-card-header green-gradient d-flex justify-content-between align-items-center px-4 py-3">
                    <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.7rem; letter-spacing: 0.05em;">Response Rate</h6>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-check-double fa-lg text-white"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade d-flex flex-column justify-content-between py-3">
                    <div>
                        <h3 class="font-weight-bold mb-0 text-dark text-center py-2">{{ $responseRate }}%</h3>
                    </div>
                    <div class="mt-2 text-muted small text-center">
                        {{ $alumniResponseCount }} dari {{ $totalStudents }} alumni
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Atasan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-cascade narrower stat-card h-100">
                <div class="view view-cascade gradient-card-header peach-gradient d-flex justify-content-between align-items-center px-4 py-3">
                    <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.7rem; letter-spacing: 0.05em;">Respons Atasan</h6>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-building fa-lg text-white"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade d-flex flex-column justify-content-between py-3">
                    <div>
                        <h3 class="font-weight-bold mb-0 text-dark text-center py-2">{{ number_format($atasanResponseCount) }}</h3>
                    </div>
                    <div class="mt-2 text-muted small text-center">
                        Total Atasan/Perusahaan
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Respons Form -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-cascade narrower stat-card h-100">
                <div class="view view-cascade gradient-card-header blue-gradient-custom d-flex justify-content-between align-items-center px-4 py-3">
                    <h6 class="text-white font-weight-bold text-uppercase mb-0" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Semua Form</h6>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-file-invoice fa-lg text-white"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade d-flex flex-column justify-content-between py-3">
                    <div>
                        <h3 class="font-weight-bold mb-0 text-dark text-center py-2">{{ number_format($totalResponses) }}</h3>
                    </div>
                    <div class="mt-2 text-muted small text-center">
                        Jumlah submisi kuesioner
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Line Chart: Trend Response per Bulan -->
        <div class="col-lg-6 mb-4">
            <div class="card card-cascade narrower mb-4">
                <div class="view view-cascade gradient-card-header purple-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold mb-0">Trend Respons Bulanan</h5>
                        <span class="text-white-50 small">12 bulan terakhir</span>
                    </div>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade">
                    <div class="position-relative" style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Sudah vs Belum Mengisi per Prodi -->
        <div class="col-lg-6 mb-4">
            <div class="card card-cascade narrower mb-4">
                <div class="view view-cascade gradient-card-header blue-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold mb-0">Pengisian Kuesioner per Prodi (Alumni)</h5>
                        <span class="text-white-50 small">Perbandingan sudah vs belum mengisi</span>
                    </div>
                    <div class="icon-wrapper-new">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                </div>
                <div class="card-body card-body-cascade">
                    <div class="position-relative" style="height: 300px;">
                        <canvas id="prodiCompareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Charts Section -->
    <div class="mb-5">
        <h4 class="font-weight-bold text-dark mb-1">Analitik Kuesioner (Dinamis)</h4>
        <p class="text-muted small mb-4">Grafik di bawah ini digenerate secara otomatis berdasarkan pertanyaan pilihan ganda pada form kuesioner aktif.</p>
        
        <div class="row">
            @foreach($dynamicCharts as $chart)
            <div class="col-lg-6 mb-4">
                <div class="card card-cascade narrower mb-4 h-100">
                    <div class="view view-cascade gradient-card-header unique-color py-3 px-4 text-white">
                        <span class="badge badge-light font-weight-bold text-uppercase px-2 py-1 mb-1" style="font-size: 8px;">{{ $chart['form_title'] }}</span>
                        <h6 class="font-weight-bold text-white mb-0 leading-normal" style="font-size: 0.9rem; line-height: 1.4;">{{ $chart['question_text'] }}</h6>
                    </div>
                    <div class="card-body card-body-cascade">
                        <div class="position-relative d-flex align-items-center justify-content-center" style="min-height: 280px;">
                            <canvas id="{{ $chart['id'] }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if(empty($dynamicCharts))
            <div class="col-12 text-center py-5 bg-white border border-dashed rounded-lg" style="border-width: 2px !important; border-radius: 15px;">
                <div class="w-16 h-16 bg-light rounded-circle flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px; display: flex;">
                    <i class="fas fa-chart-pie fa-2x text-muted m-auto"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-1">Belum Ada Data Analitik</h5>
                <p class="text-muted small">Tidak ada pertanyaan bertipe pilihan ganda (radio/select) yang sudah diisi oleh responden pada form aktif saat ini.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Alumni per angkatan -->
    <div class="card card-cascade narrower mb-4">
        <div class="view view-cascade gradient-card-header peach-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="font-weight-bold mb-0">Alumni per Angkatan</h5>
                <span class="text-white-50 small">Jumlah mahasiswa per tahun masuk</span>
            </div>
            <div class="icon-wrapper-new">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
        </div>
        <div class="card-body card-body-cascade">
            <div class="relative" style="height: 320px;">
                <canvas id="angkatanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Analitik Karir Alumni -->
    <div class="mb-5">
        <h4 class="font-weight-bold text-dark mb-1">Analitik Karir Alumni</h4>
        <p class="text-muted small mb-4">Data berdasarkan jawaban kuesioner tracer study alumni</p>

        <div class="row">
            <!-- 1. Waktu Tunggu Pekerjaan -->
            <div class="col-lg-6 mb-4">
                <div class="card card-cascade narrower mb-4">
                    <div class="view view-cascade gradient-card-header purple-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold mb-0">Waktu Tunggu Mendapatkan Pekerjaan</h5>
                            <span class="text-white-50 small">Distribusi berapa lama alumni menunggu kerja setelah lulus</span>
                        </div>
                        <div class="icon-wrapper-new">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="card-body card-body-cascade">
                        <div class="relative" style="height:300px">
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
                            <span class="text-white-50 small">Sebaran alumni berdasarkan skala perusahaan tempat bekerja</span>
                        </div>
                        <div class="icon-wrapper-new">
                            <i class="fas fa-globe-americas"></i>
                        </div>
                    </div>
                    <div class="card-body card-body-cascade">
                        <div class="relative d-flex align-items-center justify-content-center" style="height:300px">
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
                            <h5 class="font-weight-bold mb-0">Distribusi Pendapatan Alumni</h5>
                            <span class="text-white-50 small">Rentang pendapatan per bulan (Rupiah)</span>
                        </div>
                        <div class="icon-wrapper-new">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                    <div class="card-body card-body-cascade">
                        <div class="relative" style="height:300px">
                            <canvas id="pendapatanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Kesesuaian Pekerjaan dengan Prodi -->
            <div class="col-lg-6 mb-4">
                <div class="card card-cascade narrower mb-4">
                    <div class="view view-cascade gradient-card-header blue-gradient py-3 px-4 text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold mb-0">Kesesuaian Pekerjaan dengan Program Studi</h5>
                            <span class="text-white-50 small">Seberapa relevan pekerjaan alumni dengan jurusannya</span>
                        </div>
                        <div class="icon-wrapper-new">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                    </div>
                    <div class="card-body card-body-cascade">
                        <div class="relative d-flex align-items-center justify-content-center" style="height:300px">
                            <canvas id="kesesuaianChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ─────────────────────────────────────────────────────────────
//  CASCADE DROPDOWN FILTER (Univ → Fakultas → Prodi)
// ─────────────────────────────────────────────────────────────
(function () {
    const selUniv     = document.getElementById('sel_univ');
    const selFakultas = document.getElementById('sel_fakultas');
    const selProdi    = document.getElementById('sel_prodi');
    const filterUrl   = '{{ route("reports.filterOptions") }}';

    function fetchOptions(type, parentId, targetSelect, currentVal) {
        if (!parentId || parentId === '0') {
            targetSelect.innerHTML = '<option value="0">-- Semua ' + (type === 'fakultas' ? 'Fakultas' : 'Prodi') + ' --</option>';
            targetSelect.disabled = true;
            return;
        }
        fetch(filterUrl + '?type=' + type + '&parent_id=' + parentId)
            .then(r => r.json())
            .then(items => {
                targetSelect.innerHTML = '<option value="0">-- Semua ' + (type === 'fakultas' ? 'Fakultas' : 'Prodi') + ' --</option>';
                items.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.label;
                    if (String(item.id) === String(currentVal)) opt.selected = true;
                    targetSelect.appendChild(opt);
                });
                targetSelect.disabled = false;
            });
    }

    selUniv.addEventListener('change', function () {
        fetchOptions('fakultas', this.value, selFakultas, 0);
        selProdi.innerHTML = '<option value="0">-- Semua Prodi --</option>';
        selProdi.disabled = true;
    });

    selFakultas.addEventListener('change', function () {
        fetchOptions('prodi', this.value, selProdi, 0);
    });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🎨 Color palette & Gradients
    const maroon = '#800000';
    const emerald = '#059669';
    const amber = '#d97706';
    const violet = '#7c3aed';
    const rose = '#e11d48';
    const cyan = '#0891b2';
    const blue = '#3b82f6';
    const indigo = '#6366f1';
    
    const colors = [emerald, rose, amber, violet, cyan, maroon, blue, '#f97316', '#14b8a6', indigo];

    Chart.defaults.font.family = "'Inter', 'Roboto', sans-serif";
    Chart.defaults.color = '#6b7280';
    
    // Common Tooltip Styling
    const tooltipOptions = {
        backgroundColor: 'rgba(17, 24, 39, 0.9)',
        titleColor: '#f9fafb',
        bodyColor: '#e5e7eb',
        borderColor: 'rgba(255,255,255,0.1)',
        borderWidth: 1,
        cornerRadius: 8,
        padding: 12,
        boxPadding: 6,
        usePointStyle: true,
    };

    // Common Grid Styling
    const gridOptions = {
        color: 'rgba(0, 0, 0, 0.04)',
        borderDash: [5, 5],
        drawBorder: false,
    };

    // Helper: Create Gradient
    function createGradient(ctx, colorStart, colorEnd) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, colorStart);
        gradient.addColorStop(1, colorEnd);
        return gradient;
    }

    // 1. Trend Line Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendGradient = createGradient(trendCtx, 'rgba(128, 0, 0, 0.4)', 'rgba(128, 0, 0, 0.0)');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($monthLabels),
            datasets: [{
                label: 'Jumlah Respons',
                data: @json($monthCounts),
                borderColor: maroon,
                backgroundColor: trendGradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: maroon,
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: maroon,
                pointHoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1500, easing: 'easeOutQuart' },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: tooltipOptions
            },
            scales: {
                y: { beginAtZero: true, grid: gridOptions, border: { display: false } },
                x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 45 } }
            }
        }
    });

    // 2. Bar: Sudah vs Belum per Prodi
    const prodiCtx = document.getElementById('prodiCompareChart').getContext('2d');
    new Chart(prodiCtx, {
        type: 'bar',
        data: {
            labels: @json($prodiLabels),
            datasets: [
                {
                    label: 'Sudah Mengisi',
                    data: @json($sudahMengisi),
                    backgroundColor: createGradient(prodiCtx, emerald, '#10b981'),
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                },
                {
                    label: 'Belum Mengisi',
                    data: @json($belumMengisi),
                    backgroundColor: '#e5e7eb',
                    hoverBackgroundColor: '#d1d5db',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1500, easing: 'easeOutQuart' },
            plugins: {
                legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, padding: 20 } },
                tooltip: tooltipOptions
            },
            scales: {
                y: { beginAtZero: true, stacked: true, grid: gridOptions, border: { display: false } },
                x: { stacked: true, grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 3. Dynamic Charts Generation
    const dynamicChartsData = @json($dynamicCharts);
    dynamicChartsData.forEach((chartData, index) => {
        const ctx = document.getElementById(chartData.id).getContext('2d');
        const type = chartData.labels.length > 5 ? 'bar' : 'doughnut';
        
        let bgColors = colors;
        if (type === 'bar') {
            bgColors = createGradient(ctx, indigo, blue);
        }

        new Chart(ctx, {
            type: type,
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Total Pemilih',
                    data: chartData.data,
                    backgroundColor: bgColors,
                    borderColor: '#ffffff',
                    borderWidth: type === 'doughnut' ? 3 : 0,
                    borderRadius: type === 'bar' ? 6 : 0,
                    hoverOffset: type === 'doughnut' ? 10 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1500, easing: 'easeOutQuart' },
                cutout: type === 'doughnut' ? '65%' : undefined,
                plugins: {
                    legend: {
                        display: type === 'doughnut',
                        position: 'right',
                        labels: { usePointStyle: true, padding: 15, font: { size: 11 } }
                    },
                    tooltip: tooltipOptions
                },
                scales: type === 'bar' ? {
                    y: { beginAtZero: true, grid: gridOptions, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 45 } }
                } : undefined
            }
        });
    });

    // 4. Bar: Alumni per Angkatan
    const angkatanData = @json($angkatanData);
    const angkatanCtx = document.getElementById('angkatanChart').getContext('2d');
    new Chart(angkatanCtx, {
        type: 'bar',
        data: {
            labels: angkatanData.map(d => d.angkatan),
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: angkatanData.map(d => d.total),
                backgroundColor: createGradient(angkatanCtx, cyan, '#06b6d4'),
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1500, easing: 'easeOutBounce' },
            plugins: {
                legend: { display: false },
                tooltip: tooltipOptions
            },
            scales: {
                y: { beginAtZero: true, grid: gridOptions, border: { display: false } },
                x: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: 'bold' } } }
            }
        }
    });

    // 5. Waktu Tunggu (Bar)
    const wtCtx = document.getElementById('waktuTungguChart').getContext('2d');
    new Chart(wtCtx, {
        type: 'bar',
        data: {
            labels: @json($waktuTungguLabels),
            datasets: [{
                label: 'Jumlah Alumni',
                data: @json($waktuTungguData),
                backgroundColor: [
                    createGradient(wtCtx, emerald, '#10b981'),
                    createGradient(wtCtx, cyan, '#06b6d4'),
                    createGradient(wtCtx, amber, '#f59e0b'),
                    createGradient(wtCtx, violet, '#8b5cf6'),
                    createGradient(wtCtx, rose, '#f43f5e')
                ],
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.5,
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            animation: { duration: 1500, easing: 'easeOutQuart' },
            plugins: { legend: { display: false }, tooltip: tooltipOptions },
            scales: {
                y: { beginAtZero: true, grid: gridOptions, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 6. Skala Tempat Kerja (Doughnut)
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
                backgroundColor: skalaEmpty ? ['#f3f4f6'] : [emerald, cyan, violet, rose, amber],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 12,
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '65%',
            animation: { animateScale: true, animateRotate: true, duration: 1500 },
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, padding: 15 } },
                tooltip: tooltipOptions
            }
        }
    });

    // 7. Pendapatan (Bar horizontal)
    const pendCtx = document.getElementById('pendapatanChart').getContext('2d');
    const pendapatanRaw = @json($pendapatanData);
    const orderedSalaryLabels = ['< 1.000.000','1.000.000 - 5.000.000','5.000.000 - 10.000.000','10.000.000 - 20.000.000','> 20.000.000'];
    const pendapatanLabels = orderedSalaryLabels.filter(l => pendapatanRaw[l] !== undefined);
    const pendapatanValues = pendapatanLabels.map(l => pendapatanRaw[l]);
    new Chart(pendCtx, {
        type: 'bar',
        data: {
            labels: pendapatanLabels.length ? pendapatanLabels : ['Belum ada data'],
            datasets: [{
                label: 'Jumlah Alumni',
                data: pendapatanValues.length ? pendapatanValues : [0],
                backgroundColor: createGradient(pendCtx, maroon, '#dc2626'),
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, 
            maintainAspectRatio: false,
            animation: { duration: 1500, easing: 'easeOutQuart' },
            plugins: { legend: { display: false }, tooltip: tooltipOptions },
            scales: {
                x: { beginAtZero: true, grid: gridOptions, border: { display: false } },
                y: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 8. Kesesuaian Pekerjaan (Pie)
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
                backgroundColor: kesesuaianEmpty ? ['#f3f4f6'] : kesesuaianColors.slice(0, kesesuaianLabels.length),
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 12,
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '60%',
            animation: { animateScale: true, animateRotate: true, duration: 1500 },
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, padding: 15 } },
                tooltip: tooltipOptions
            }
        }
    });
});
</script>
@endpush
