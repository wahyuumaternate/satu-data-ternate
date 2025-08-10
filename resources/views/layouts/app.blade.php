<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dynamic Excel Import')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-scroll {
            max-height: 600px;
            overflow-y: auto;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .dataset-card {
            transition: transform 0.2s;
        }

        .dataset-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dataset.index') }}">
                <i class="fas fa-database me-2"></i>Dynamic Dataset
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dataset.index') }}">
                    <i class="fas fa-list me-1"></i>All Datasets
                </a>
                <a class="nav-link" href="{{ route('dataset.create') }}">
                    <i class="fas fa-upload me-1"></i>Import Excel
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">Dynamic Excel Import System &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>
