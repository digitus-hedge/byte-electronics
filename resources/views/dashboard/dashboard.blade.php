@extends('layouts.app')

@push('styles')
    <style>
        .page-content {
            padding: calc(85px - 33px) calc(22px / 2) 60px calc(22px / 2) !important;
        }
        .metric-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .metric-label {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        .bg-primary-light {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }
        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1);
        }
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }
        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.1);
        }
        @media (max-width: 767.98px) {
            .metric-card {
                margin-bottom: 1rem;
            }
            .metric-value {
                font-size: 1.5rem;
            }
            .metric-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="card-title mb-0">Dashboard Overview</h4>
                <div class="dropdown">
                    <a class="dropdown-toggle text-reset" href="#" data-bs-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <span class="fw-semibold">Filter:</span> <span class="text-muted">All Time<i
                                class="mdi mdi-chevron-down ms-1"></i></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">All Time</a>
                        <a class="dropdown-item" href="#">Yearly</a>
                        <a class="dropdown-item" href="#">Monthly</a>
                        <a class="dropdown-item" href="#">Weekly</a>
                        <a class="dropdown-item" href="#">Today</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Users -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-primary-light text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <p class="metric-value">{{ $totalUsers }}</p>
                        <p class="metric-label">Total Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-success-light text-success">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <p class="metric-value">{{ $totalProducts }}</p>
                        <p class="metric-label">Total Products</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-info-light text-info">
                        <i class="bi bi-cart-fill"></i>
                    </div>
                    <div>
                        <p class="metric-value">{{ $totalOrders }}</p>
                        <p class="metric-label">Total Orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Last Week -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-warning-light text-warning">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div>
                        <p class="metric-value">{{ $lastWeekOrders }}</p>
                        <p class="metric-label">Orders Last Week</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Products Last Week -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-secondary-light text-secondary">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <p class="metric-value">{{ $lastWeekProducts }}</p>
                        <p class="metric-label">New Products Last Week</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection