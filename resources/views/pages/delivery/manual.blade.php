@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="card-header mb-2">
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <h6 class="card-title mb-0">Jadwal Pengiriman</h6>
                </div>
                <div class="col-md-6 text-right">
                    <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kuhlModal">Tambah Data Baru</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="dataTableExample" class="table">
                <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Tanggal Order</th>
                    <th>Tanggal Kirim</th>
                    <th>Target Kirim</th>
                    <th>SO Number</th>
                    <th>Ekspedisi</th>
                    <th>Data Barang</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($data as $data)
                    @php 
                        $jsonData = json_decode($data->items, true);
                    @endphp
                    <tr>
                        <td>{{$data->customer}}</td>
                        <td>{{$data->tanggal_order}}</td>
                        <td>{{$data->tanggal_kirim}}</td>
                        <td>{{$data->target_kirim}}</td>
                        <td><a href="{{route('manual-delivery.edit', $data->id)}}">{{$data->nomor_so}}</a></td>
                        <td>{{$data->ekspedisi}}</td>
                        <td>
                            @if ($jsonData !== null)
                                @foreach ($jsonData['nama_barang'] as $index => $namaBarang)
                                    <p>Nama Barang: {{$namaBarang}}</p>
                                    <p>Total Order: {{$jsonData['total_order'][$index]}}</p>
                                    @if(isset($jsonData['order_dikirim'][$index]))
                                        <p>Orderan Terkirim: {{$jsonData['order_dikirim'][$index]}}</p>
                                    @endif
                                    <p>Sisa Order: {{$jsonData['sisa_order'][$index]}}</p>
                                @endforeach
                            @else
                                <p>Nama Barang: {{$data->nama_barang}}</p>
                                <p>Total Order: {{$data->total_order}}</p>
                                <p>Sisa Order: {{$data->sisa_order}}</p>
                            @endif
                        </td>
                        <form action="{{ route('manual-delivery.update', $data->id) }}" method="POST">
                        <td>
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-control">
                                <option value="Done" {{$data->status == 'Done' ? 'selected' : ''}}>Done</option>
                                <option value="On Progress" {{$data->status == 'On Progress' ? 'selected' : ''}}>On Progress</option>
                                <option value="On Delivery" {{$data->status == 'On Delivery' ? 'selected' : ''}}>On Delivery</option>
                                <option value="Delayed" {{$data->status == 'Delayed' ? 'selected' : ''}}>Delayed</option>
                                <option value="Cancel" {{$data->status == 'Cancel' ? 'selected' : ''}}>Cancel</option>
                                <option value="On Hold" {{$data->status == 'On Hold' ? 'selected' : ''}}>On Hold</option>
                                <option value="Dikirim Sebagian" {{$data->status == 'Dikirim Sebagian' ? 'selected' : ''}}>Dikirim Sebagian</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-primary w-100 mt-2">Update Status</button>
                        </td>
                        </form>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" id="kuhlModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Buat Data Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('manual-delivery.store')}}" method="POST" id="orderForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Tanggal Order</label>
                            <input type="date" class="form-control" name="tanggal_order" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Tanggal Kirim</label>
                            <input type="date" class="form-control" name="tanggal_kirim" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Nomor SO</label>
                            <input type="text" class="form-control" name="nomor_so" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Ekspedisi</label>
                            <input type="text" class="form-control" name="ekspedisi" required>
                        </div>
                    </div>
                    <div id="itemContainer">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" name="nama_barang[]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="" class="form-label">Total Order</label>
                                <input type="text" class="form-control" name="total_order[]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="" class="form-label">Orderan Yang Dikirim</label>
                                <input type="text" class="form-control" name="order_dikirim[]">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="" class="form-label">Sisa Order</label>
                                <input type="text" class="form-control" name="sisa_order[]">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addButton" class="btn btn-primary mb-2">Tambah Jenis Barang</button>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Driver</label>
                            <select name="driver" class="form-control" id="">
                                <option value="-">-</option>
                                <option value="Adi Rusman">Adi Rusman</option>
                                <option value="Aas">Aas</option>
                                <option value="Febri Andrianto">Febri Andrianto</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="" class="form-label">Status</label>
                            <select name="status" class="form-control" id="">
                                <option value="Done">Done</option>
                                <option value="On Progress">On Progress</option>
                                <option value="On Delivery">On Delivery</option>
                                <option value="Delayed">Delayed</option>
                                <option value="Cancel">Cancel</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Dikirim Sebagian">Dikirim Sebagian</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan">
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="btn btn-primary w-100" type="submit">Simpan Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    $(document).ready(function() {
    // Listen for changes in the status dropdown
    $('select.status-dropdown').change(function() {
        const status = $(this).val();
        const dataId = $(this).data('id');
        
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // Send an AJAX request to update the status
        $.ajax({
            url: '/update-status',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: dataId,
                status: status
            },
            success: function(response) {
                // Handle the response from the server if needed
                console.log('Status updated successfully:', response);
            },
            error: function(error) {
                console.error('Error updating status:', error);
                console.error('XHR status:', status);
                console.error('XHR response:', xhr.responseText);
            }
        });
    });
});
</script>
<script>
    $(document).ready(function () {
        // Counter for unique IDs
        var uniqueIdCounter = 0;

        // Add new row on button click
        $("#addButton").on("click", function () {
            var newItem = $("#itemContainer .row:first").clone();

            // Update IDs to ensure uniqueness
            newItem.find('input').each(function () {
                var oldId = $(this).attr('id');
                var newId = oldId + "_" + uniqueIdCounter;
                $(this).attr('id', newId).val('');
            });

            $("#itemContainer").append(newItem);
            uniqueIdCounter++;
        });
    });
</script>

@endpush