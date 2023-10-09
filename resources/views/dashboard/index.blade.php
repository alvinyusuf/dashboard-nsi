@extends('layouts.main')

@section('content')
@inject('carbon', 'Carbon\Carbon')
<main id="main" class="main">
  @if (session()->has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif
  <div class="pagetitle">
    <h1>Dashboard Data Mesin Rusak</h1>
  </div>

  <section class="section dashboard">
    <div class="row">
      <div class="col-lg-12">

          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>
                  <li><a type="button" onclick="monthFilter('{{ $carbon->now()->format('F Y') }}')" class="dropdown-item">{{ $carbon->now()->format('F Y') }}</a></li>
                  <li><a type="button" onclick="monthFilter('{{ $carbon->now()->subMonth()->format('F Y') }}')" class="dropdown-item">{{ $carbon->now()->subMonth()->format('F Y') }}</a></li>
                  <li><a type="button" onclick="monthFilter('{{ $carbon->now()->subMonths(2)->format('F Y') }}')" class="dropdown-item">{{ $carbon->now()->subMonths(2)->format('F Y') }}</a></li>
                </ul>
              </div>
              <div class="card-body">
                <h5 class="card-title">Total Downtime <span>| </span><span id="monthFilter">{{ $carbon::now()->format("F Y") }}</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock"></i>
                  </div>
                  <div class="ps-3">
                    <h6 class="fs-4" id="totalDowntime">20</h6>
                    <span class="text-success small pt-1 fw-bold">20</span> <span
                      class="text-muted small pt-2 ps-1">Mesin down bulan ini</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        {{-- javascript untuk filtering bulan total downtime --}}
        @include('components.monthFiltering')

        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex justify-content-between">
              <h5>Data Mesin Rusak</h5>
              <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#tambahData">
                Tambah Data
              </button>
              @include('components.dashboard.modals.tambah')
            </div>

            <table border="0" cellspacing="5" cellpadding="5">
              <form action="/export-mesin-rusak" method="post">
                @csrf
                <tbody>
                  <tr>
                    <td scope="col">Minimum date:</td>
                    <td scope="col"><input type="text" id="minRusak" name="min"></td>
                    <td rowspan="2"><button type="submit" class="btn btn-success">Export</button></td>
                  </tr>
                  <tr>
                    <td scope="col">Maximum date:</td>
                    <td scope="col"><input type="text" id="maxRusak" name="max"></td>
                  </tr>
                </tbody>
              </form>
            </table>

            <table class="table table-fixed table-bordered table-striped" style="overflow-x: scroll; display: block; table-layout: fixed; width: 100%;"
            id="tableMesinRusak">
              <thead class="mt-4">
                <tr>
                  <th hidden style="width: 10; ">search</th>
                  <th scope="col">No</th>
                  <th scope="col">No Mesin</th>
                  <th scope="col">Tipe Mesin</th>
                  <th scope="col">Tipe Bartop</th>
                  <th scope="col">PIC</th>
                  <th scope="col">Request</th>
                  <th scope="col">Analisa</th>
                  <th scope="col">Aksi</th>
                  <th scope="col">Sparepart</th>
                  <th scope="col">PRL</th>
                  <th scope="col">PO</th>
                  <th scope="col">Kedatangan PO</th>
                  <th scope="col">Kedatangan Request PRL</th>
                  <th scope="col">Tgl Kerusakan</th>
                  <th scope="col">Status Mesin</th>
                  <th scope="col">Downtime</th>
                  <th scope="col">Status Aktivitas</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($machinesOnRepair as $machineOnRepair)
                <tr>
                  <td hidden style="width: 10; ">{{ $machineOnRepair->search }}</td>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td style="width: 1000px">{{ $machineOnRepair->dataMesin->no_mesin }}</td>
                  <td>{{ $machineOnRepair->dataMesin->tipe_mesin }}</td>
                  <td>{{ $machineOnRepair->dataMesin->tipe_bartop }}</td>
                  <td>{{ $machineOnRepair->pic }}</td>
                  <td>{{ $machineOnRepair->request }}</td>
                  <td>{{ $machineOnRepair->analisa }}</td>
                  <td>{{ $machineOnRepair->action }}</td>
                  <td>{{ $machineOnRepair->sparepart }}</td>
                  <td>{{ $machineOnRepair->prl }}</td>
                  <td>{{ $machineOnRepair->po }}</td>
                  <td>{{ $machineOnRepair->kedatangan_po }}</td>
                  <td>{{ $machineOnRepair->kedatangan_prl }}</td>
                  <td>{{ $machineOnRepair->tgl_kerusakan }}</td>
                  <td>{{ $machineOnRepair->status_mesin }}</td>
                  <td id='downtime{{ $machineOnRepair->id }}' class='bg-success text-light'>
                    {!! $machineOnRepair->downtime !!}
                  </td>
                  <td>
                    {{ $machineOnRepair->status_aktifitas }}

                  </td>
                  <td class="text-center">
                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="modal"
                    data-bs-target="#selesaiModal{{ $machineOnRepair->id }}">Selesai</button>
                    @include('components.dashboard.modals.selesai')
                    <button class="btn btn-warning mb-1" type="button" data-bs-toggle="modal"
                    data-bs-target="#editModal{{ $machineOnRepair->id }}">Edit</button>
                    @include('components.dashboard.modals.edit')
                    <button class="btn btn-danger mb-1" type="button" data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $machineOnRepair->id }}">Hapus</button>
                    @include('components.dashboard.modals.hapus')
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        </div>

      </div>
    </div>
  </section>

  <script>
    const jsonString = `<?= json_encode($jsMachinesOnRepair) ?>`;
    const jsonArray = JSON.parse(jsonString);
    console.log(jsonArray);
  </script>

</main>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.js"></script>

@include('components.dashboard.dataTable')

@include('components.timerDowntime')

@endsection
