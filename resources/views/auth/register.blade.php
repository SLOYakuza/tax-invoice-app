<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija - Davčni Računi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            min-height: 100vh;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .register-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .register-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .register-body {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 12px 15px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            color: white;
        }
        .register-footer {
            text-align: center;
            padding: 20px 40px 30px;
            border-top: 1px solid #dee2e6;
        }
        .register-footer p {
            margin: 0;
            color: #666;
        }
        .register-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }
        .register-footer a:hover {
            text-decoration: underline;
        }
        .form-text {
            font-size: 0.875rem;
            color: #666;
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .icon-large {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .password-requirements {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 12px 15px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 0.875rem;
        }
        .password-requirements ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }
        .password-requirements li {
            margin: 4px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-container">
                    <div class="register-header">
                        <div class="icon-large">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h1>Davčni Računi</h1>
                        <p>Ustvari nov račun</p>
                    </div>

                    <div class="register-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Napaka pri registraciji!</strong>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Ime <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Vnesite svoje ime" required>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">E-pošta <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="vam@primer.com" required>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name" class="form-label">Ime Podjetja</label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Ime vašega podjetja">
                                        @error('company_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">Telefonska Številka</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+386 1 234 56 78">
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Geslo <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Vnesite geslo" required>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="password-requirements">
                                    <strong>Zahteve za geslo:</strong>
                                    <ul>
                                        <li>Najmanj 8 znakov</li>
                                        <li>Najmanj ena velika črka</li>
                                        <li>Najmanj ena številka</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Potrdi Geslo <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Ponovite geslo" required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-register">
                                <i class="fas fa-user-plus me-2"></i>Registracija
                            </button>
                        </form>
                    </div>

                    <div class="register-footer">
                        <p>
                            že imate račun?
                            <a href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Prijava
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
