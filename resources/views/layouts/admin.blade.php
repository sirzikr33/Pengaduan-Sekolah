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
            --bg:        #f0f4f0;
            --surface:   #ffffff;
            --surface2:  #f5f8f5;
            --border:    rgba(34,139,34,0.12);
            --text:      #1a2e1a;
            --muted:     rgba(26,46,26,0.5);
            --accent:    #22a645;
            --accent-h:  #1a8a38;
            --accent-lt: rgba(34,166,69,0.08);
            --success:   #22a645;
            --danger:    #dc2626;
            --warn:      #d97706;
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
            box-shadow: 2px 0 12px rgba(34,139,34,0.06);
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
            background: linear-gradient(135deg, var(--accent), #16a34a);
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
        .nav-item:hover { background: var(--accent-lt); color: var(--accent); }
        .nav-item.active {
            background: var(--accent-lt);
            color: var(--accent);
            font-weight: 600;
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
            background: linear-gradient(135deg, var(--accent), #16a34a);
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
            color: rgba(220,38,38,0.65);
            font-size: 0.84rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            text-align: left;
            margin-top: 2px;
        }
        .logout-btn svg { width: 17px; height: 17px; }
        .logout-btn:hover { background: rgba(220,38,38,0.06); color: var(--danger); }

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
            box-shadow: 0 1px 8px rgba(34,139,34,0.06);
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
        .flash-success { background: rgba(34,166,69,0.08); border: 1px solid rgba(34,166,69,0.25); color: #166534; }
        .flash-error   { background: rgba(220,38,38,0.08);  border: 1px solid rgba(220,38,38,0.2);  color: #991b1b; }
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
            box-shadow: 0 2px 10px rgba(34,166,69,0.25);
        }
        .btn-primary:hover { background: var(--accent-h); border-color: var(--accent-h); }
        .btn-ghost {
            background: var(--surface);
            color: var(--muted);
            border-color: var(--border);
        }
        .btn-ghost:hover { background: var(--accent-lt); color: var(--accent); border-color: rgba(34,166,69,0.3); }
        .btn-danger {
            background: rgba(220,38,38,0.07);
            color: #dc2626;
            border-color: rgba(220,38,38,0.2);
        }
        .btn-danger:hover { background: rgba(220,38,38,0.14); }
        .btn-sm { padding: 0.38rem 0.7rem; font-size: 0.78rem; }

        /* ── Table ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 8px rgba(34,139,34,0.05);
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid var(--border); background: var(--surface2); }
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
        tbody tr:hover { background: var(--accent-lt); }

        /* ── Form ── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.75rem;
            max-width: 520px;
            box-shadow: 0 1px 8px rgba(34,139,34,0.05);
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
        .form-control::placeholder { color: rgba(26,46,26,0.25); }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(34,166,69,0.1);
        }
        .form-control.is-invalid { border-color: rgba(220,38,38,0.5); }
        .invalid-feedback { font-size: 0.75rem; color: #dc2626; margin-top: 0.35rem; }

        /* ── Badge ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
        }
        .badge-blue  { background: rgba(34,166,69,0.1); color: #166534; }
        .badge-green { background: rgba(34,166,69,0.12); color: #166534; }
        .badge-gray  { background: rgba(26,46,26,0.07); color: var(--muted); }

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
        .pagination a { background: var(--surface); color: var(--muted); border: 1px solid var(--border); transition: all 0.15s; }
        .pagination a:hover { background: var(--accent-lt); color: var(--accent); border-color: rgba(34,166,69,0.3); }
        .pagination span.active-page { background: var(--accent); color: #fff; }
        .pagination span.disabled { background: var(--surface2); color: rgba(26,46,26,0.2); border: 1px solid var(--border); }

        /* ── Stats card ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 8px rgba(34,139,34,0.05);
        }
        .stat-label { font-size: 0.72rem; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-sub   { font-size: 0.72rem; color: var(--muted); margin-top: 0.3rem; }
        .stat-icon  { float: right; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-top: -2px; }
        .stat-icon svg { width: 18px; height: 18px; }
        .stat-icon.blue  { background: rgba(34,166,69,0.1); color: var(--accent); }
        .stat-icon.green { background: rgba(34,166,69,0.12); color: var(--accent); }
        .stat-icon.warn  { background: rgba(217,119,6,0.1);  color: var(--warn); }
        .stat-icon.red   { background: rgba(220,38,38,0.08); color: var(--danger); }

        /* ── Section header ── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem;
        }
        .section-title { font-size: 0.9rem; font-weight: 600; color: var(--text); }
        .section-sub   { font-size: 0.75rem; color: var(--muted); margin-top: 1px; }

        /* ── Empty state ── */
        .empty-state { padding: 3rem; text-align: center; color: var(--muted); }
        .empty-state svg { width: 40px; height: 40px; margin-bottom: 0.75rem; opacity: 0.3; color: var(--accent); }
        .empty-state p { font-size: 0.875rem; }

        /* ── Modal delete confirm ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.25);
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        .modal-icon { width: 44px; height: 44px; background: rgba(220,38,38,0.08); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
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
                LaporinAja
                <span>Admin</span>
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
            <a href="{{ route('admin.pengaduan.index') }}" class="nav-item {{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg>
                Pengaduan
            </a>
            <a href="{{ route('admin.chat.index') }}" class="nav-item {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                Live Chat
            </a>
            <a href="{{ route('admin.logs.index') }}" class="nav-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                Audit Log
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
            <div class="modal-title">Hapus Data?</div>
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
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            const url  = btn.dataset.url;
            const name = btn.dataset.name;
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteDesc').textContent = '"' + name + '" akan dihapus secara permanen.';
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
