<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Davčni Računi - Upravljanje in Izdaja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature {
            text-align: center;
            padding: 30px 20px;
        }
        .feature i {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .feature h4 {
            margin: 15px 0;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 10px 30px;
            font-size: 1.1rem;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-file-invoice-dollar" style="color: #0d6efd;"></i>
                <strong>Davčni Računi</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Nadzorna Plošča</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Prijava</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registracija</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Upravljanje Davčnih Računov</h1>
            <p class="lead mb-4">Preprosta in hitira aplikacija za izdajo in upravljanje davčnih računov</p>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Pojdi na Nadzorno Ploščo</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">Registracija</a>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Prijava</a>
            @endauth
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-file-invoice"></i>
                    <h4>Preprosta Izdaja</h4>
                    <p>Hitro in preprosto ustvarjajte in upravljajte račune v le nekaj klikih.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-users"></i>
                    <h4>Upravljanje Klijentov</h4>
                    <p>Shranjujte podatke vseh svojih klijentov in jih hitro dostopajte.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-file-pdf"></i>
                    <h4>Izvoz v PDF</h4>
                    <p>Izvezte račune v PDF ali Excel format in jih pošljite klijentom.</p>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-calculator"></i>
                    <h4>Avtomatske Kalkulacije</h4>
                    <p>PDV in skupne vsote se avtomatsko izračunajo.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-lock"></i>
                    <h4>Varna Shramba</h4>
                    <p>Vaši podatki so varno shranjeni v naši bazi podatkov.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-chart-bar"></i>
                    <h4>Pregled in Analitika</h4>
                    <p>Oglejte si pregled svojih prihodkov in statusa računov.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center text-muted">
            <p>&copy; 2024 Davčni Računi. Vse pravice pridržane.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
