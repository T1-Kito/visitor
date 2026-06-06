<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f7ec7">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $loginBrand['title'] ?? 'Đăng nhập' }}</title>
    @if (! empty($loginBrand['favicon_url']))
        <link rel="icon" href="{{ $loginBrand['favicon_url'] }}">
        <link rel="shortcut icon" href="{{ $loginBrand['favicon_url'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            min-height: 100%;
        }
        body {
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px 16px;
            font-family: "Manrope", sans-serif;
            background: radial-gradient(circle at top left, #d7ecff, #eef4fa 45%, #f6f9fc 100%);
            -webkit-tap-highlight-color: transparent;
        }
        .login-wrap {
            width: min(420px, 100%);
        }
        .login-card {
            border: 1px solid #e4ebf4;
            border-radius: 16px;
            box-shadow: 0 18px 35px rgba(17, 44, 76, 0.1);
        }
        .brand-pill {
            width: 96px;
            height: 60px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(130deg, #0f7ec7, #085f99);
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.05em;
            overflow: hidden;
            flex: 0 0 auto;
            margin: 0 auto;
        }
        .brand-pill.has-logo {
            background: transparent;
            border: 0;
            border-radius: 0;
            overflow: hidden;
            width: auto;
            height: 88px;
            display: block;
            padding: 0;
        }
        .brand-pill.has-logo img {
            width: auto;
            height: 108px;
            max-width: 280px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .login-brand-copy {
            min-width: 0;
            text-align: center;
        }
        .login-brand {
            flex-direction: column;
            justify-content: center;
            text-align: center;
            gap: 18px !important;
        }
        .login-brand-copy h1 {
            line-height: 1.25;
        }
        .login-card .form-label {
            margin-bottom: 4px;
            font-size: .9rem;
            font-weight: 500;
        }
        .login-card .form-control {
            padding: .65rem .85rem;
            font-size: 16px;
            border-radius: 10px;
        }
        .login-card .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            margin-top: .2rem;
        }
        .login-card .form-check-label {
            font-size: .9rem;
            padding-left: 4px;
        }
        .login-card .btn-brand {
            min-height: 48px;
            border-radius: 10px;
            font-size: 1rem;
        }
        .login-card .demo-credentials {
            font-size: .82rem;
            line-height: 1.45;
            background: #f4f7fb;
            border-color: #e1e8f1 !important;
        }
        @media (max-width: 480px) {
            body {
                padding: 12px;
                align-items: start;
                padding-top: max(16px, env(safe-area-inset-top));
            }
            .login-card {
                border-radius: 14px;
                box-shadow: 0 10px 24px rgba(17, 44, 76, 0.08);
            }
            .login-card .card-body {
                padding: 1.25rem !important;
            }
            .login-brand {
                gap: 12px !important;
                margin-bottom: 1rem !important;
            }
            .brand-pill {
                width: 84px;
                height: 54px;
            }
            .brand-pill.has-logo {
                height: 64px;
                max-width: 80%;
            }
            .brand-pill.has-logo img {
                height: 84px;
                max-width: 100%;
            }
            .login-brand-copy h1 {
                font-size: 1.05rem;
                margin-bottom: 2px !important;
            }
            .login-brand-copy p {
                font-size: .82rem;
            }
        }
        @media (max-width: 360px) {
            .login-card .card-body {
                padding: 1rem !important;
            }
            .brand-pill.has-logo {
                height: 56px;
            }
            .brand-pill.has-logo img {
                height: 56px;
            }
        }
        .btn-brand {
            background: linear-gradient(130deg, #0f7ec7, #0b5f97);
            border: 0;
            color: #fff;
            font-weight: 700;
        }
        .btn-brand:hover, .btn-brand:focus, .btn-brand:active {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <div class="card login-card">
            <div class="card-body p-4">
                <div class="login-brand d-flex align-items-center gap-2 mb-3">
                    <div class="brand-pill {{ ! empty($loginBrand['logo_url']) ? 'has-logo' : '' }}">
                        @if (! empty($loginBrand['logo_url']))
                            <img src="{{ $loginBrand['logo_url'] }}" alt="{{ $loginBrand['title'] ?? 'Logo' }}">
                        @else
                            VMS
                        @endif
                    </div>
                    <div class="login-brand-copy">
                        <h1 class="h5 mb-1 fw-bold">{{ $loginBrand['title'] ?? 'Visitor Management System' }}</h1>
                        <p class="text-secondary mb-0">{{ $loginBrand['subtitle'] ?? 'Đăng nhập vào hệ thống vận hành' }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('login.attempt') }}" class="d-grid gap-2" data-login-form data-csrf-url="{{ route('csrf-token') }}">
                    @csrf
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="admin@company.local" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <button class="btn btn-brand py-2 mt-2" type="submit" data-login-submit>Đăng nhập</button>
                </form>

                <div class="alert alert-light border mt-3 mb-0 py-2 demo-credentials">
                    Tai khoan demo: <strong>superadmin@company.local</strong> / <strong>Admin@123</strong>
                </div>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const form = document.querySelector('[data-login-form]');
            const submitButton = document.querySelector('[data-login-submit]');
            const tokenInput = form?.querySelector('input[name="_token"]');
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            let isSubmitting = false;

            form?.addEventListener('submit', async (event) => {
                if (isSubmitting) {
                    return;
                }

                event.preventDefault();

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Dang kiem tra...';
                }

                try {
                    const response = await fetch(form.dataset.csrfUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            Accept: 'application/json',
                            'Cache-Control': 'no-cache',
                        },
                    });
                    const payload = await response.json();

                    if (payload.token && tokenInput) {
                        tokenInput.value = payload.token;
                    }

                    if (payload.token && tokenMeta) {
                        tokenMeta.setAttribute('content', payload.token);
                    }
                } catch (error) {
                    // If token refresh fails, still submit with the token rendered by Laravel.
                }

                isSubmitting = true;
                form.submit();
            });
        })();
    </script>
</body>
</html>
