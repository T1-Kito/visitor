<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            font-family: "Manrope", sans-serif;
            background: radial-gradient(circle at top left, #d7ecff, #eef4fa 45%, #f6f9fc 100%);
        }
        .login-wrap {
            width: min(420px, 92vw);
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
            overflow: visible;
            width: auto;
            height: 88px;
        }
        .brand-pill.has-logo img {
            width: auto;
            height: 100%;
            max-width: 280px;
            object-fit: contain;
            display: block;
        }
        .login-brand-copy {
            min-width: 0;
            text-align: center;
        }
        .login-brand {
            flex-direction: column;
            justify-content: center;
            text-align: center;
            gap: 20px !important;
        }
        .login-card .form-label {
            margin-bottom: 4px;
            font-size: .9rem;
            font-weight: 500;
        }
        .login-card .form-control {
            padding: .55rem .75rem;
        }
        @media (max-width: 420px) {
            .brand-pill {
                width: 88px;
                height: 56px;
            }
            .brand-pill.has-logo {
                height: 72px;
            }
            .login-brand-copy h1 {
                font-size: 1rem;
            }
            .login-brand-copy p {
                font-size: .82rem;
            }
        }
        .btn-brand {
            background: linear-gradient(130deg, #0f7ec7, #0b5f97);
            border: 0;
            color: #fff;
            font-weight: 700;
        }
        .btn-brand:hover {
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

                <div class="alert alert-light border mt-3 mb-0 py-2 small">
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
