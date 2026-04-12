<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_desc', 'Panel Admin Aspirasi Sekolah')">
    <title>@yield('title', 'Dashboard') | Aspirasi Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0d1117;
            --surface:   #161b22;
            --surface2:  #1c2230;
            --border:    rgba(255,255,255,0.07);
            --text:      #e6edf3;
            --muted:     rgba(230,237,243,0.45);
            --accent:    #3b82f6;
            --accent-h:  #2563eb;
            --success:   #10b981;
            --danger:    #ef4444;
            --warn:      #f59e0b;
            --sidebar-w: 240px;
            --radius:    10px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            font-size: 0.9rem;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 1.4rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }
        .brand-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), #06b6d4);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-icon svg { width: 18px; height: 18px; fill: #fff; }
        .brand-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }
        .brand-name span { display: block; font-weight: 400; font-size: 0.7rem; color: var(--muted); }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }
        .nav-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 0.75rem 0.5rem 0.35rem;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.75rem;
            border-radius: var(--radius);
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 2px;
        }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: var(--text); }
        .nav-item.active {
            background: rgba(59,130,246,0.12);
            color: var(--accent);
        }
        .nav-item.active svg { color: var(--accent); }

        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.6rem 0.75rem;
            border-radius: var(--radius);
        }
        .avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--accent), #06b6d4);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 0.8rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.68rem; color: var(--muted); }

        .logout-btn {
            display: flex; align-items: center; gap: 0.6rem;
            width: 100%;
            padding: 0.55rem 0.75rem;
            background: none;
            border: none;
            border-radius: var(--radius);
            color: rgba(239,68,68,0.7);
            font-size: 0.84rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            text-align: left;
            margin-top: 2px;
        }
        .logout-btn svg { width: 17px; height: 17px; }
        .logout-btn:hover { background: rgba(239,68,68,0.08); color: var(--danger); }

        /* ── Main ── */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.75rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 50;
        }
        .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
        }
        .breadcrumb {
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 1px;
        }

        .page-body {
            padding: 1.75rem;
            flex: 1;
        }

        /* ── Flash ── */
        .flash {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.84rem;
            margin-bottom: 1.25rem;
            animation: fadeIn 0.3s ease;
        }
        .flash-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; }
        .flash-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.25);  color: #fca5a5; }
        .flash svg { width: 16px; height: 16px; flex-shrink: 0; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: var(--radius);
            font-size: 0.84rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.15s;
            line-height: 1;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 2px 10px rgba(59,130,246,0.25);
        }
        .btn-primary:hover { background: var(--accent-h); border-color: var(--accent-h); }
        .btn-ghost {
            background: rgba(255,255,255,0.05);
            color: var(--muted);
            border-color: var(--border);
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); color: var(--text); }
        .btn-danger {
            background: rgba(239,68,68,0.1);
            color: #fca5a5;
            border-color: rgba(239,68,68,0.25);
        }
        .btn-danger:hover { background: rgba(239,68,68,0.18); color: var(--danger); }
        .btn-sm { padding: 0.38rem 0.7rem; font-size: 0.78rem; }

        /* ── Table ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid var(--border); }
        th {
            padding: 0.7rem 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            text-align: left;
        }
        td {
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }

        /* ── Form ── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.75rem;
            max-width: 520px;
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.45rem;
        }
        .form-control {
            width: 100%;
            padding: 0.65rem 0.875rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.18s, box-shadow 0.18s;
            outline: none;
        }
        .form-control::placeholder { color: rgba(230,237,243,0.2); }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .form-control.is-invalid { border-color: rgba(239,68,68,0.5); }
        .invalid-feedback { font-size: 0.75rem; color: #fca5a5; margin-top: 0.35rem; }

        /* ── Badge ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
        }
        .badge-blue { background: rgba(59,130,246,0.12); color: #93c5fd; }
        .badge-green { background: rgba(16,185,129,0.12); color: #6ee7b7; }
        .badge-gray { background: rgba(255,255,255,0.06); color: var(--muted); }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 0.35rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 32px; height: 32px;
            padding: 0 0.6rem;
            border-radius: 8px;
            font-size: 0.8rem;
            text-decoration: none;
        }
        .pagination a { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); transition: all 0.15s; }
        .pagination a:hover { background: rgba(59,130,246,0.12); color: var(--accent); border-color: rgba(59,130,246,0.3); }
        .pagination span.active-page { background: var(--accent); color: #fff; }
        .pagination span.disabled { background: var(--surface2); color: rgba(230,237,243,0.2); border: 1px solid var(--border); }

        /* ── Stats card ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
        }
        .stat-label { font-size: 0.72rem; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-sub   { font-size: 0.72rem; color: var(--muted); margin-top: 0.3rem; }
        .stat-icon  { float: right; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-top: -2px; }
        .stat-icon svg { width: 18px; height: 18px; }
        .stat-icon.blue  { background: rgba(59,130,246,0.12); color: #93c5fd; }
        .stat-icon.green { background: rgba(16,185,129,0.12); color: #6ee7b7; }
        .stat-icon.warn  { background: rgba(245,158,11,0.12);  color: #fcd34d; }
        .stat-icon.red   { background: rgba(239,68,68,0.12);   color: #fca5a5; }

        /* ── Section header ── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem;
        }
        .section-title { font-size: 0.9rem; font-weight: 600; color: var(--text); }
        .section-sub   { font-size: 0.75rem; color: var(--muted); margin-top: 1px; }

        /* ── Empty state ── */
        .empty-state { padding: 3rem; text-align: center; color: var(--muted); }
        .empty-state svg { width: 40px; height: 40px; margin-bottom: 0.75rem; opacity: 0.4; }
        .empty-state p { font-size: 0.875rem; }

        /* ── Modal delete confirm ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            display: none; align-items: center; justify-content: center;
            z-index: 200;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.2s ease; }
        .modal-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.75rem;
            max-width: 360px; width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .modal-icon { width: 44px; height: 44px; background: rgba(239,68,68,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
        .modal-icon svg { width: 22px; height: 22px; color: var(--danger); }
        .modal-title { font-size: 1rem; font-weight: 600; color: var(--text); margin-bottom: 0.4rem; }
        .modal-desc  { font-size: 0.84rem; color: var(--muted); margin-bottom: 1.25rem; line-height: 1.5; }
        .modal-actions { display: flex; gap: 0.5rem; justify-content: flex-end; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <div class="brand-name">
                Aspirasi Sekolah
                <span>Panel Admin</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Dashboard
            </a>

            <div class="nav-label">Data</div>
            <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5S15.01 22 17.5 22s4.5-2.01 4.5-4.5S19.99 13 17.5 13zm0 7c-1.38 0-2.5-1.12-2.5-2.5S16.12 15 17.5 15s2.5 1.12 2.5 2.5S18.88 20 17.5 20zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>
                Kategori
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-content">
        <header class="topbar">
            <div>
                <div class="page-title">@yield('page_title', 'Dashboard')</div>
                <div class="breadcrumb">@yield('breadcrumb', 'Beranda')</div>
            </div>
            <div style="font-size:0.78rem; color:var(--muted);">
                {{ now()->translatedFormat('d F Y') }}
            </div>
        </header>

        <main class="page-body">
            @if(session('success'))
                <div class="flash flash-success">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Delete confirm modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </div>
            <div class="modal-title">Hapus Kategori?</div>
            <div class="modal-desc" id="deleteDesc">Data ini akan dihapus secara permanen dan tidak dapat dikembalikan.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tombol hapus pakai data-* attribute — aman untuk semua karakter
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            const url  = btn.dataset.url;
            const name = btn.dataset.name;
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteDesc').textContent = 'Kategori "' + name + '" akan dihapus permanen.';
            document.getElementById('deleteModal').classList.add('show');
        });

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
    @stack('scripts')
</body>
</html>
