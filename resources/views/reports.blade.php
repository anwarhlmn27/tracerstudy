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
    // Color palette
    const maroon = '#800000';
    const maroonLight = 'rgba(128, 0, 0, 0.15)';
    const emerald = '#059669';
    const amber = '#d97706';
    const violet = '#7c3aed';
    const rose = '#e11d48';
    const cyan = '#0891b2';
    
    const colors = [emerald, rose, amber, violet, cyan, maroon, '#3b82f6', '#f97316', '#14b8a6', '#6366f1'];

    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.color = '#6b7280';

    // 1. Trend Line Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: @json($monthLabels),
            datasets: [{
                label: 'Jumlah Respons',
                data: @json($monthCounts),
                borderColor: maroon,
                backgroundColor: maroonLight,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: maroon,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#d1d5db',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: '#f3f4f6' },
                    border: { display: false }
                },
                x: {
                    ticks: { font: { size: 10 }, maxRotation: 45 },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });

    // 2. Bar: Sudah vs Belum per Prodi
    new Chart(document.getElementById('prodiCompareChart'), {
        type: 'bar',
        data: {
            labels: @json($prodiLabels),
            datasets: [
                {
                    label: 'Sudah Mengisi',
                    data: @json($sudahMengisi),
                    backgroundColor: emerald,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Belum Mengisi',
                    data: @json($belumMengisi),
                    backgroundColor: '#e5e7eb',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, pointStyleWidth: 12, font: { size: 11 }, padding: 16 }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    cornerRadius: 8,
                    padding: 12,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: '#f3f4f6' },
                    border: { display: false }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });

    // 3. Dynamic Charts Generation
    const dynamicChartsData = @json($dynamicCharts);
    dynamicChartsData.forEach((chartData, index) => {
        const type = chartData.labels.length > 5 ? 'bar' : 'doughnut';
        
        new Chart(document.getElementById(chartData.id), {
            type: type,
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Total Pemilih',
                    data: chartData.data,
                    backgroundColor: type === 'bar' ? maroon : colors,
                    borderColor: type === 'bar' ? maroon : '#fff',
                    borderWidth: type === 'bar' ? 0 : 2,
                    borderRadius: type === 'bar' ? 6 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type !== 'bar',
                        position: 'right',
                        labels: { usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        cornerRadius: 8,
                        padding: 12,
                    }
                },
                scales: type === 'bar' ? {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, minRotation: 45 } }
                } : undefined
            }
        });
    });

    // 4. Bar: Alumni per Angkatan
    const angkatanData = @json($angkatanData);
    new Chart(document.getElementById('angkatanChart'), {
        type: 'bar',
        data: {
            labels: angkatanData.map(d => d.angkatan),
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: angkatanData.map(d => d.total),
                backgroundColor: cyan,
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
                x: { ticks: { font: { size: 12, weight: 500 } }, grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 5. Waktu Tunggu (Bar)
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

    // 7. Pendapatan (Bar horizontal)
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
