@extends('voyager::master')

@section('page_title', 'Railway Complaint Portal | '.$dataType->getTranslatedAttribute('display_name_plural'))

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
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import_modal">
                    <i class="voyager-upload"></i> Import CSV
                </button>
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
                        <div class="filter-search-bar">
                            <input type="text" id="filter_fir_number" class="form-control" placeholder="Filter by FIR Number">
                            <select id="filter_status" class="form-control">
                                <option value="">Filter by Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Under Investigation">Under Investigation</option>
                                <option value="Detected & Property Recovered">Detected & Property Recovered</option>
                                <option value="Detected but Property Not Recovered">Detected but Property Not Recovered</option>
                                <option value="Mobile Recovered – Collect from PS">Mobile Recovered – Collect from PS</option>
                                <option value="Not Detected – Closure Report Filed">Not Detected – Closure Report Filed</option>
                            </select>
                            <input type="date" id="filter_date" class="form-control">
                            <button id="reset_filters" class="btn btn-warning">Reset Filters</button>
                            </div>
                        </div>

                        <div class="table-container">
                            <table id="dataTable" class="table table-hover modern-table">
                                <thead>
                                <tr>
                                    @foreach($dataType->browseRows as $row)
                                    <th>{{ $row->display_name }}</th>
                                    @endforeach
                                    <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                    <tr>
                                        @foreach($dataType->browseRows as $row)
                                        <td>
                                            @if($row->type == 'image')
                                                <img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                            @else
                                            @if($row->field == 'status')
                                                @php
                                                    $statusMap = [
                                                        1 => 'Pending',
                                                        2 => 'Under Investigation',
                                                        3 => 'Detected & Property Recovered',
                                                        4 => 'Detected but Property Not Recovered',
                                                        5 => 'Mobile Recovered – Collect from PS',
                                                        6 => 'Not Detected – Closure Report Filed',
                                                    ];
                                                    
                                                    $statusClassMap = [
                                                        1 => 'status-pending',
                                                        2 => 'status-under-investigation',
                                                        3 => 'status-detected-recovered',
                                                        4 => 'status-detected-not-recovered',
                                                        5 => 'status-mobile-recovered',
                                                        6 => 'status-not-detected-closed',
                                                    ];
                                                    
                                                    $statusText = $statusMap[$data->{$row->field}] ?? $data->{$row->field};
                                                    $statusClass = $statusClassMap[$data->{$row->field}] ?? 'status-pending';
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    <i class="fa-regular fa-square-check"></i> {{ $statusText }}
                                                </span>
                                            @elseif($row->type == 'relationship')
                                                @include('voyager::formfields.relationship', ['view' => 'browse','options' => $row->details])
                                            @else
                                                {{ $data->{$row->field} }}
                                            
                                            @endif
                                        @endif

                                        </td>
                                        @endforeach
                                        <td class="no-sort no-click bread-actions">
                                            @can('delete', $data)
                                                <div class="btn btn-sm btn-danger pull-right delete" data-id="{{ $data->{$data->getKeyName()} }}">
                                                    <!-- <i class="voyager-trash"></i>  -->
                                                    <i class="fa-regular fa-trash-can"></i> {{ __('voyager::generic.delete') }}
                                                </div>
                                            @endcan
                                            @can('edit', $data)
                                                <a href="{{ route('voyager.'.$dataType->slug.'.edit', $data->{$data->getKeyName()}) }}" class="btn btn-sm btn-primary pull-right edit">
                                                    <!-- <i class="voyager-edit"></i>  -->
                                                    <i class="fa-regular fa-pen-to-square"></i> {{ __('voyager::generic.edit') }}
                                                </a>
                                            @endcan
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
                    <a href="{{ asset('sample_csvs/complaints_sample.csv') }}" class="btn btn-info import-btn" download>
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
    var table = $('#dataTable').DataTable({
        "order": [],
        "language": {!! json_encode(__('voyager::datatable'), true) !!},
        "columnDefs": [{"targets": -1, "searchable":  false, "orderable": false}]
        @if(config('dashboard.data_tables.responsive')), responsive: true @endif
    });

    $('#filter_fir_number').on('keyup', function () {
        console.log(table);
        table.column(1).search(this.value).draw();
    });

    $('#filter_status').on('change', function () {
        var val = this.value;
        if (val) {
            table.column(6).search('^' + val + '$', true, false).draw();
        } else {
            table.column(6).search('').draw();
        }
    });


    $('#filter_date').on('change', function () {
        console.log(table.column(6));
        table.column(8).search(this.value).draw();
    });


        $('#reset_filters').on('click', function () {
            $('#filter_fir_number').val('');
            $('#filter_status').val('');
            $('#filter_date').val('');
            table.search('').columns().search('').draw();
        });
    });


        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', ['id' => '__menu']) }}'.replace('__menu', $(this).data('id'));

            $('#delete_modal').modal('show');
        });
    </script>
@stop