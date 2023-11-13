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
    <h1>Master Data Mesin</h1>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex justify-content-between">
              <h5>Data Mesin</h5>
            </div>

            <table class="table table-bordered table-striped"
              style="" id="tableMesin">
              <thead class="mt-4">
                <tr>
                  <th scope="col">No</th>
                  <th scope="col">No Mesin</th>
                  <th scope="col">Tipe Mesin</th>
                  <th scope="col">Tipe Bartop</th>
                  <th scope="col">Serial Mesin</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($machines as $machine)
                <tr>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td>{{ $machine->no_mesin }}</td>
                  <td>{{ $machine->tipe_mesin }}</td>
                  <td>{{ $machine->tipe_bartop }}</td>
                  <td>{{ $machine->seri_mesin }}</td>
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

{{-- komponen datatable --}}
@include('maintenance.components.machines.dataTable')

@endsection
