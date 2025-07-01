@extends('layouts/contentNavbarLayout', ['navbarBreadcrumbActive' => 'Management Dashboard'])

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@section('content')
<div class="container-fluid h-100">
    <div class="row g-6 h-100">
        <!-- Items Navigation -->
        <div class="col-12 col-lg-3">
            <div class="card mb-6 h-100">
                <div class="card-header d-flex justify-content-start align-items-center mb-6">
                    <h4 class="mb-0">All Categories</h4>
                </div>
                <div class="card-body h-100">
                    <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
                        <ul class="nav nav-align-left nav-pills flex-column">
                            <li class="nav-item mb-1">
                                <div class="d-flex justify-content-start align-items-center mb-6 nav-link active">
                                    <div class="avatar me-3">
                                        <img src="{{asset('assets/img/elements/azurelogo.jpg')}}" alt="Avatar" class="rounded">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="mb-0 text-lg">Azure</div>
                                        <span class="small">brawser@password.com</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Items Navigation -->

        <!-- Items Details -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="store_details" role="tabpanel">
                    <div class="card mb-6 bg-transparent shadow-none">
                    <div class="card-header d-flex justify-content-start align-items-center mb-6 px-0">
                        <div class="me-3">
                                <img src="{{asset('assets/img/elements/azurelogo.jpg')}}" width="70" class="rounded" alt="Azure Logo">
                            </div>
                            <div class="d-flex">
                                <h4 class="mb-0">Azure</h4>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        <div class="mb-6 border rounded-3">
                            <div class="d-flex justify-content-start align-items-center border-bottom p-3">
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-primary">Username</h6>
                                    <span>brawser@password.com</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start align-items-center border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div>
                                        <h6 class="mb-0 text-primary">Password</h6>
                                        <span>••••••••••</span>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary btn-sm" onclick="navigator.clipboard.writeText('••••••••••')">Copy</button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start align-items-center p-3">
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-primary">OTP</h6>
                                    <span>930 <span class="text-muted">•</span> 657</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mb-6">
                            <h6 class="mb-0 text-primary">Website</h6>
                            <a href="https://www.hurryapp.com" class="text-body">https://www.hurryapp.com</a>
                        </div>

                        <div class="d-flex flex-column mb-6">
                            <h6 class="mb-0 text-primary">Note</h6>
                            <p class="text-body">
                                This is a sample note for the <strong> Azure </strong> account. It can contain additional information about the account, such as security tips or usage instructions.
                            </p>
                        </div>

                        <div class="d-flex flex-column mb-6">
                            <h6 class="mb-0 text-primary">tags</h6>
                            <div class="d-flex flex-wrap">
                                <span class="badge bg-label-primary me-1 mb-1">azure</span>
                                <span class="badge bg-label-secondary me-1 mb-1">cloud</span>
                                <span class="badge bg-label-success me-1 mb-1">devops</span>
                                <span class="badge bg-label-info me-1 mb-1">security</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column text-lighter small justify-content-around align-items-center">
                            <span>
                                Created At: 2023-10-01, 12:00:00
                            </span>
                            <span>
                                Updated At: 2023-10-05, 14:30:00
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Items Details-->
    </div>
  </div>
</div>
@endsection
