@extends('admin.layouts.admin')

@section('title', 'Dashboard Reports')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-bar-chart"></i> Analytics Dashboard</h2>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Revenue</h6>
                    <h4>#</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Avg Order Value</h6>
                    <h4>#</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">New Customers</h6>
                    <h4>#</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Customer Retention</h6>
                    <h4>#</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex align-items-center gap-2">
                <input type="date" name="start_date" value="#" class="form-control">
                <input type="date" name="end_date" value="#" class="form-control">
                <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            </form>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Orders Summary</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Total Orders</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($labels as $i => $date)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                        <td>{{ $orders[$i] }}</td>
                        <td>${{ number_format($sales[$i], 2) }}</td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            // labels: @json($labels),
            datasets: [{
                label: 'Sales',
                // data: @json($sales),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
