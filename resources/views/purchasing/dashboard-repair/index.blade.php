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

        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex justify-content-between">
              <h5>Data Mesin Rusak</h5>
            </div>

            <table border="0" cellspacing="5" cellpadding="5">
              <form action="/export/machine-repairs" method="post">
                @csrf
                <tbody>
                  <tr>
                    <td scope="col">Minimum date:</td>
                    <td scope="col"><input type="text" class="form-control" id="minRusak" name="min"></td>
                    <td rowspan="2"><button type="submit" class="btn btn-success">Export</button></td>
                  </tr>
                  <tr>
                    <td scope="col">Maximum date:</td>
                    <td scope="col"><input type="text" class="form-control" id="maxRusak" name="max"></td>
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
                </tr>
              </thead>
              <tbody>
                @foreach ($machineRepairs as $machineRepair)
                <tr>
                  <td hidden style="width: 10; ">{{ $machineRepair->search }}</td>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td style="width: 1000px">{{ $machineRepair->dataMesin->no_mesin }}</td>
                  <td>{{ $machineRepair->dataMesin->tipe_mesin }}</td>
                  <td>{{ $machineRepair->dataMesin->tipe_bartop }}</td>
                  <td>{{ $machineRepair->pic }}</td>
                  <td>{{ $machineRepair->request }}</td>
                  <td>{{ $machineRepair->analisa }}</td>
                  <td>{{ $machineRepair->aksi }}</td>
                  <td>{{ $machineRepair->sparepart }}</td>
                  <td>{{ $machineRepair->prl }}</td>
                  <td>{{ $machineRepair->po }}</td>
                  <td>{{ $machineRepair->kedatangan_po }}</td>
                  <td>{{ $machineRepair->kedatangan_prl }}</td>
                  <td>{{ $machineRepair->tgl_kerusakan }}</td>
                  <td>{{ $machineRepair->status_mesin }}</td>
                  <td id='downtime{{ $machineRepair->id }}' class='bg-success text-light'>
                    {!! $machineRepair->downtime !!}
                  </td>
                  <td>{{ $machineRepair->status_aktifitas }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        </div>

      </div>
    </div>
  </section>

</main>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.js"></script>

@include('maintenance.components.dashboard-repair.dataTable')

@include('run-downtime.index')

@endsection
