<!-- resources/views/dataset/import.blade.php -->
@extends('layouts.main')

@section('title', 'Import Excel File')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Import Excel File
                    </h4>
                    <small>Upload any Excel file and let the system automatically detect columns</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('dataset.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="mb-4">
                                    <label for="file" class="form-label h5">
                                        <i class="fas fa-file-excel me-2 text-success"></i>Select Excel File
                                    </label>
                                    <input type="file"
                                        class="form-control form-control-lg @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Supported formats: .xlsx, .xls, .csv (Maximum 10MB)
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-magic me-2"></i>Smart Column Detection</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="mb-0">
                                                <li>Automatically detects column headers</li>
                                                <li>Supports any number of columns</li>
                                                <li>Preserves original data types</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-0">
                                                <li>Handles special characters</li>
                                                <li>Clean column name formatting</li>
                                                <li>Dynamic table generation</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-upload me-2"></i>Import Dataset
                                    </button>
                                    <a href="{{ route('dataset.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Datasets
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Example Preview -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview: How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Your Excel File:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Laptop HP</td>
                                            <td>15000000</td>
                                            <td>25</td>
                                            <td>Electronics</td>
                                        </tr>
                                        <tr>
                                            <td>Mouse Wireless</td>
                                            <td>250000</td>
                                            <td>100</td>
                                            <td>Accessories</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Will be displayed as:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Category</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Laptop HP</td>
                                            <td>15,000,000</td>
                                            <td>25</td>
                                            <td>Electronics</td>
                                            <td><i class="fas fa-eye text-primary"></i></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Mouse Wireless</td>
                                            <td>250,000</td>
                                            <td>100</td>
                                            <td>Accessories</td>
                                            <td><i class="fas fa-eye text-primary"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
