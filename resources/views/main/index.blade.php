@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')

    {{-- ===================== --}}
    {{-- TOP STAT CARDS --}}
    {{-- ===================== --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>234%</h3>
                    <p>New Accounts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>71%</h3>
                    <p>Total Expenses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>$1.45M</h3>
                    <p>Company Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>34</h3>
                    <p>New Employees</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- CHART SECTION --}}
    {{-- ===================== --}}
    <div class="row">
        {{-- BAR + LINE --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Traffic Sources</h3>
                </div>
                <div class="card-body">
                    <canvas id="trafficChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- DONUT --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Income</h3>
                </div>
                <div class="card-body text-center">
                    <canvas id="incomeChart" height="200"></canvas>
                    <p class="mt-2"><strong>75%</strong> Spending Target</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- INFO BOX (BISA TETAP) --}}
    {{-- ===================== --}}
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-primary">
                    <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Users</span>
                    <span class="info-box-number">120</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-user-shield"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Roles</span>
                    <span class="info-box-number">5</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning">
                    <i class="fas fa-history"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Activity</span>
                    <span class="info-box-number">32</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger">
                    <i class="fas fa-cogs"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">System</span>
                    <span class="info-box-number">OK</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- WELCOME & QUICK MENU --}}
    {{-- ===================== --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Welcome</h3>
                </div>
                <div class="card-body">
                    <p>
                        Halo, <strong>{{ auth()->user()->fullname }}</strong><br>
                        Selamat bekerja di sistem <strong>MARINDOTECH</strong>.
                    </p>
                    <a href="{{ url('users') }}" class="btn btn-primary">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="{{ url('activity-log') }}" class="btn btn-warning">
                        <i class="fas fa-history"></i> Log Activity
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">System Info</h3>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> {{ auth()->user()->username }}</p>
                    <p><strong>Role ID:</strong> {{ auth()->user()->rolesid }}</p>
                    <p><strong>Last Login:</strong> {{ auth()->user()->lastlogin ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- ===================== --}}
{{-- CHART JS --}}
{{-- ===================== --}}
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        new Chart(document.getElementById('trafficChart'), {
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                        type: 'bar',
                        label: 'Website',
                        data: [400, 500, 300, 600, 700, 450, 380],
                        backgroundColor: '#007bff'
                    },
                    {
                        type: 'line',
                        label: 'Social Media',
                        data: [300, 420, 380, 450, 520, 390, 410],
                        borderColor: '#28a745',
                        fill: false
                    }
                ]
            }
        });

        new Chart(document.getElementById('incomeChart'), {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Remaining'],
                datasets: [{
                    data: [75, 25],
                    backgroundColor: ['#17a2b8', '#e9ecef']
                }]
            }
        });
    </script>
@endsection
