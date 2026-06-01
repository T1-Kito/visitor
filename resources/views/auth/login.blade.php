<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Visitor Management</title>
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
            width: min(460px, 92vw);
        }
        .login-card {
            border: 1px solid #e4ebf4;
            border-radius: 18px;
            box-shadow: 0 18px 35px rgba(17, 44, 76, 0.1);
        }
        .brand-pill {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(130deg, #0f7ec7, #085f99);
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.05em;
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
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="brand-pill">VMS</div>
                    <div>
                        <h1 class="h5 mb-1 fw-bold">Visitor Management System</h1>
                        <p class="text-secondary mb-0">Dang nhap vao he thong van hanh</p>
                    </div>
                </div>

                <form method="post" action="{{ route('login.attempt') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="admin@company.local" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Mat khau</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ghi nho dang nhap</label>
                    </div>
                    <button class="btn btn-brand py-2" type="submit">Dang nhap</button>
                </form>

                <div class="alert alert-light border mt-4 mb-0">
                    Tai khoan demo: <strong>superadmin@company.local</strong> / <strong>Admin@123</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
