@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Dashboard')
@section('breadcrumb', 'Beranda / Dashboard')

@push('styles')
<style>
    /* ── Progress Tracker ── */
    .progress-card {
        background: linear-gradient(135deg, var(--surface) 0%, rgba(34,166,69,0.05) 100%);
        border: 1px solid rgba(34,166,69,0.2);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 4px 20px rgba(34,139,34,0.1);
        position: relative;
        overflow: hidden;
    }
    .progress-card::before {
        content: '';
        position: absolute; top:0; left:0; width:4px; height:100%;
        background: linear-gradient(180deg, var(--accent), #16a34a);
        border-radius: 2px 0 0 2px;
    }
    .p-head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; }
    .p-title-wrap { display: flex; align-items:center; gap:0.75rem; }
    .p-icon {
        width:44px; height:44px; border-radius:12px;
        background: linear-gradient(135deg, var(--accent), #16a34a);
        color:#fff; display:flex; align-items:center; justify-content:center;
        box-shadow:0 4px 12px rgba(34,166,69,0.35); flex-shrink:0;
    }
    .p-icon svg { width:22px; height:22px; }
    .p-title { font-size:1rem; font-weight:700; color:var(--text); }
    .p-subtitle { font-size:0.78rem; color:var(--muted); margin-top:3px; }

    /* Stepper */
    .stepper-wrap { position:relative; padding: 0 1rem; }
    .stepper-line {
        position:absolute; top:20px; left:10%; right:10%; height:3px;
        background:var(--border); z-index:0;
    }
    .stepper-fill {
        position:absolute; top:20px; left:10%; height:3px;
        background:var(--accent); z-index:1; transition:width 0.5s ease;
    }
    .stepper { display:flex; justify-content:space-between; position:relative; z-index:2; max-width:600px; margin:0 auto; }
    .step { text-align:center; flex:1; }
    .step-dot {
        width:40px; height:40px; border-radius:50%;
        background:var(--surface); border:3px solid var(--border);
        display:flex; align-items:center; justify-content:center;
        margin:0 auto 0.5rem; color:var(--muted);
        font-weight:700; font-size:0.85rem; transition:all 0.3s;
    }
    .step-dot svg { width:16px; height:16px; }
    .step.active .step-dot {
        border-color:#3b82f6; background:#3b82f6; color:#fff;
        box-shadow:0 0 0 5px rgba(59,130,246,0.18);
    }
    .step.done .step-dot {
        border-color:var(--accent); background:var(--accent); color:#fff;
    }
    .step-label { font-size:0.78rem; font-weight:600; color:var(--muted); }
    .step.active .step-label { color:#3b82f6; }
    .step.done .step-label { color:var(--accent); }
    .step-note { font-size:0.68rem; color:var(--muted); margin-top:2px; }
    .step.active .step-note { color:rgba(59,130,246,0.8); }

    /* ── Dashboard Grid ── */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.75rem;
    }
    @media (max-width: 1100px) { .dashboard-grid { grid-template-columns: 1fr; } }

    /* ── Panel Box ── */
    .panel-box {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 8px rgba(34,139,34,0.04);
        display: flex;
        flex-direction: column;
    }
    .panel-header {
        font-size: 0.82rem; font-weight: 600; color: var(--text);
        margin-bottom: 0.875rem; display: flex; align-items:center; justify-content:space-between;
        padding-bottom: 0.65rem; border-bottom: 1px solid var(--border);
    }
    .panel-header-left { display:flex; align-items:center; gap:0.45rem; }
    .panel-header svg { width:15px; height:15px; color:var(--accent); flex-shrink:0; }

    /* ── Heatmap Calendar ── */
    .cal-outer { overflow-x:auto; }
    .cal-wrap { display:flex; gap:4px; min-width:min-content; }
    .cal-days-label { display:flex; flex-direction:column; gap:3px; font-size:0.62rem; color:var(--muted); padding-top:18px; padding-right:4px; }
    .cal-days-label span { height:12px; line-height:12px; }
    .cal-grid { display:flex; flex-direction:column; gap:0; }
    .cal-month-labels { display:flex; gap:3px; margin-bottom:4px; }
    .cal-month-label { font-size:0.62rem; color:var(--muted); }
    .cal-weeks { display:flex; gap:3px; }
    .cal-week { display:flex; flex-direction:column; gap:3px; }
    .cal-cell {
        width:12px; height:12px;
        border-radius:2px;
        background:rgba(34,139,34,0.07);
        cursor:default;
        transition:transform 0.12s;
        position:relative;
    }
    .cal-cell:hover { transform:scale(1.3); z-index:5; }
    .cal-cell.empty { background:transparent; }
    .cal-cell[data-v="1"] { background: #bbf7d0; }
    .cal-cell[data-v="2"] { background: #4ade80; }
    .cal-cell[data-v="3"] { background: #22a645; }
    .cal-cell[data-v="4"] { background: #166534; }
    .cal-cell.today { box-shadow: 0 0 0 1.5px var(--accent); }

    /* ── Chart ── */
    .chart-wrap { position:relative; flex:1; min-height:0; }
    .chart-wrap canvas { display:block; }

    /* ── Multi Chart Grid ── */
    .multi-chart-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.1rem; }
    @media (max-width: 600px) { .multi-chart-grid { grid-template-columns:1fr; } }

    /* ── Mini Table ── */
    .mini-table { font-size:0.78rem; width:100%; border-collapse:collapse; }
    .mini-table th { padding:0.5rem 0.3rem; color:var(--muted); font-weight:600; text-transform:uppercase; font-size:0.65rem; border-bottom:1px solid var(--border); }
    .mini-table td { padding:0.55rem 0.3rem; border-bottom:1px solid rgba(34,139,34,0.06); }
    .mini-table tbody tr:last-child td { border-bottom:none; }
    .mini-table tbody tr:hover td { background:var(--surface2); }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ──────────────────────────────────────────────── --}}
    {{-- PROGRESS AKTIF                                   --}}
    {{-- ──────────────────────────────────────────────── --}}
    @if($activePengaduan)
    @php
        $s = $activePengaduan->status;
        $fillPct = $s === 'pending' ? '0%' : ($s === 'proses' ? '50%' : '100%');
    @endphp
    <div class="progress-card">
        <div class="p-head">
            <div class="p-title-wrap">
                <div class="p-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
                </div>
                <div>
                    <div class="p-title">🔔 Pengaduan Aktif: {{ $activePengaduan->nama_pengaduan }}</div>
                    <div class="p-subtitle">
                        {{ $activePengaduan->kategori->nama_kategori ?? '-' }}
                        &bull; Dilaporkan {{ $activePengaduan->tanggal_pengaduan->format('d M Y') }}
                        &bull; Kondisi: {{ ucfirst($activePengaduan->kondisi_pengaduan) }}
                    </div>
                </div>
            </div>
            <a href="{{ route('siswa.pengaduan.show', $activePengaduan) }}" class="btn btn-primary btn-sm" style="border-radius:20px; white-space:nowrap;">
                Lihat Detail →
            </a>
        </div>

        <div class="stepper-wrap">
            <div class="stepper-line"></div>
            <div class="stepper-fill" style="width:{{ $fillPct }};"></div>
            <div class="stepper">
                <div class="step done">
                    <div class="step-dot"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
                    <div class="step-label">Diterima</div>
                    <div class="step-note">Pengaduan masuk</div>
                </div>
                <div class="step {{ $s === 'proses' ? 'active' : ($s === 'selesai' ? 'done' : '') }}">
                    <div class="step-dot">
                        @if($s === 'selesai')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        @elseif($s === 'proses')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
                        @else
                            2
                        @endif
                    </div>
                    <div class="step-label">Diproses</div>
                    <div class="step-note">Sedang ditangani</div>
                </div>
                <div class="step {{ $s === 'selesai' ? 'done' : '' }}">
                    <div class="step-dot">
                        @if($s === 'selesai')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        @else
                            3
                        @endif
                    </div>
                    <div class="step-label">Selesai</div>
                    <div class="step-note">Pengaduan tuntas</div>
                </div>
            </div>
        </div>

        @if($activePengaduan->catatan)
        <div style="margin-top:1rem; padding:0.6rem 0.85rem; background:rgba(34,166,69,0.07); border-radius:8px; border-left:3px solid var(--accent); font-size:0.8rem; color:var(--text);">
            <strong>Catatan Admin:</strong> {{ $activePengaduan->catatan }}
        </div>
        @endif
    </div>
    @endif

    {{-- ──────────────────────────────────────────────── --}}
    {{-- STATS CARDS                                      --}}
    {{-- ──────────────────────────────────────────────── --}}
    <div class="stats-grid" style="margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-icon gray"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg></div>
            <div class="stat-label">Total Pengaduan</div>
            <div class="stat-value">{{ $totalPengaduan }}</div>
            <div class="stat-sub">semua pengaduan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warn"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg></div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $pending }}</div>
            <div class="stat-sub">belum diproses</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
            <div class="stat-label">Diproses</div>
            <div class="stat-value">{{ $proses }}</div>
            <div class="stat-sub">sedang ditangani</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg></div>
            <div class="stat-label">Selesai</div>
            <div class="stat-value">{{ $selesai }}</div>
            <div class="stat-sub">sudah ditangani</div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────── --}}
    {{-- DASHBOARD GRID (Charts + Calendar | History)     --}}
    {{-- ──────────────────────────────────────────────── --}}
    <div class="dashboard-grid">

        {{-- ── KOLOM KIRI ── --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Panel Kalender Bulanan --}}
            <div class="panel-box">
                <div class="panel-header">
                    <div class="panel-header-left">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                        <span id="calTitle">Aktivitas Pengaduan</span>
                    </div>
                    <span style="font-size:0.7rem; color:var(--muted);">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:rgba(34,139,34,0.07);margin-right:3px;"></span>Tidak ada
                        <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#22a645;margin:0 3px 0 8px;"></span>Ada pengaduan
                    </span>
                </div>
                <div class="cal-outer">
                    <div class="cal-wrap" id="calWrap"></div>
                </div>
            </div>

            {{-- Panel Tren Garis --}}
            <div class="panel-box">
                <div class="panel-header">
                    <div class="panel-header-left">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                        Tren Pengaduan {{ date('Y') }}
                    </div>
                </div>
                <div class="chart-wrap" style="height:190px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

        </div>

        {{-- ── KOLOM KANAN ── --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Pie + Bar --}}
            <div class="multi-chart-grid">
                <div class="panel-box">
                    <div class="panel-header">
                        <div class="panel-header-left">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.5-4.25 8.97-8.99h-8.97z"/></svg>
                            Kondisi
                        </div>
                    </div>
                    <div class="chart-wrap" style="height:150px;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <div class="panel-box">
                    <div class="panel-header">
                        <div class="panel-header-left">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg>
                            Kategori
                        </div>
                    </div>
                    <div class="chart-wrap" style="height:150px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Mini History Table --}}
            <div class="panel-box" style="flex:1;">
                <div class="panel-header">
                    <div class="panel-header-left">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        Histori Terbaru
                    </div>
                    <a href="{{ route('siswa.pengaduan.index') }}" style="font-size:0.72rem; color:var(--accent); text-decoration:none; font-weight:500;">Semua →</a>
                </div>
                @if($recentPengaduan->isEmpty())
                    <div style="padding:1.5rem; text-align:center; color:var(--muted); font-size:0.8rem;">Belum ada pengaduan.</div>
                @else
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Pengaduan</th>
                                <th>Status</th>
                                <th>Tgl</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPengaduan as $p)
                            <tr>
                                <td>
                                    <a href="{{ route('siswa.pengaduan.show', $p) }}" style="color:var(--text); text-decoration:none; font-weight:500; display:block; max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $p->nama_pengaduan }}
                                    </a>
                                </td>
                                <td><span class="badge badge-{{ $p->status }}" style="font-size:0.62rem;">{{ ucfirst($p->status) }}</span></td>
                                <td style="color:var(--muted); font-size:0.72rem; white-space:nowrap;">{{ $p->tanggal_pengaduan->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Data dari PHP ── //
    const calData   = @json($calendarDataRaw);
    const pieVals   = @json(array_values($chartKondisi->toArray()));
    const pieKeys   = @json(array_keys($chartKondisi->toArray()));
    const barVals   = @json(array_values($chartKategori->toArray()));
    const barKeys   = @json(array_keys($chartKategori->toArray()));
    const lineVals  = @json(array_values($bulans));

    const COLORS = {
        accent : '#22a645',
        blue   : '#3b82f6',
        warn   : '#d97706',
        red    : '#dc2626',
        empty  : 'rgba(34,139,34,0.07)',
    };
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = 'rgba(26,46,26,0.55)';

    // ─────────────────────────────────────────
    // 1. KALENDER BULANAN (per bulan)
    // ─────────────────────────────────────────
    (function buildCalendar() {
        const wrap  = document.getElementById('calWrap');
        const title = document.getElementById('calTitle');
        const today = new Date();
        const yr    = today.getFullYear();
        const mo    = today.getMonth();   // 0-based

        const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        title.textContent = 'Aktivitas Pengaduan — ' + monthNames[mo] + ' ' + yr;

        const dayLabels = ['Sen','Sel','Rab','Kam','Jum','Sab','Mgg'];

        // Day labels column
        const lblCol = document.createElement('div');
        lblCol.style.cssText = 'display:flex;flex-direction:column;gap:3px;padding-top:20px;padding-right:5px;';
        dayLabels.forEach(d => {
            const s = document.createElement('span');
            s.textContent = d;
            s.style.cssText = 'font-size:0.6rem;color:var(--muted);height:12px;line-height:12px;';
            lblCol.appendChild(s);
        });
        wrap.appendChild(lblCol);

        // Grid column
        const gridWrap = document.createElement('div');
        gridWrap.style.cssText = 'display:flex;flex-direction:column;';

        // Build weeks: from Monday of week 1 to Sunday of last week
        const firstDay  = new Date(yr, mo, 1);
        const lastDay   = new Date(yr, mo + 1, 0);

        // Monday of first week
        let startDow = firstDay.getDay(); // 0=Sun…6=Sat
        let offset   = startDow === 0 ? 6 : startDow - 1; // how many days back to Monday
        const start  = new Date(firstDay);
        start.setDate(start.getDate() - offset);

        // Sunday of last week
        let endDow = lastDay.getDay();
        let endOff = endDow === 0 ? 0 : 7 - endDow;
        const end  = new Date(lastDay);
        end.setDate(end.getDate() + endOff);

        // Generate weeks (each week = 7 cells, column)
        const weeksRow = document.createElement('div');
        weeksRow.style.cssText = 'display:flex;gap:3px;margin-top:4px;';

        // Week labels row (above weeks)
        const wklblRow = document.createElement('div');
        wklblRow.style.cssText = 'display:flex;gap:3px;margin-bottom:4px;height:14px;margin-left:0;';
        wrap.insertBefore(wklblRow, lblCol.nextSibling); // will add below

        let cur = new Date(start);
        let isFirst = true;

        while (cur <= end) {
            const weekCol = document.createElement('div');
            weekCol.style.cssText = 'display:flex;flex-direction:column;gap:3px;';

            // Week label (month abbrev only on first week of each month)
            const wlbl = document.createElement('div');
            wlbl.style.cssText = 'font-size:0.6rem;color:var(--muted);height:14px;line-height:14px;text-align:center;';
            // show date of monday
            if (isFirst || cur.getDate() <= 7) {
                // just show week start date to orient user - omit for cleanliness, leave blank
            }
            wklblRow.appendChild(wlbl);

            for (let d = 0; d < 7; d++) {
                const cell = document.createElement('div');
                cell.className = 'cal-cell';

                const inMonth = cur.getMonth() === mo;
                if (!inMonth) {
                    // dim out-of-month cells
                    cell.style.opacity = '0.25';
                } else {
                    const pad = n => String(n).padStart(2,'0');
                    const iso = cur.getFullYear() + '-' + pad(cur.getMonth()+1) + '-' + pad(cur.getDate());
                    const cnt = calData[iso] || 0;

                    if (cnt > 0) {
                        const v = Math.min(cnt, 4);
                        cell.setAttribute('data-v', v);
                    }

                    // Mark today
                    if (cur.toDateString() === today.toDateString()) {
                        cell.classList.add('today');
                    }

                    const pad2 = n => String(n).padStart(2,'0');
                    const lbl  = pad2(cur.getDate()) + '/' + pad2(cur.getMonth()+1);
                    cell.title = cnt > 0 ? cnt + ' pengaduan — ' + lbl : 'Tidak ada — ' + lbl;
                }

                weekCol.appendChild(cell);
                cur.setDate(cur.getDate() + 1);
            }

            weeksRow.appendChild(weekCol);
            isFirst = false;
        }

        gridWrap.appendChild(wklblRow);
        gridWrap.appendChild(weeksRow);
        wrap.appendChild(gridWrap);
    })();

    // ─────────────────────────────────────────
    // 2. CHARTS
    // ─────────────────────────────────────────

    // A. Doughnut (Kondisi)
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: pieKeys.length ? pieKeys.map(k => k.charAt(0).toUpperCase()+k.slice(1)) : ['Kosong'],
            datasets: [{
                data: pieVals.length ? pieVals : [1],
                backgroundColor: pieVals.length ? [COLORS.blue, COLORS.warn, COLORS.red] : ['rgba(26,46,26,0.08)'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position:'bottom', labels:{ boxWidth:10, font:{size:9}, padding:6 } },
                tooltip: { enabled: pieVals.length > 0 }
            }
        }
    });

    // B. Bar (Kategori)
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: barKeys.length ? barKeys : ['-'],
            datasets: [{
                data: barVals.length ? barVals : [0],
                backgroundColor: COLORS.accent,
                borderRadius: 3,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: barKeys.length > 3 ? 'y' : 'x',
            plugins: { legend:{display:false} },
            scales: {
                x: { ticks:{font:{size:9}}, grid:{display:false}, beginAtZero:true },
                y: { ticks:{font:{size:9}}, grid:{display:false}, beginAtZero:true }
            }
        }
    });

    // C. Line (Bulanan)
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
            datasets: [{
                label: 'Pengaduan',
                data: lineVals,
                borderColor: COLORS.accent,
                backgroundColor: 'rgba(34,166,69,0.09)',
                borderWidth: 2,
                pointBackgroundColor: COLORS.accent,
                pointRadius: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend:{display:false} },
            scales: {
                y: { beginAtZero:true, ticks:{precision:0,stepSize:1,font:{size:10}} },
                x: { ticks:{font:{size:10}}, grid:{display:false} }
            }
        }
    });

});
</script>
@endpush
