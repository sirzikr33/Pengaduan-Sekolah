<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login - Sistem Aspirasi Sekolah">
    <title>Masuk | Aspirasi Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #f0f4f0;
            --surface: #ffffff;
            --border:  rgba(34,139,34,0.12);
            --text:    #1a2e1a;
            --muted:   rgba(26,46,26,0.5);
            --accent:  #22a645;
            --accent-h:#1a8a38;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: var(--text);
        }

        /* Decorative background blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }
        body::before {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(34,166,69,0.12) 0%, transparent 70%);
            top: -100px; left: -100px;
        }
        body::after {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(22,163,74,0.08) 0%, transparent 70%);
            bottom: -80px; right: -80px;
        }

        .login-wrap {
            width: 100%;
            max-width: 390px;
            animation: slideUp 0.35s ease-out;
            position: relative;
            z-index: 1;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), #16a34a);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(34,166,69,0.3);
        }
        .brand-icon svg { width: 20px; height: 20px; fill: #fff; }
        .brand-text { line-height: 1.2; }
        .brand-name { font-size: 0.95rem; font-weight: 700; color: var(--text); }
        .brand-sub  { font-size: 0.72rem; color: var(--muted); }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 24px rgba(34,139,34,0.08);
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.25rem;
        }
        .card-sub {
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 1.75rem;
        }

        /* Alerts */
        .alert {
            display: flex; align-items: center; gap: 0.45rem;
            padding: 0.65rem 0.875rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
        }
        .alert svg { width: 15px; height: 15px; flex-shrink: 0; }
        .alert-success { background: rgba(34,166,69,0.08); border: 1px solid rgba(34,166,69,0.2); color: #166534; }
        .alert-error   { background: rgba(220,38,38,0.07); border: 1px solid rgba(220,38,38,0.2); color: #991b1b; }

        /* Form */
        .form-group { margin-bottom: 1rem; }
        label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 0.8rem;
            top: 50%; transform: translateY(-50%);
            color: rgba(34,139,34,0.3);
            width: 16px; height: 16px;
            pointer-events: none;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.65rem 0.875rem 0.65rem 2.5rem;
            background: #f5f8f5;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.18s, box-shadow 0.18s;
            outline: none;
        }
        input::placeholder { color: rgba(26,46,26,0.25); }
        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(34,166,69,0.1);
            background: #fff;
        }
        input.is-invalid { border-color: rgba(220,38,38,0.5); }
        .error-msg { font-size: 0.73rem; color: #dc2626; margin-top: 0.3rem; }

        /* Toggle password */
        .toggle-pass {
            position: absolute;
            right: 0.8rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: rgba(34,139,34,0.3); padding: 0;
            transition: color 0.15s; line-height: 1;
        }
        .toggle-pass:hover { color: var(--accent); }
        .toggle-pass svg { width: 16px; height: 16px; }

        /* Remember */
        .remember-row {
            display: flex; align-items: center; gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: var(--accent); padding: 0; cursor: pointer;
        }
        .remember-row label {
            margin: 0; font-size: 0.78rem;
            color: var(--muted); cursor: pointer;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: var(--accent);
            color: #fff;
            border: none; border-radius: 8px;
            font-size: 0.9rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: 0 3px 14px rgba(34,166,69,0.3);
        }
        .btn-login svg { width: 16px; height: 16px; }
        .btn-login:hover { background: var(--accent-h); transform: translateY(-1px); box-shadow: 0 5px 18px rgba(34,166,69,0.35); }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .footer-text {
            text-align: center;
            font-size: 0.7rem;
            color: rgba(26,46,26,0.3);
            margin-top: 1.5rem;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <div class="brand-text">
                <div class="brand-name">LaporinAja</div>
                <div class="brand-sub">Sistem Pengaduan Siswa</div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Masuk</div>
            <div class="card-sub">Gunakan akun yang telah terdaftar</div>

            @if(session('success'))
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->has('email') && !$errors->has('password'))
                <div class="alert alert-error">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login.post') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@sekolah.sch.id"
                            autocomplete="email"
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                            required>
                    </div>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        <input type="password" id="password" name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            required>
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan password">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/>
                    </svg>
                    Masuk
                </button>
            </form>
        </div>

        <p class="footer-text">Aspirasi &amp; Pengaduan Sekolah &copy; {{ date('Y') }}</p>
    </div>

    <script>
        const togglePass = document.getElementById('togglePass');
        const pwdInput   = document.getElementById('password');
        const eyeIcon    = document.getElementById('eyeIcon');

        const eyeOpen  = `<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>`;
        const eyeClose = `<path d="M2.39 1.73L1.11 3l2.11 2.11C1.91 6.57 1 8.19 1 12c1.73 4.39 6 7.5 11 7.5 1.85 0 3.6-.49 5.12-1.35L19.73 21l1.27-1.27-18.61-18zM12 17c-2.76 0-5-2.24-5-5 0-.77.18-1.5.49-2.15l1.6 1.6c-.05.17-.09.36-.09.55 0 1.66 1.34 3 3 3 .19 0 .38-.03.55-.09l1.6 1.6c-.65.31-1.38.49-2.15.49zm4.84-3.27l-1.43-1.43c.1-.41.14-.82.14-1.3 0-1.66-1.34-3-3-3-.48 0-.89.04-1.3.14L9.82 6.71C10.51 6.26 11.23 6 12 6c2.76 0 5 2.24 5 5 0 .77-.26 1.49-.71 2.18l-1.45-1.45z"/>`;

        togglePass.addEventListener('click', () => {
            const isPass = pwdInput.type === 'password';
            pwdInput.type = isPass ? 'text' : 'password';
            eyeIcon.innerHTML = isPass ? eyeClose : eyeOpen;
            togglePass.setAttribute('aria-label', isPass ? 'Sembunyikan password' : 'Tampilkan password');
        });

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('btnLogin');
            btn.disabled = true;
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="animation:spin 1s linear infinite"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg> Memproses...`;
        });
    </script>
</body>
</html>
