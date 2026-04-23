@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('breadcrumb', 'Beranda / Dashboard')

@push('styles')
<style>
    /* ── Dashboard Grid ── */
    .db-grid-top { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .db-grid-mid { display:grid; grid-template-columns:2fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    .db-grid-bot { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    @media (max-width:1100px) { .db-grid-mid,.db-grid-bot { grid-template-columns:1fr; } }

    /* ── Stat Card ── */
    .stat-card-v2 {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 2px 10px rgba(34,139,34,0.06);
        display: flex; flex-direction: column; gap: 0.25rem;
        position: relative; overflow: hidden;
    }
    .stat-card-v2::after {
        content:''; position:absolute; bottom:0; left:0; right:0; height:3px;
        background: linear-gradient(90deg, var(--accent), #16a34a);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .stat-card-v2:hover::after { opacity: 1; }
    .stat-label-v2 { font-size:0.7rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
    .stat-val-v2   { font-size:1.75rem; font-weight:800; color:var(--text); line-height:1; }
    .stat-sub-v2   { font-size:0.71rem; color:var(--muted); }
    .stat-trend    { font-size:0.7rem; font-weight:600; margin-top:0.25rem; }
    .stat-trend.up   { color: var(--success); }
    .stat-trend.down { color: var(--danger); }
    .stat-trend.flat { color: var(--muted); }
    .stat-icon-v2 {
        position:absolute; top:1rem; right:1.1rem;
        width:38px; height:38px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
    }
    .stat-icon-v2 svg { width:18px; height:18px; }
    .si-green { background:rgba(34,166,69,0.1); color:var(--accent); }
    .si-warn  { background:rgba(217,119,6,0.1);  color:#d97706; }
    .si-blue  { background:rgba(59,130,246,0.1); color:#3b82f6; }
    .si-red   { background:rgba(220,38,38,0.08); color:#dc2626; }
    .si-gray  { background:rgba(26,46,26,0.07);  color:var(--muted); }
    .si-purple{ background:rgba(139,92,246,0.1); color:#8b5cf6; }

    /* ── Panel ── */
    .db-panel {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 14px; padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 8px rgba(34,139,34,0.04);
    }
    .db-panel-hd {
        font-size:0.82rem; font-weight:600; color:var(--text);
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:.875rem; padding-bottom:.65rem; border-bottom:1px solid var(--border);
    }
    .db-panel-hd-left { display:flex; align-items:center; gap:0.45rem; }
    .db-panel-hd svg  { width:15px; height:15px; color:var(--accent); }

    /* ── Alert pending ── */
    .alert-queue {
        display:flex; align-items:center; gap:0.75rem;
        background: linear-gradient(135deg, rgba(217,119,6,0.08), rgba(217,119,6,0.04));
        border: 1px solid rgba(217,119,6,0.25); border-radius:12px;
        padding:0.875rem 1.1rem; margin-bottom:1.25rem;
    }
    .alert-queue-icon { width:40px; height:40px; background:rgba(217,119,6,0.12); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .alert-queue-icon svg { width:20px; height:20px; color:#d97706; }
    .alert-queue-text { flex:1; }
    .alert-queue-text strong { font-size:0.9rem; color:var(--text); display:block; }
    .alert-queue-text span   { font-size:0.78rem; color:var(--muted); }

    /* ── Pengaduan List ── */
    .pengaduan-row {
        display:flex; gap:0.75rem; padding:0.65rem 0;
        border-bottom:1px solid rgba(34,139,34,0.07); align-items:flex-start;
    }
    .pengaduan-row:last-child { border-bottom:none; }
    .p-status-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:5px; }
    .p-status-dot.pending  { background:#d97706; }
    .p-status-dot.proses   { background:#3b82f6; }
    .p-row-content { flex:1; min-width:0; }
    .p-row-name { font-size:0.83rem; font-weight:600; color:var(--text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .p-row-meta { font-size:0.71rem; color:var(--muted); margin-top:1px; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- Alert Chat Queue --}}
    @if($chatQueue > 0)
    <div class="alert-queue">
        <div class="alert-queue-icon">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        </div>
        <div class="alert-queue-text">
            <strong>💬 {{ $chatQueue }} siswa menunggu di antrean chat</strong>
            <span>Segera tangani sebelum siswa menunggu terlalu lama.</span>
        </div>
        <a href="{{ route('admin.chat.index') }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">
            Buka Chat →
        </a>
    </div>
    @endif

    {{-- ── STAT CARDS ── --}}
    <div class="db-grid-top">
        <div class="stat-card-v2">
            <div class="stat-icon-v2 si-green">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            </div>
            <div class="stat-label-v2">Total Pengaduan</div>
            <div class="stat-val-v2">{{ $totalPengaduan }}</div>
            <div class="stat-sub-v2">Semua waktu</div>
            <div class="stat-trend {{ $trendPct > 0 ? 'up' : ($trendPct < 0 ? 'down' : 'flat') }}">
                {{ $trendPct > 0 ? '↑' : ($trendPct < 0 ? '↓' : '→') }} {{ abs($trendPct) }}% vs bulan lalu
            </div>
        </div>
        <div class="stat-card-v2">
            <div class="stat-icon-v2 si-warn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div class="stat-label-v2">Pending</div>
            <div class="stat-val-v2">{{ $pendingCount }}</div>
            <div class="stat-sub-v2">Belum ditangani</div>
        </div>
        <div class="stat-card-v2">
            <div class="stat-icon-v2 si-blue">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
            </div>
            <div class="stat-label-v2">Diproses</div>
            <div class="stat-val-v2">{{ $prosesCount }}</div>
            <div class="stat-sub-v2">Sedang berjalan</div>
        </div>
        <div class="stat-card-v2">
            <div class="stat-icon-v2 si-green">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div class="stat-label-v2">Selesai</div>
            <div class="stat-val-v2">{{ $selesaiCount }}</div>
            <div class="stat-sub-v2">Sudah tuntas</div>
        </div>
        <div class="stat-card-v2">
            <div class="stat-icon-v2 si-gray">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            </div>
            <div class="stat-label-v2">Total Siswa</div>
            <div class="stat-val-v2">{{ $totalSiswa }}</div>
            <div class="stat-sub-v2">Pengguna aktif</div>
        </div>
        <div class="stat-card-v2">
            <div class="stat-icon-v2 si-purple">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5S15.01 22 17.5 22s4.5-2.01 4.5-4.5S19.99 13 17.5 13zm0 7c-1.38 0-2.5-1.12-2.5-2.5S16.12 15 17.5 15s2.5 1.12 2.5 2.5S18.88 20 17.5 20zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>
            </div>
            <div class="stat-label-v2">Kategori</div>
            <div class="stat-val-v2">{{ $totalKategori }}</div>
            <div class="stat-sub-v2">Jenis laporan</div>
        </div>
    </div>

    {{-- ── CHART: Line 30 Hari + Doughnut Status ── --}}
    <div class="db-grid-mid">
        {{-- Line Chart 30 Hari --}}
        <div class="db-panel">
            <div class="db-panel-hd">
                <div class="db-panel-hd-left">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                    Tren Pengaduan — 30 Hari Terakhir
                </div>
                <span style="font-size:0.72rem; color:var(--muted);">Masuk per hari</span>
            </div>
            <div style="position:relative; height:220px;">
                <canvas id="lineChart30"></canvas>
            </div>
        </div>

        {{-- Doughnut Status --}}
        <div class="db-panel">
            <div class="db-panel-hd">
                <div class="db-panel-hd-left">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.5-4.25 8.97-8.99h-8.97z"/></svg>
                    Distribusi Status
                </div>
            </div>
            <div style="position:relative; height:180px;">
                <canvas id="doughnutStatus"></canvas>
            </div>
            <div style="display:flex; gap:1rem; justify-content:center; margin-top:0.5rem; flex-wrap:wrap;">
                <span style="font-size:0.72rem; color:var(--muted); display:flex; align-items:center; gap:0.3rem;"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#d97706;"></span>Pending ({{ $pendingCount }})</span>
                <span style="font-size:0.72rem; color:var(--muted); display:flex; align-items:center; gap:0.3rem;"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3b82f6;"></span>Proses ({{ $prosesCount }})</span>
                <span style="font-size:0.72rem; color:var(--muted); display:flex; align-items:center; gap:0.3rem;"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#22a645;"></span>Selesai ({{ $selesaiCount }})</span>
            </div>
        </div>
    </div>

    {{-- ── CHART: Kategori + Kondisi + Tren 12 Bulan ── --}}
    <div class="db-grid-bot">
        {{-- Bar Kategori --}}
        <div class="db-panel">
            <div class="db-panel-hd">
                <div class="db-panel-hd-left">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg>
                    Pengaduan per Kategori
                </div>
            </div>
            <div style="position:relative; height:180px;">
                <canvas id="barKategori"></canvas>
            </div>
        </div>

        {{-- Kondisi Chart --}}
        <div class="db-panel">
            <div class="db-panel-hd">
                <div class="db-panel-hd-left">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                    Kondisi (Bulan Ini)
                </div>
            </div>
            <div style="position:relative; height:180px;">
                <canvas id="barKondisi"></canvas>
            </div>
        </div>

        {{-- Tren 12 Bulan --}}
        <div class="db-panel">
            <div class="db-panel-hd">
                <div class="db-panel-hd-left">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-1.99.9-1.99 2L3 19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/></svg>
                    Tren 12 Bulan
                </div>
            </div>
            <div style="position:relative; height:180px;">
                <canvas id="tren12"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Pengaduan Perlu Perhatian ── --}}
    <div class="db-panel">
        <div class="db-panel-hd">
            <div class="db-panel-hd-left">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                Pengaduan Perlu Perhatian
            </div>
            <a href="{{ route('admin.pengaduan.index', ['status' => 'pending']) }}"
               style="font-size:0.72rem; color:var(--accent); text-decoration:none; font-weight:500;">
                Lihat Semua Pending →
            </a>
        </div>
        @if($recentPengaduan->isEmpty())
            <div style="padding:2rem; text-align:center; color:var(--muted); font-size:0.83rem;">
                🎉 Tidak ada pengaduan yang menunggu — semua sudah ditangani!
            </div>
        @else
            @foreach($recentPengaduan as $p)
            <div class="pengaduan-row">
                <div class="p-status-dot {{ $p->status }}"></div>
                <div class="p-row-content">
                    <div class="p-row-name">{{ $p->nama_pengaduan }}</div>
                    <div class="p-row-meta">
                        {{ $p->is_anonim ? '🔒 Anonim' : ($p->siswa->nama ?? '-') }}
                        &bull; {{ $p->kategori->nama_kategori ?? '-' }}
                        &bull; Kondisi: {{ ucfirst($p->kondisi_pengaduan) }}
                        &bull; {{ $p->created_at->diffForHumans() }}
                    </div>
                </div>
                <a href="{{ route('admin.pengaduan.show', $p) }}" class="btn btn-ghost btn-sm" style="white-space:nowrap;">
                    Tangani →
                </a>
            </div>
            @endforeach
        @endif
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const C = {
        green:  '#22a645',
        warn:   '#d97706',
        blue:   '#3b82f6',
        red:    '#dc2626',
        purple: '#8b5cf6',
        muted:  'rgba(26,46,26,0.4)',
    };
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = C.muted;

    // 1. Line 30 hari
    new Chart(document.getElementById('lineChart30'), {
        type: 'line',
        data: {
            labels: @json($days30labels),
            datasets: [{
                label: 'Pengaduan',
                data: @json($days30vals),
                borderColor: C.green,
                backgroundColor: 'rgba(34,166,69,0.08)',
                borderWidth: 2,
                pointRadius: 2,
                pointBackgroundColor: C.green,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(26,46,26,0.05)' } },
                x: {
                    ticks: {
                        font: { size: 9 }, maxTicksLimit: 10,
                        callback: function(val, i) { return i % 3 === 0 ? this.getLabelForValue(val) : ''; }
                    },
                    grid: { display: false }
                },
            }
        }
    });

    // 2. Doughnut Status
    new Chart(document.getElementById('doughnutStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Proses', 'Selesai'],
            datasets: [{
                data: [{{ $statusChart['pending'] }}, {{ $statusChart['proses'] }}, {{ $statusChart['selesai'] }}],
                backgroundColor: [C.warn, C.blue, C.green],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    // 3. Bar Kategori
    const katKeys = @json(array_keys($chartKategori->toArray()));
    const katVals = @json(array_values($chartKategori->toArray()));
    new Chart(document.getElementById('barKategori'), {
        type: 'bar',
        data: {
            labels: katKeys.length ? katKeys : ['-'],
            datasets: [{
                data: katVals.length ? katVals : [0],
                backgroundColor: C.green,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } }, grid: { color: 'rgba(26,46,26,0.05)' } },
                y: { ticks: { font: { size: 9 } }, grid: { display: false } }
            }
        }
    });

    // 4. Bar Kondisi
    const kondisiKeys = @json(array_keys($kondisiChart->toArray()));
    const kondisiVals = @json(array_values($kondisiChart->toArray()));
    const kondisiColors = kondisiKeys.map(k => k === 'berat' ? C.red : (k === 'sedang' ? C.warn : C.blue));
    new Chart(document.getElementById('barKondisi'), {
        type: 'bar',
        data: {
            labels: kondisiKeys.length ? kondisiKeys.map(k => k.charAt(0).toUpperCase()+k.slice(1)) : ['Kosong'],
            datasets: [{
                data: kondisiVals.length ? kondisiVals : [0],
                backgroundColor: kondisiKeys.length ? kondisiColors : ['rgba(26,46,26,0.08)'],
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } }, grid: { color: 'rgba(26,46,26,0.05)' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // 5. Tren 12 Bulan
    new Chart(document.getElementById('tren12'), {
        type: 'bar',
        data: {
            labels: @json($tren12labels),
            datasets: [{
                label: 'Pengaduan',
                data: @json($tren12vals),
                backgroundColor: 'rgba(34,166,69,0.65)',
                borderRadius: 3,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } }, grid: { color: 'rgba(26,46,26,0.05)' } },
                x: {
                    ticks: { font: { size: 8 }, maxRotation: 45 },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
