@extends('layouts.siswa')

@section('title', 'Dashboard Saya')
@section('page_title', 'Dashboard')
@section('breadcrumb', 'Beranda / Dashboard')

@push('styles')
<style>
    /* ── Stat Cards ── */
    .db-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .db-stat {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 12px; padding: 1rem 1.1rem;
        box-shadow: 0 1px 6px rgba(34,139,34,0.05);
        position: relative; overflow: hidden;
    }
    .db-stat-label { font-size:0.69rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
    .db-stat-val   { font-size:1.8rem; font-weight:800; color:var(--text); line-height:1.1; margin-top:0.15rem; }
    .db-stat-sub   { font-size:0.7rem; color:var(--muted); margin-top:0.15rem; }
    .db-stat-bar   { position:absolute; bottom:0; left:0; height:3px; border-radius:0 0 0 0; }

    /* ── Progress Card ── */
    .progress-card {
        background: linear-gradient(135deg, var(--surface), rgba(34,166,69,0.05));
        border: 1px solid rgba(34,166,69,0.2);
        border-radius: 14px; padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 16px rgba(34,139,34,0.08);
        position:relative; overflow:hidden;
    }
    .progress-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:linear-gradient(180deg,var(--accent),#16a34a); }
    .p-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.1rem; }
    .p-title { font-size:0.92rem; font-weight:700; color:var(--text); }
    .p-meta  { font-size:0.75rem; color:var(--muted); margin-top:2px; }

    /* Stepper */
    .stepper-wrap { position:relative; padding:0 1rem; margin-top:0.25rem; }
    .stepper-bg { position:absolute; top:20px; left:10%; right:10%; height:2px; background:var(--border); z-index:0; }
    .stepper-fill { position:absolute; top:20px; left:10%; height:2px; background:var(--accent); z-index:1; transition:width 0.5s ease; }
    .stepper { display:flex; justify-content:space-between; position:relative; z-index:2; }
    .step { text-align:center; flex:1; }
    .step-dot {
        width:38px; height:38px; border-radius:50%;
        background:var(--surface); border:2px solid var(--border);
        display:flex; align-items:center; justify-content:center;
        margin:0 auto 0.4rem; color:var(--muted);
        font-size:0.8rem; font-weight:700; transition:all 0.3s;
    }
    .step-dot svg { width:16px; height:16px; }
    .step.done .step-dot { border-color:var(--accent); background:var(--accent); color:#fff; }
    .step.active .step-dot { border-color:#3b82f6; background:#3b82f6; color:#fff; box-shadow:0 0 0 4px rgba(59,130,246,0.15); }
    .step-lbl { font-size:0.72rem; font-weight:600; color:var(--muted); }
    .step.done .step-lbl { color:var(--accent); }
    .step.active .step-lbl { color:#3b82f6; }

    /* ── Alert Chat Aktif ── */
    .chat-alert {
        display:flex; align-items:center; gap:0.75rem;
        background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.2);
        border-radius:12px; padding:0.85rem 1.1rem; margin-bottom:1.25rem;
    }
    .chat-alert svg { width:20px; height:20px; color:#3b82f6; flex-shrink:0; }

    /* ── Dashboard Grid ── */
    .db-grid { display:grid; grid-template-columns:1.5fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    @media (max-width:1000px) { .db-grid { grid-template-columns:1fr; } }

    /* ── Panel ── */
    .panel {
        background:var(--surface); border:1px solid var(--border);
        border-radius:14px; padding:1.1rem 1.25rem;
        box-shadow:0 1px 8px rgba(34,139,34,0.04);
    }
    .panel-hd {
        font-size:0.82rem; font-weight:600; color:var(--text);
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:.875rem; padding-bottom:.65rem; border-bottom:1px solid var(--border);
    }
    .panel-hd-left { display:flex; align-items:center; gap:0.45rem; }
    .panel-hd svg  { width:15px; height:15px; color:var(--accent); }

    /* ── Kalender ── */
    .cal-outer { overflow-x:auto; }
    .cal-wrap { display:flex; gap:4px; min-width:min-content; }
    .cal-cell {
        width:14px; height:14px; border-radius:3px;
        background:rgba(34,139,34,0.07); cursor:default;
        transition:transform 0.12s; position:relative;
    }
    .cal-cell:hover { transform:scale(1.3); z-index:5; }
    .cal-cell[data-v="1"] { background:#bbf7d0; }
    .cal-cell[data-v="2"] { background:#4ade80; }
    .cal-cell[data-v="3"] { background:#22a645; }
    .cal-cell[data-v="4"] { background:#166534; }
    .cal-cell.today { box-shadow:0 0 0 2px var(--accent); }
    .cal-cell.out-of-month { background:transparent; border:1px dashed rgba(34,139,34,0.1); }

    /* ── Mini Chart ── */
    .chart-2col { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media (max-width:600px) { .chart-2col { grid-template-columns:1fr; } }

    /* ── Mini Table ── */
    .mini-table { font-size:0.78rem; width:100%; border-collapse:collapse; }
    .mini-table th { padding:0.45rem 0.3rem; color:var(--muted); font-weight:600; text-transform:uppercase; font-size:0.62rem; border-bottom:1px solid var(--border); }
    .mini-table td { padding:0.5rem 0.3rem; border-bottom:1px solid rgba(34,139,34,0.06); }
    .mini-table tbody tr:last-child td { border-bottom:none; }
    .mini-table tbody tr:hover td { background:var(--surface2); }

    /* ── Resolution Ring ── */
    .ring-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.5rem; padding:0.5rem; }
    .ring-label { font-size:0.72rem; color:var(--muted); text-align:center; }
    .ring-val   { font-size:1.4rem; font-weight:800; color:var(--text); text-align:center; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ── Alert: Sesi Chat Aktif ── --}}
    @if($activeChat)
    <div class="chat-alert">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        <div style="flex:1;">
            <strong style="font-size:0.85rem; color:var(--text);">
                💬 Kamu {{ $activeChat->status === 'queued' ? 'sedang menunggu di antrean chat' : 'memiliki sesi chat yang aktif' }}
            </strong>
            <div style="font-size:0.75rem; color:var(--muted);">
                {{ $activeChat->status === 'queued' ? 'Mohon tunggu, Admin akan segera menerima chatmu.' : 'Admin sudah terhubung. Lanjutkan percakapan.' }}
            </div>
        </div>
        <a href="{{ route('siswa.chat.index') }}" class="btn btn-ghost btn-sm">
            Buka Chat →
        </a>
    </div>
    @endif

    {{-- ── Progress Pengaduan Aktif ── --}}
    @if($activePengaduan)
    @php $s = $activePengaduan->status; @endphp
    <div class="progress-card">
        <div class="p-head">
            <div>
                <div class="p-title">🔔 {{ $activePengaduan->nama_pengaduan }}</div>
                <div class="p-meta">
                    {{ $activePengaduan->kategori->nama_kategori ?? '-' }}
                    &bull; {{ $activePengaduan->tanggal_pengaduan->format('d M Y') }}
                    &bull; Kondisi: {{ ucfirst($activePengaduan->kondisi_pengaduan) }}
                    @if($activePengaduan->is_anonim) &bull; 🔒 Anonim @endif
                </div>
            </div>
            <a href="{{ route('siswa.pengaduan.show', $activePengaduan) }}" class="btn btn-primary btn-sm" style="border-radius:20px; white-space:nowrap;">
                Detail →
            </a>
        </div>
        <div class="stepper-wrap">
            @php
                $fillPct = $s === 'pending' ? '0%' : ($s === 'proses' ? '50%' : '100%');
            @endphp
            <div class="stepper-bg"></div>
            <div class="stepper-fill" style="width:{{ $fillPct }};"></div>
            <div class="stepper">
                <div class="step done">
                    <div class="step-dot"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
                    <div class="step-lbl">Diterima</div>
                </div>
                <div class="step {{ $s === 'proses' ? 'active' : ($s === 'selesai' ? 'done' : '') }}">
                    <div class="step-dot">
                        @if($s === 'selesai')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        @elseif($s === 'proses')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
                        @else 2 @endif
                    </div>
                    <div class="step-lbl">Diproses</div>
                </div>
                <div class="step {{ $s === 'selesai' ? 'done' : '' }}">
                    <div class="step-dot">
                        @if($s === 'selesai')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        @else 3 @endif
                    </div>
                    <div class="step-lbl">Selesai</div>
                </div>
            </div>
        </div>
        @if($activePengaduan->catatan)
        <div style="margin-top:1rem; padding:0.55rem 0.85rem; background:rgba(34,166,69,0.07); border-radius:8px; border-left:3px solid var(--accent); font-size:0.8rem; color:var(--text);">
            <strong>📋 Catatan Admin:</strong> {{ $activePengaduan->catatan }}
        </div>
        @endif
    </div>
    @endif

    {{-- ── STAT CARDS ── --}}
    <div class="db-stats">
        <div class="db-stat">
            <div class="db-stat-label">Total Pengaduanku</div>
            <div class="db-stat-val">{{ $total }}</div>
            <div class="db-stat-sub">Semua waktu</div>
            <div class="db-stat-bar" style="width:100%; background:rgba(26,46,26,0.1);"></div>
        </div>
        <div class="db-stat">
            <div class="db-stat-label">Pending</div>
            <div class="db-stat-val" style="color:#d97706;">{{ $pending }}</div>
            <div class="db-stat-sub">Menunggu admin</div>
            <div class="db-stat-bar" style="width:{{ $total > 0 ? round(($pending/$total)*100) : 0 }}%; background:#d97706;"></div>
        </div>
        <div class="db-stat">
            <div class="db-stat-label">Diproses</div>
            <div class="db-stat-val" style="color:#3b82f6;">{{ $proses }}</div>
            <div class="db-stat-sub">Sedang ditangani</div>
            <div class="db-stat-bar" style="width:{{ $total > 0 ? round(($proses/$total)*100) : 0 }}%; background:#3b82f6;"></div>
        </div>
        <div class="db-stat">
            <div class="db-stat-label">Selesai</div>
            <div class="db-stat-val" style="color:#22a645;">{{ $selesai }}</div>
            <div class="db-stat-sub">Sudah tuntas</div>
            <div class="db-stat-bar" style="width:{{ $total > 0 ? round(($selesai/$total)*100) : 0 }}%; background:#22a645;"></div>
        </div>
        <div class="db-stat">
            <div class="db-stat-label">Penyelesaian</div>
            <div class="db-stat-val" style="color:var(--accent);">{{ $resolutionRate }}%</div>
            <div class="db-stat-sub">Dari semua pengaduan</div>
            <div class="db-stat-bar" style="width:{{ $resolutionRate }}%; background:var(--accent);"></div>
        </div>
    </div>

    {{-- ── GRID: Kalender + Charts ── --}}
    <div class="db-grid">

        {{-- Kolom Kiri: Kalender + Tren ──────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Kalender Bulan Ini --}}
            <div class="panel">
                <div class="panel-hd">
                    <div class="panel-hd-left">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                        <span id="calTitle">Aktivitas Bulan Ini</span>
                    </div>
                    <span style="font-size:0.68rem; color:var(--muted);">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:rgba(34,139,34,0.07);margin-right:3px;"></span>Tidak ada
                        <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#22a645;margin:0 3px 0 8px;"></span>Ada laporan
                    </span>
                </div>
                <div class="cal-outer">
                    <div class="cal-wrap" id="calWrap"></div>
                </div>
            </div>

            {{-- Tren Pengaduan Tahun Ini --}}
            <div class="panel">
                <div class="panel-hd">
                    <div class="panel-hd-left">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                        Tren Pengaduan Tahun {{ date('Y') }}
                    </div>
                </div>
                <div style="position:relative; height:170px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Pie Kondisi + Bar Kategori + Histori ────── --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Dua chart kecil --}}
            <div class="chart-2col">
                <div class="panel">
                    <div class="panel-hd" style="margin-bottom:0.6rem; padding-bottom:0.5rem;">
                        <div class="panel-hd-left">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.5-4.25 8.97-8.99h-8.97z"/></svg>
                            Kondisi
                        </div>
                    </div>
                    <div style="position:relative; height:130px;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-hd" style="margin-bottom:0.6rem; padding-bottom:0.5rem;">
                        <div class="panel-hd-left">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg>
                            Kategori
                        </div>
                    </div>
                    <div style="position:relative; height:130px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="panel" style="flex:1;">
                <div class="panel-hd">
                    <div class="panel-hd-left">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        Riwayat Terbaru
                    </div>
                    <a href="{{ route('siswa.pengaduan.index') }}" style="font-size:0.72rem; color:var(--accent); text-decoration:none; font-weight:500;">Semua →</a>
                </div>
                @if($recentPengaduan->isEmpty())
                    <div style="padding:1.5rem; text-align:center; color:var(--muted); font-size:0.8rem;">
                        Belum ada pengaduan.
                    </div>
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
                                    <a href="{{ route('siswa.pengaduan.show', $p) }}"
                                       style="color:var(--text); text-decoration:none; font-weight:500; display:block; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                       title="{{ $p->nama_pengaduan }}">
                                        {{ $p->nama_pengaduan }}
                                    </a>
                                </td>
                                <td><span class="badge badge-{{ $p->status }}" style="font-size:0.6rem;">{{ ucfirst($p->status) }}</span></td>
                                <td style="color:var(--muted); font-size:0.7rem; white-space:nowrap;">{{ $p->tanggal_pengaduan->format('d/m/Y') }}</td>
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
    const calData   = @json($calendarDataRaw);
    const pieVals   = @json(array_values($chartKondisi->toArray()));
    const pieKeys   = @json(array_keys($chartKondisi->toArray()));
    const barVals   = @json(array_values($chartKategori->toArray()));
    const barKeys   = @json(array_keys($chartKategori->toArray()));
    const lineVals  = @json(array_values($bulans));

    const C = {
        green: '#22a645', blue: '#3b82f6', warn: '#d97706', red: '#dc2626',
        empty: 'rgba(34,139,34,0.07)',
    };
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = 'rgba(26,46,26,0.5)';

    // ── 1. Kalender Bulan Ini ──────────────────────────────────────
    (function buildCal() {
        const wrap  = document.getElementById('calWrap');
        const today = new Date();
        const yr    = today.getFullYear();
        const mo    = today.getMonth();
        const names = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('calTitle').textContent = 'Aktivitas — ' + names[mo] + ' ' + yr;

        const dayLabels = ['Sen','Sel','Rab','Kam','Jum','Sab','Mgg'];
        const lblCol = document.createElement('div');
        lblCol.style.cssText = 'display:flex;flex-direction:column;gap:4px;padding-top:20px;padding-right:6px;';
        dayLabels.forEach(d => {
            const s = document.createElement('span');
            s.textContent = d;
            s.style.cssText = 'font-size:0.6rem;color:var(--muted);height:14px;line-height:14px;';
            lblCol.appendChild(s);
        });
        wrap.appendChild(lblCol);

        const gridWrap = document.createElement('div');
        gridWrap.style.cssText = 'display:flex;flex-direction:column;';

        const firstDay = new Date(yr, mo, 1);
        const lastDay  = new Date(yr, mo + 1, 0);
        let startDow = firstDay.getDay();
        let offset = startDow === 0 ? 6 : startDow - 1;
        const start = new Date(firstDay);
        start.setDate(start.getDate() - offset);
        let endDow = lastDay.getDay();
        let endOff = endDow === 0 ? 0 : 7 - endDow;
        const end = new Date(lastDay);
        end.setDate(end.getDate() + endOff);

        const wklblRow = document.createElement('div');
        wklblRow.style.cssText = 'display:flex;gap:4px;margin-bottom:4px;height:14px;';
        const weeksRow = document.createElement('div');
        weeksRow.style.cssText = 'display:flex;gap:4px;';

        let cur = new Date(start);
        while (cur <= end) {
            const wlbl = document.createElement('div');
            wlbl.style.cssText = 'width:14px;font-size:0;';
            wklblRow.appendChild(wlbl);

            const weekCol = document.createElement('div');
            weekCol.style.cssText = 'display:flex;flex-direction:column;gap:4px;';

            for (let d = 0; d < 7; d++) {
                const cell = document.createElement('div');
                cell.className = 'cal-cell';
                const inMonth = cur.getMonth() === mo;
                if (!inMonth) {
                    cell.classList.add('out-of-month');
                } else {
                    const pad = n => String(n).padStart(2,'0');
                    const iso = cur.getFullYear() + '-' + pad(cur.getMonth()+1) + '-' + pad(cur.getDate());
                    const cnt = calData[iso] || 0;
                    if (cnt > 0) cell.setAttribute('data-v', Math.min(cnt, 4));
                    if (cur.toDateString() === today.toDateString()) cell.classList.add('today');
                    cell.title = cnt > 0 ? cnt + ' pengaduan — ' + pad(cur.getDate())+'/'+ pad(cur.getMonth()+1) : 'Tidak ada — ' + pad(cur.getDate())+'/'+ pad(cur.getMonth()+1);
                }
                weekCol.appendChild(cell);
                cur.setDate(cur.getDate() + 1);
            }
            weeksRow.appendChild(weekCol);
        }
        gridWrap.appendChild(wklblRow);
        gridWrap.appendChild(weeksRow);
        wrap.appendChild(gridWrap);
    })();

    // ── 2. Doughnut Kondisi ────────────────────────────────────────
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: pieKeys.length ? pieKeys.map(k => k.charAt(0).toUpperCase()+k.slice(1)) : ['Belum Ada'],
            datasets: [{
                data: pieVals.length ? pieVals : [1],
                backgroundColor: pieVals.length ? [C.blue, C.warn, C.red] : ['rgba(26,46,26,0.08)'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { position:'bottom', labels:{ boxWidth:8, font:{size:8}, padding:5 } } }
        }
    });

    // ── 3. Bar Kategori ────────────────────────────────────────────
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: barKeys.length ? barKeys : ['-'],
            datasets: [{ data: barVals.length ? barVals : [0], backgroundColor: C.green, borderRadius: 3 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks:{font:{size:8}}, grid:{display:false}, beginAtZero:true },
                y: { ticks:{font:{size:8}}, grid:{display:false} }
            }
        }
    });

    // ── 4. Line Tren Tahunan ───────────────────────────────────────
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
            datasets: [{
                label: 'Pengaduan',
                data: lineVals,
                borderColor: C.green,
                backgroundColor: 'rgba(34,166,69,0.08)',
                borderWidth: 2, pointRadius: 3, pointBackgroundColor: C.green, fill: true, tension: 0.4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero:true, ticks:{precision:0,stepSize:1,font:{size:9}}, grid:{color:'rgba(26,46,26,0.05)'} },
                x: { ticks:{font:{size:9}}, grid:{display:false} }
            }
        }
    });
});
</script>
@endpush
