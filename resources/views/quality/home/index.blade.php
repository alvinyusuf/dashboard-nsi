@extends('layouts.main')

@section('content')
<main id="main" class="main">
  @if (session()->has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif
  <div class="pagetitle">
    <h1>Dashboard Data Claim NCR dan Lot Tag</h1>
  </div>

  <section class="section dashboard">
    <div class="row">

      <div class="col-xxl-12 col-md-12 mb-3 text-center">
        <button class="btn btn-success py-3" type="button" data-bs-toggle="modal" data-bs-target="#tambahData">
          Tambah Data
        </button>
      </div>
      @include('quality.components.home.modals.tambah')

      <div class="col-lg-12 row">

        <div class="col-xxl-6 col-md-6">
          <div class="card info-card sales-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li class="dropdown-item">Oktober</li>
                <li class="dropdown-item">September</li>
                <li class="dropdown-item">Agustus</li>
              </ul>
            </div>
            <div class="card-body">
              <div class="card-title d-flex justify-content-between">
                <h5>IPQC <span>| </span><span id="monthFilter">Oktober</span></h5>
              </div>

              <table class="table table-fixed table-bordered table-striped" id="tableMesinRusak">
                <thead class="mt-4">
                  <tr>
                    <th>Departement</th>
                    <th>Target</th>
                    <th>Aktual</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>CAM</td>
                    <td>6</td>
                    <td>{{ $camIpqc }}</td>
                  </tr>
                  <tr>
                    <td>CNC</td>
                    <td>12</td>
                    <td>{{ $cncIpqc }}</td>
                  </tr>
                  <tr>
                    <td>MFG2</td>
                    <td>5</td>
                    <td>{{ $mfgIpqc }}</td>
                  </tr>
                </tbody>
              </table>

              <a href="/quality/dashboard-ipqc"><button class="btn btn-primary">Detail</button></a>

              <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editTargetIpqc">
                Edit
              </button>

              @include('quality.components.home.modals.editIpqc')

            </div>
          </div>
        </div>

        <div class="col-xxl-6 col-md-6">
          <div class="card info-card sales-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li class="dropdown-item">Oktober</li>
                <li class="dropdown-item">September</li>
                <li class="dropdown-item">Agustus</li>
              </ul>
            </div>
            <div class="card-body">
              <div class="card-title d-flex justify-content-between">
                <h5>OQC <span>| </span><span id="monthFilter">Oktober</span></h5>
              </div>

              <table class="table table-fixed table-bordered table-striped" id="tableMesinRusak">
                <thead class="mt-4">
                  <tr>
                    <th>Departement</th>
                    <th>Target</th>
                    <th>Aktual</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>CAM</td>
                    <td>6</td>
                    <td>{{ $camOqc }}</td>
                  </tr>
                  <tr>
                    <td>CNC</td>
                    <td>12</td>
                    <td>{{ $cncOqc }}</td>
                  </tr>
                  <tr>
                    <td>MFG2</td>
                    <td>5</td>
                    <td>{{ $mfgOqc }}</td>
                  </tr>
                </tbody>
              </table>

              <a href="/quality/dashboard-oqc"><button class="btn btn-primary">Detail</button></a>

              <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editTargetOqc">
                Edit
              </button>

              @include('quality.components.home.modals.editOqc')

            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

</main>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.js"></script>

{{-- @include('maintenance.components.dashboard-repair.dataTable') --}}


@endsection
