@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="align-self-center">Maintenance Tiket</h5>
                <div class="button">
                    <a href="{{route('maintenance-ticket.create')}}" class="btn btn-primary" data-bs-toggle="#" data-bs-target="#">Tambah Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>Nomor Tiket</th>
                                <th>Tanggal</th>
                                <th>Pengirim</th>
                                <th>Status</th>
                                <th>Kategori</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticketdata as $data)
                            <tr>
                                <td> <a href="#" data-bs-toggle="modal" data-bs-target="#modalUpdate{{$data->nomor}}">{{ $data->nomor }}</a> </td>
                                <td> {{ $data->tanggal }} </td>
                                <td> {{ $data->pengirim }} </td>
                                @if($data->status === 'Menunggu')
                                    <td><span class="badge bg-warning">{{ $data->status }}</span></td>
                                @elseif($data->status === 'Dikerjakan')
                                    <td><span class="badge bg-primary">{{ $data->status }}</span></td>
                                @else
                                    <td><span class="badge bg-success">{{ $data->status }}</span></td>
                                @endif
                                
                                @if($data->kategori === 'High')
                                    <td><span class="badge bg-danger">{{ $data->kategori }}</span></td>
                                @elseif($data->kategori === 'Medium')
                                    <td><span class="badge bg-warning">{{ $data->kategori }}</span></td>
                                @else
                                    <td><span class="badge bg-primary">{{ $data->kategori }}</span></td>
                                @endif
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#EditContact{{ $data->id}}">
                                                <i data-feather="eye" class="icon-sm me-2"></i>
                                                <span class="">Details</span>
                                            </a>
                                            <form action="{{ route('contact.destroy', $data->id) }}" method="POST" id="delete_contact" class="contactdelete"> 
                                                @csrf @method('DELETE') 
                                                <a class="dropdown-item d-flex align-items-center" href="#" onClick="showDeleteDataDialog('{{ $data->id }}')">
                                                    <i data-feather="trash" class="icon-sm me-2"></i>
                                                    <span class="">Delete</span>
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit atau Update -->
@foreach ($ticketdata as $data)
<div class="modal fade" id="modalUpdate{{$data->nomor}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Tiket {{$data->nomor}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('maintenance-ticket.update', $data->id )}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" value="{{$data->tanggal}}" required readonly>    
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Nomor Tiket</label>
                            <input type="text" class="form-control" name="nomor" value="{{$data->nomor}}" required readonly>    
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="" class="form-label">Permasalahan</label>
                            <textarea name="permasalahan" id="" class="form-control" cols="30" rows="10" readonly>{{$data->permasalahan}}</textarea>   
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Status</label>
                            <select name="status" id="" class="form-control">
                                <option value="Menunggu" {{$data->status == 'Menunggu' ? 'selected' : ''}}>Menunggu</option>
                                <option value="Dikerjakan" {{$data->status == 'Dikerjakan' ? 'selected' : ''}}>Dikerjakan</option>
                                <option value="Selesai" {{$data->status == 'Selesai' ? 'selected' : ''}}>Selesai</option>
                            </select>    
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Estimasi Waktu</label>
                            <input type="text" class="form-control" name="estimasi_waktu" value="{{$data->estimasi_waktu}}" placeholder="e.g 5 hari" required>    
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" value="{{$data->tanggal_mulai}}" required>    
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" value="{{$data->tanggal_selesai}}" required>    
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="btn btn-primary w-100" type="submit">Update Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert.js') }}"></script>
  <script>
    function showDeleteDataDialog(id) {
        Swal.fire({
            title: 'Hapus Data',
            text: 'Anda Yakin Akan Menghapus Data Ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the delete action here (e.g., send a request to delete the data)
                // Menggunakan ID yang diteruskan sebagai parameter ke dalam URL delete route
                const deleteUrl = "{{ route('contact.destroy', ':id') }}".replace(':id', id);
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                }).then((response) => {
                    // Handle the response as needed (e.g., show alert if data is deleted successfully)
                    if (response.ok) {
                        Swal.fire({
                            title: 'Contact Successfully Deleted',
                            icon: 'success',
                        }).then(() => {
                            window.location.reload(); // Refresh halaman setelah menutup alert
                        });
                    } else {
                        // Handle error response if needed
                        Swal.fire({
                            title: 'Contact Failed to Delete',
                            text: 'An error occurred while deleting data.',
                            icon: 'error',
                        });
                    }
                }).catch((error) => {
                    // Handle fetch error if needed
                    Swal.fire({
                        title: 'Contact Failed to Delete',
                        text: 'An error occurred while deleting data.',
                        icon: 'error',
                    });
                });
            }
        });
    }
</script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
        });
    @endif
</script>
<script>
    $(function() {
  'use strict';

  $(function() {
    $('#dataTablePCK').DataTable({
      "aLengthMenu": [
        [10, 30, 50, -1],
        [10, 30, 50, "All"]
      ],
      "iDisplayLength": 10,
      "language": {
        search: ""
      }
    });
    $('#dataTablePCK').each(function() {
      var datatable = $(this);
      // SEARCH - Add the placeholder for Search and Turn this into in-line form control
      var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
      search_input.attr('placeholder', 'Search');
      search_input.removeClass('form-control-sm');
      // LENGTH - Inline-Form control
      var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
      length_sel.removeClass('form-control-sm');
    });
  });

});
</script>
<script>
    $(function() {
  'use strict';

  $(function() {
    $('#DataTableArtikel').DataTable({
      "aLengthMenu": [
        [10, 30, 50, -1],
        [10, 30, 50, "All"]
      ],
      "iDisplayLength": 10,
      "language": {
        search: ""
      }
    });
    $('#DataTableArtikel').each(function() {
      var datatable = $(this);
      // SEARCH - Add the placeholder for Search and Turn this into in-line form control
      var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
      search_input.attr('placeholder', 'Search');
      search_input.removeClass('form-control-sm');
      // LENGTH - Inline-Form control
      var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
      length_sel.removeClass('form-control-sm');
    });
  });

});
</script>
@endpush