@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing'))

@section('page_header')
    <h1 class="page-title">

    </h1>
@stop

@section('content')
    @include('voyager::menus.partial.notice')

    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Total Complaints</h4>
                    <p>{{ $totalComplaints }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Pending Complaint</h4>
                    <p>{{ $pendingComplaints }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4>In Progress Complaint</h4>
                    <p>{{ $investigationComplaints }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Resolved Complaint</h4>
                    <p>{{ $detectedRecoveredComplaints }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>All Complaints</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Complainant</th>
                <th>FIR Number</th>
                <th>Status</th>
                <th>Police Station</th>
                <th>Officer</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        @forelse($complaints as $complaint)
            <tr>
                <td>{{ $complaint->id }}</td>
                <td>{{ $complaint->complainant_name }}</td>
                <td>{{ $complaint->fir_number }}</td>
                <td>
                    @switch($complaint->status)
                        @case(1) Pending @break
                        @case(2) Investigation @break
                        @case(3) Detected & Recovered @break
                        @case(4) Detected Not Recovered @break
                        @case(5) Mobile Recovered @break
                        @case(6) Closure @break
                        @default Unknown
                    @endswitch
                </td>
                <td>{{ $complaint->police_station_name }}</td>
                <td>{{ $complaint->officer_name }}</td>
                <td>{{ $complaint->created_at->format('d-m-Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="7">No complaints found.</td></tr>
        @endforelse
        </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{-- <h4 class="modal-title">
                        <i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}?
                    </h4> --}}
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <!-- DataTables -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "order": [],
                "language": {!! json_encode(__('voyager::datatable'), true) !!},
                "columnDefs": [{"targets": -1, "searchable":  false, "orderable": false}]
                @if(config('dashboard.data_tables.responsive')), responsive: true @endif
            });
        });


    </script>
@stop
