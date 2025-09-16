@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Stat Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam fs-2 text-primary"></i>
                        <h5 class="mt-2">1,240</h5>
                        <p class="text-muted mb-0">Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-2 text-success"></i>
                        <h5 class="mt-2">8,540</h5>
                        <p class="text-muted mb-0">Customers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-check fs-2 text-warning"></i>
                        <h5 class="mt-2">3,210</h5>
                        <p class="text-muted mb-0">Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-2 text-danger"></i>
                        <h5 class="mt-2">$98,450</h5>
                        <p class="text-muted mb-0">Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">Recent Orders</h6>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1001</td>
                            <td>Rahul Sharma</td>
                            <td>iPhone 15</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>02 Sep 2025</td>
                            <td>$1200</td>
                        </tr>
                        <tr>
                            <td>#1002</td>
                            <td>Ananya Singh</td>
                            <td>MacBook Pro</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>01 Sep 2025</td>
                            <td>$2200</td>
                        </tr>
                        <tr>
                            <td>#1003</td>
                            <td>Vikram Patel</td>
                            <td>Samsung Galaxy S24</td>
                            <td><span class="badge bg-danger">Cancelled</span></td>
                            <td>31 Aug 2025</td>
                            <td>$999</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection