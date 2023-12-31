@extends('layouts.main')

@section('content')
<main id="main" class="main">
  @if (session()->has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <section class="section dashboard">
    <div class="row">
      <div class="col-lg-6 row">
        <div class="col-xxl-12 col-md-12">
          <div class="pagetitle">
            <h1>Target Departement Quality</h1>
          </div>
          <div class="card info-card sales-card">
            <div class="card-body row">
              <div class="col-lg-6">
                <div class="card-title d-flex justify-content-between">
                  <h5>IPQC</h5>
                </div>

                <table class="table table-fixed table-bordered table-striped" id="tableMesinRusak">
                  <thead class="mt-4">
                    <tr>
                      <th>Departement</th>
                      <th>Target</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>CAM</td>
                      <td>{{ $targetQuality->target_cam_ipqc }}</td>
                    </tr>
                    <tr>
                      <td>CNC</td>
                      <td>{{ $targetQuality->target_cnc_ipqc }}</td>
                    </tr>
                    <tr>
                      <td>MFG2</td>
                      <td>{{ $targetQuality->target_mfg_ipqc }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-lg-6">
                <div class="card-title d-flex justify-content-between">
                  <h5>OQC</h5>
                </div>

                <table class="table table-fixed table-bordered table-striped" id="tableMesinRusak">
                  <thead class="mt-4">
                    <tr>
                      <th>Departement</th>
                      <th>Target</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>CAM</td>
                      <td>{{ $targetQuality->target_cam_oqc }}</td>
                    </tr>
                    <tr>
                      <td>CNC</td>
                      <td>{{ $targetQuality->target_cnc_oqc }}</td>
                    </tr>
                    <tr>
                      <td>MFG2</td>
                      <td>{{ $targetQuality->target_mfg_oqc }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editTargetQuality">
                Edit
              </button>

              @include('target.components.editQuality')
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 row">
        <div class="col-xxl-12 col-md-12">
          <div class="pagetitle">
            <h1>Target Departement Maintenance</h1>
          </div>
          <div class="card info-card sales-card">
            <div class="card-body row">
              <div class="col-lg-12">
                <div class="card-title d-flex justify-content-between">
                  <h5>Target Downtime selama satu bulan</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                  <h1>{{ $targetMaintenance->target_maintenance }}</h1>
                </div>
              </div>
              <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editTargetMaintenance">
                Edit
              </button>

              @include('target.components.editMaintenance')
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 row">
        <div class="col-xxl-12 col-md-12">
          <div class="pagetitle">
            <h1>Target Departement Sales</h1>
          </div>
          <div class="card info-card sales-card">
            <div class="card-body row">
              <div class="col-lg-12">

                <div class="card-title d-flex justify-content-between">
                  <h5>Target Sales Bulanan</h5>
                </div>

                <table class="table table-fixed table-bordered table-striped" id="tableMesinRusak">
                  <thead class="mt-4">
                    <tr>
                      <th>Bulan</th>
                      <th>Target</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Januari</td>
                      <td>{{ $targetSalesShow->januari }}</td>
                    </tr>
                    <tr>
                      <td>Februari</td>
                      <td>{{ $targetSalesShow->februari }}</td>
                    </tr>
                    <tr>
                      <td>Maret</td>
                      <td>{{ $targetSalesShow->maret }}</td>
                    </tr>
                    <tr>
                      <td>April</td>
                      <td>{{ $targetSalesShow->april }}</td>
                    </tr>
                    <tr>
                      <td>Mei</td>
                      <td>{{ $targetSalesShow->mei }}</td>
                    </tr>
                    <tr>
                      <td>Juni</td>
                      <td>{{ $targetSalesShow->juni }}</td>
                    </tr>
                    <tr>
                      <td>Juli</td>
                      <td>{{ $targetSalesShow->juli }}</td>
                    </tr>
                    <tr>
                      <td>Agustus</td>
                      <td>{{ $targetSalesShow->agustus }}</td>
                    </tr>
                    <tr>
                      <td>September</td>
                      <td>{{ $targetSalesShow->september }}</td>
                    </tr>
                    <tr>
                      <td>Oktober</td>
                      <td>{{ $targetSalesShow->oktober }}</td>
                    </tr>
                    <tr>
                      <td>November</td>
                      <td>{{ $targetSalesShow->november }}</td>
                    </tr>
                    <tr>
                      <td>Desember</td>
                      <td>{{ $targetSalesShow->desember }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editTargetSales">
                Edit
              </button>

              @include('target.components.editSales')
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
