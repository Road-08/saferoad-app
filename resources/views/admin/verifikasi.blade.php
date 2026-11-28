@extends('layouts.app')

@section('title', 'Verifikasi KTP Pengguna · Admin')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Verifikasi KTP Pengguna</h1>
    <table id="ktpTable" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>NIK</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

{{-- DataTables + jQuery (jika belum dimuat di layout) --}}
@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#ktpTable').DataTable({
        ajax: '{{ route('admin.verifikasi.data') }}',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'nik' },
            { data: 'identity_status' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
@endsection