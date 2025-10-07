@extends('voyager::master')

@section('page_title', 'Railway Complaint Portal | '.$dataType->getTranslatedAttribute('display_name_plural'))

@php
    $user = auth()->user();
@endphp

@section('page_header')
    <div class="table-heading-sec">
        <h1 class="page-title">
            {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        <div class="table-heading-right-sec">
            @can('add',app($dataType->model_name))
                <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success">
                    <i class="voyager-plus"></i> {{ __('voyager::generic.add_new') }}
                </a>
            @endcan
            @can('add', app($dataType->model_name))
            @if($user->role_id == 2)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import_modal">
                    <i class="voyager-upload"></i> Import CSV
                </button>
            @endif
            @endcan
        </div>
    </div>
@stop

@section('content')
    @include('voyager::menus.partial.notice')

    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row mb-4 cards-row">
                            <div class="col-md-4">
                                <div class="card status-card" data-status="" style="cursor: pointer;">
                                    <div class="card-body">
                                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2499 37.5H20.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M37.4999 29.1667H20.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.33342 45.8333H41.6668C42.7718 45.8333 43.8316 45.3944 44.613 44.613C45.3944 43.8316 45.8334 42.7717 45.8334 41.6667V8.33334C45.8334 7.22827 45.3944 6.16846 44.613 5.38706C43.8316 4.60566 42.7718 4.16667 41.6668 4.16667H16.6667C15.5617 4.16667 14.5019 4.60566 13.7205 5.38706C12.9391 6.16846 12.5001 7.22827 12.5001 8.33334V41.6667C12.5001 42.7717 12.0611 43.8316 11.2797 44.613C10.4983 45.3944 9.43848 45.8333 8.33342 45.8333ZM8.33342 45.8333C7.22835 45.8333 6.16854 45.3944 5.38714 44.613C4.60573 43.8316 4.16675 42.7717 4.16675 41.6667V22.9167C4.16675 21.8116 4.60573 20.7518 5.38714 19.9704C6.16854 19.189 7.22835 18.75 8.33342 18.75H12.5001" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M35.4166 12.5H22.9166C21.766 12.5 20.8333 13.4327 20.8333 14.5833V18.75C20.8333 19.9006 21.766 20.8333 22.9166 20.8333H35.4166C36.5672 20.8333 37.4999 19.9006 37.4999 18.75V14.5833C37.4999 13.4327 36.5672 12.5 35.4166 12.5Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Total Complaints</h4>
                                        <p>{{ \App\Helpers\Helper::getComplaintsStatusCount('') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card status-card" data-status="2" style="cursor: pointer;">
                                    <div class="card-body">
                                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M33.3333 29.1667V33.75L36.6666 35.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V14.2333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16.6666 8.33333H12.4999C11.3948 8.33333 10.335 8.77231 9.55364 9.55372C8.77224 10.3351 8.33325 11.3949 8.33325 12.5V41.6667C8.33325 42.7717 8.77224 43.8315 9.55364 44.6129C10.335 45.3943 11.3948 45.8333 12.4999 45.8333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 45.8333C40.2368 45.8333 45.8333 40.2369 45.8333 33.3333C45.8333 26.4298 40.2368 20.8333 33.3333 20.8333C26.4297 20.8333 20.8333 26.4298 20.8333 33.3333C20.8333 40.2369 26.4297 45.8333 33.3333 45.8333Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Under Investigation</h4>
                                        <p>{{ \App\Helpers\Helper::getComplaintsStatusCount('2') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card status-card" data-status="3" style="cursor: pointer;">
                                    <div class="card-body">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V41.6667C41.6666 42.7717 41.2276 43.8315 40.4462 44.6129C39.6648 45.3943 38.605 45.8333 37.4999 45.8333H12.4999C11.3948 45.8333 10.335 45.3943 9.55364 44.6129C8.77224 43.8315 8.33325 42.7717 8.33325 41.6667V12.5C8.33325 11.3949 8.77224 10.3351 9.55364 9.55372C10.335 8.77231 11.3948 8.33333 12.4999 8.33333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.75 29.1667L22.9167 33.3333L31.25 25" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Detected & Property Recovered</h4>
                                        <p>{{ \App\Helpers\Helper::getComplaintsStatusCount('3') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card status-card" data-status="4" style="cursor: pointer;">
                                    <div class="card-body">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V41.6667C41.6666 42.7717 41.2276 43.8315 40.4462 44.6129C39.6648 45.3943 38.605 45.8333 37.4999 45.8333H12.4999C11.3948 45.8333 10.335 45.3943 9.55364 44.6129C8.77224 43.8315 8.33325 42.7717 8.33325 41.6667V12.5C8.33325 11.3949 8.77224 10.3351 9.55364 9.55372C10.335 8.77231 11.3948 8.33333 12.4999 8.33333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.75 29.1667L22.9167 33.3333L31.25 25" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Detected but Property Not Recovered</h4>
                                        <p>{{ \App\Helpers\Helper::getComplaintsStatusCount('4') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card status-card" data-status="5" style="cursor: pointer;">
                                    <div class="card-body">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V41.6667C41.6666 42.7717 41.2276 43.8315 40.4462 44.6129C39.6648 45.3943 38.605 45.8333 37.4999 45.8333H12.4999C11.3948 45.8333 10.335 45.3943 9.55364 44.6129C8.77224 43.8315 8.33325 42.7717 8.33325 41.6667V12.5C8.33325 11.3949 8.77224 10.3351 9.55364 9.55372C10.335 8.77231 11.3948 8.33333 12.4999 8.33333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.75 29.1667L22.9167 33.3333L31.25 25" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Mobile Recovered – Collect from PS</h4>
                                        <p>{{ \App\Helpers\Helper::getComplaintsStatusCount('5') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card status-card" data-status="6" style="cursor: pointer;">
                                    <div class="card-body">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V41.6667C41.6666 42.7717 41.2276 43.8315 40.4462 44.6129C39.6648 45.3943 38.605 45.8333 37.4999 45.8333H12.4999C11.3948 45.8333 10.335 45.3943 9.55364 44.6129C8.77224 43.8315 8.33325 42.7717 8.33325 41.6667V12.5C8.33325 11.3949 8.77224 10.3351 9.55364 9.55372C10.335 8.77231 11.3948 8.33333 12.4999 8.33333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.75 29.1667L22.9167 33.3333L31.25 25" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Not Detected – Closure Report Filed</h4>
                                        <p>{{ \App\Helpers\Helper::getComplaintsStatusCount('6') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="filter-search-bar">
                            @if($user->role_id == 1)
                            <select id="filter_station" class="form-control select2">
                                <option value="">All Stations</option>
                                @foreach(\App\Helpers\Helper::getStationList() as $station)
                                    <option value="{{ $station->station_name }}">{{ $station->station_name }}</option>
                                @endforeach
                            </select>
                            @endif

                            <input type="text" id="filter_fir_number" class="form-control" placeholder="Filter by FIR Number">
                            <select id="filter_status" class="form-control select2">
                                <option value="">Filter by Status</option>
                                <option value="2">Under Investigation</option>
                                <option value="3">Detected & Property Recovered</option>
                                <option value="4">Detected but Property Not Recovered</option>
                                <option value="5">Mobile Recovered – Collect from PS</option>
                                <option value="6">Not Detected – Closure Report Filed</option>
                            </select>
                            <input type="date" id="filter_date_from" class="form-control" placeholder="From Date">
                            <input type="date" id="filter_date_to" class="form-control" placeholder="To Date">

                            <button id="reset_filters" class="btn btn-warning">Reset Filters</button>
                            </div>
                        </div>

                        <table id="dataTable" class="table table-hover modern-table">
                            <thead>
                                <tr>
                                    @if($user->role_id == 1)<th>Police Station Name</th>@endif
                                    <th>FIR Number</th>
                                    <th>FIR Date</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>I/O Officer</th>
                                    <th>I/O Officer Number</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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
                    <h4 class="modal-title">
                        <i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}?
                    </h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_this_confirm') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel">
    <div class="modal-dialog" role="document">
        <form action="{{ route('voyager.complaints.import') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="importModalLabel">Import Complaints via CSV</h4>
                    <a href="{{ url('/download-sample-csv') }}" class="btn btn-info import-btn">
                        <i class="voyager-download"></i> Click Here To Download Sample CSV
                    </a>

                </div>
                <div class="modal-body">
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>


@stop

@section('javascript')
        <script>
        $(document).ready(function () {
        let selectedStatus = '';
         $(document).on('click', '.status-card', function () {
        selectedStatus = $(this).data('status');
        console.log("Selected status:", selectedStatus);
        table.ajax.reload(); // refresh data
    });
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('voyager.complaints.serverData') }}",
            data: function (d) {
                d.station    = $('#filter_station').val();
                d.fir_number = $('#filter_fir_number').val();
                d.status     = selectedStatus || $('#filter_status').val();
                d.date_from  = $('#filter_date_from').val();
                d.date_to    = $('#filter_date_to').val();
            }
        },
        columns: [
            @if(Auth::user()->role_id == 1)
                { data: 'police_station_name', name: 'police_station_name' },
            @endif
            { data: 'fir_number', name: 'fir_number' },
            { data: 'fir_date', name: 'fir_date' },
            { data: 'complainant_name', name: 'complainant_name' },
            { data: 'complainant_number', name: 'complainant_number' },
            { data: 'officer_name', name: 'officer_name' },
            { data: 'police_station_number', name: 'police_station_number' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#filter_fir_number, #filter_status, #filter_date_from, #filter_date_to, #filter_station')
    .on('change keyup', function () {
        table.draw();
    });

    $('#reset_filters').on('click', function () {
        $('#filter_fir_number').val('');
        $('#filter_status').val('');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#filter_station').val('');
        table.draw();
    });

});

$('#dataTable').on('click', '.delete', function (e) {
    e.preventDefault();

    let deleteUrl = '{{ route('voyager.'.$dataType->slug.'.destroy', ['id' => '__id']) }}';
    $('#delete_form').attr('action', deleteUrl.replace('__id', $(this).data('id')));
    $('#delete_modal').modal('show');
});


    </script>
@stop
