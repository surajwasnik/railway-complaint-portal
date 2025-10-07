@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@php
    $user = auth()->user();
@endphp


@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                           @php
                                $dataTypeRows = $dataType->{$edit ? 'editRows' : 'addRows'};
                            @endphp
                            @php
                                $language = ($data->language ?? null) === 'english' ? 'english' : 'marathi';
                            @endphp



                            <input type="hidden" name="language" id="language" value="{{ $language }}">

                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="{{ $language == 'english' ? 'active' : '' }}">
                                    <a href="#english" aria-controls="english" role="tab" data-toggle="tab"
                                        onclick="return handleLanguageChange(event, 'english')">
                                        English
                                    </a>
                                </li>
                                <li role="presentation" class="{{ $language == 'marathi' ? 'active' : '' }}">
                                    <a href="#marathi" aria-controls="marathi" role="tab" data-toggle="tab"
                                        onclick="return handleLanguageChange(event, 'marathi')">
                                        {{ __('मराठी') }}
                                    </a>
                                </li>
                            </ul>


<div class="tab-content" style="margin-top:20px;">
    <div role="tabpanel" class="tab-pane {{ $language == 'english' ? 'active' : '' }}" id="english">

        @if($user->role_id == 2)
            {{-- Station admin: hide dropdown, add hidden input --}}
            @php
                $stationId = \App\Models\Station::where('user_id', $user->id)->value('id');
            @endphp
            <input type="hidden" name="station_id" value="{{ $stationId }}">
        @else
        <div class="form-group col-lg-6 col-md-6">
                    <label>{{ __('Police Station Name') }}</label>
            {{-- Other roles: show dropdown --}}
            <select class="form-control select2" id="station_id" name="station_id" required>
                <option value="">{{ __('Select Police Station') }}</option>
                @foreach(\App\Helpers\Helper::getStationList() as $station)
                    <option value="{{ $station->id }}"
                        {{ (isset($data['station_id']) && $data['station_id'] == $station->id) ? 'selected' : '' }}>
                        {{ $station->station_name }}
                    </option>
                @endforeach
            </select>
            <span class="text text-danger" id="station_id"></span>
                </div>
        @endif




        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('FIR Number')}}</label>
            <input type="number" min="0" class="form-control" id="fir_number" name="fir_number" placeholder="FIR Number"
                   value="{{$data['fir_number'] ?? old('fir_number')}}" autocomplete="off" required>
            <span class="text text-danger" id="fir_number"></span>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('FIR Date')}}</label>

            <input type="date" class="form-control" id="fir_date" name="fir_date" placeholder="FIR Date"
                   value="{{ old('fir_date', isset($data->fir_date) ? \Carbon\Carbon::parse($data->fir_date)->format('Y-m-d') : '') }}" autocomplete="off" required>
            <span class="text text-danger" id="fir_date"></span>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('Name')}}</label>
            <input type="text" class="form-control" id="complainant_name" name="complainant_name" placeholder="Name"
                   value="{{$data['complainant_name'] ?? old('complainant_name')}}" autocomplete="off" required>
            <span class="text text-danger" id="complainant_name"></span>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('Mobile Number')}}</label>
            <input type="number" class="form-control" id="complainant_number" name="complainant_number" placeholder="Number"
                   value="{{$data['complainant_number'] ?? old('complainant_number')}}" autocomplete="off" required>
            <span class="text text-danger" id="complainant_number"></span>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('I/O Officer')}}</label>
            <input type="text" class="form-control" id="officer_name" name="officer_name" placeholder="I/O Officer"
                   value="{{$data['officer_name'] ?? old('officer_name')}}" autocomplete="off" required>
            <span class="text text-danger" id="officer_name"></span>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('I/O Officer Number')}}</label>
            <input type="number" class="form-control" id="police_station_number" name="police_station_number" placeholder="I/O Officer Number"
                   value="{{$data['police_station_number'] ?? old('police_station_number')}}" autocomplete="off" required>
            <span class="text text-danger" id="police_station_number"></span>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('Status')}}</label>
           <select class="form-select select2" name="status" required>
    <option value="2" {{ isset($data->status) && $data->status == 2 ? 'selected' : '' }}>
        Under Investigation
    </option>
    <option value="3" {{ isset($data->status) && $data->status == 3 ? 'selected' : '' }}>
        Detected & Property Recovered
    </option>
    <option value="4" {{ isset($data->status) && $data->status == 4 ? 'selected' : '' }}>
        Detected but Property Not Recovered
    </option>
    <option value="5" {{ isset($data->status) && $data->status == 5 ? 'selected' : '' }}>
        Mobile Recovered – Collect from PS
    </option>
    <option value="6" {{ isset($data->status) && $data->status == 6 ? 'selected' : '' }}>
        Not Detected – Closure Report Filed
    </option>
</select>

        </div>
    </div>

    <!-- Marathi Tab -->
    <div role="tabpanel" class="tab-pane {{ $language == 'marathi' ? 'active' : '' }}" id="marathi">

        @if($user->role_id == 2)
            {{-- Station admin: hide dropdown, add hidden input --}}
            @php
                $stationId = \App\Models\Station::where('user_id', $user->id)->value('id');
            @endphp
            <input type="hidden" name="station_id_mr" value="{{ $stationId }}">
        @else
        <div class="form-group col-lg-6 col-md-6">
                     <label>पोलीस ठाण्याचे नाव</label>
            {{-- Other roles: show dropdown --}}
            <select class="form-control select2" id="station_id_mr" name="station_id_mr" required>
                <option value="">{{ __('Select Police Station') }}</option>
                @foreach(\App\Helpers\Helper::getStationList() as $station)
                    <option value="{{ $station->id }}"
                        {{ (isset($data['station_id']) && $data['station_id'] == $station->id) ? 'selected' : '' }}>
                        {{ $station->station_name }}
                    </option>
                @endforeach
            </select>
            <span class="text text-danger" id="station_id_mr"></span>
                </div>
        @endif

        <div class="form-group col-lg-6 col-md-6">
            <label>गुन्हा क्रमांक</label>
            <input type="number" class="form-control" id="fir_number_mr" name="fir_number_mr"
                   placeholder="गुन्हा क्रमांक"
                   value="{{$data['fir_number'] ?? old('fir_number_mr')}}" autocomplete="off">
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>गुन्ह्याची तारीख</label>
            <input type="date" class="form-control" id="fir_date_mr" name="fir_date_mr"
                   value="{{ old('fir_date', isset($data->fir_date) ? \Carbon\Carbon::parse($data->fir_date)->format('Y-m-d') : '') }}" autocomplete="off">
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>नाव</label>
            <input type="text" class="form-control translate-to-mr" id="complainant_name_mr" name="complainant_name_mr"
                   placeholder="नाव"
                   value="{{$data['complainant_name'] ?? old('complainant_name_mr')}}" autocomplete="off" >
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>मोबाईल नंबर</label>
            <input type="number" min="0" class="form-control" id="complainant_number_mr" name="complainant_number_mr"
                   placeholder=" क्रमांक"
                   value="{{$data['complainant_number'] ?? old('complainant_number_mr')}}" autocomplete="off">
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>तपास अधिकारी</label>
            <input type="text" class="form-control translate-to-mr" id="officer_name_mr" name="officer_name_mr"
                   placeholder="तपास अधिकारी"
                   value="{{$data['officer_name'] ?? old('officer_name_mr')}}" autocomplete="off">
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label>तपास अधिकारी क्रमांक</label>
            <input type="number" class="form-control" id="police_station_number_mr" name="police_station_number_mr"
                   placeholder="तपास अधिकारी क्रमांक"
                   value="{{$data['police_station_number'] ?? old('police_station_number_mr')}}" autocomplete="off">
        </div>
        <div class="form-group col-lg-6 col-md-6">
            <label>{{__('स्थिती')}}</label>
            <select class="form-select select2" name="status_mr" required>
                <option value="1" {{ isset($data->status) && $data->status == 1 ? 'selected' : '' }}>
                    Pending
                </option>
                <option value="2" {{ isset($data->status) && $data->status == 2 ? 'selected' : '' }}>
                    Under Investigation
                </option>
                <option value="3" {{ isset($data->status) && $data->status == 3 ? 'selected' : '' }}>
                    Detected & Property Recovered
                </option>
                <option value="4" {{ isset($data->status) && $data->status == 4 ? 'selected' : '' }}>
                    Detected but Property Not Recovered
                </option>
                <option value="5" {{ isset($data->status) && $data->status == 5 ? 'selected' : '' }}>
                    Mobile Recovered – Collect from PS
                </option>
                <option value="6" {{ isset($data->status) && $data->status == 6 ? 'selected' : '' }}>
                    Not Detected – Closure Report Filed
                </option>
            </select>

                    </div>
                </div>
        </div>
                            <div class="panel-footer">
                                @section('submit-buttons')
                                    <button type="submit" class="btn btn-primary save">Submit</button>
                                @stop
                                @yield('submit-buttons')
                            </div>
                        </div>
                    </form>

                    <div style="display:none">
                        <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
                        <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="languageConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Language Change</h4>
            </div>
            <div class="modal-body">
                Your data may be lost if you switch the language. Do you want to continue?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                    id="cancelLanguageBtn">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmLanguageBtn">Yes, Continue</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript" src="{{ url('js/custom.js?v=1.0') }}"></script>
    <script>
    $(document).ready(function() {
        $('.toggleswitch').bootstrapToggle();
        $('.form-group input[type=date]').each(function(idx, elt) {
            if (elt.hasAttribute('data-datepicker')) {
                elt.type = 'text';
                $(elt).datetimepicker($(elt).data('datepicker'));
            } else if (elt.type != 'date') {
                elt.type = 'text';
                $(elt).datetimepicker({
                    format: 'L',
                    extraFormats: ['YYYY-MM-DD']
                }).datetimepicker($(elt).data('datepicker'));
            }
        });

        @if ($isModelTranslatable)
            $('.side-body').multilingual({
                "editing": true
            });
        @endif

        $('.side-body input[data-slug-origin]').each(function(i, el) {
            $(el).slugify();
        });

        $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
        $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
        $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
        $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

        $('#confirm_delete').on('click', function() {
            $.post('{{ route('voyager.' . $dataType->slug . '.media.remove') }}', params, function(
                response) {
                if (response && response.data && response.data.status == 200) {
                    toastr.success(response.data.message);
                    $file.parent().fadeOut(300, function() {
                        $(this).remove();
                    })
                } else {
                    toastr.error("Error removing file.");
                }
            });
            $('#confirm_delete_modal').modal('hide');
        });

        $('[data-toggle="tooltip"]').tooltip();
    });
     @if ($edit)
            let finalLang = @json($language ?? 'english');
        @else
            let storedLang = localStorage.getItem('selectedLanguage');
            let finalLang = storedLang || 'english';
        @endif
    $(document).ready(function() {

        $('#language').val(finalLang);
        $('.nav-tabs a[href="#' + finalLang + '"]').tab('show');
        $('.nav-tabs li').removeClass('active');
        $('.nav-tabs a[href="#' + finalLang + '"]').parent().addClass('active');

        $('.nav-tabs a').on('click', function() {
            let lang = $(this).attr('href').replace('#', '');

            $('#language').val(lang);
            localStorage.setItem('selectedLanguage', lang);
        });
        $('.tab-pane').not('.active').find('input, select, textarea').prop('required', false);

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $('.tab-pane').find('input, select, textarea').prop('required', false);
            var activeTab = $(e.target).attr("href");
            $(activeTab).find('input, select, textarea').prop('required', true);
        });
    });

    function deleteHandler(tag, isMulti) {
        return function() {
            $file = $(this).siblings(tag);
            params = {
                slug: '{{ $dataType->slug }}',
                filename: $file.data('file-name'),
                id: $file.data('id'),
                field: $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }
            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
        };
    }
let pendingLang = null;
let currentLang = "{{ $language }}";

$('a[data-toggle="tab"]').on('click', function (e) {
    let lang = $(this).attr('aria-controls');

    // ✅ Skip if clicking the already active tab
    if ($(this).parent().hasClass('active')) {
        return;
    }
setLanguage(lang);
    @if (isset($edit) && $edit)
        e.preventDefault();
        pendingLang = lang;
        if (typeof finalLang === 'undefined' || finalLang !== lang) {
            $('#languageConfirmModal').modal('show');
        }
    @else
        currentLang = lang;
    @endif
});


$('#confirmLanguageBtn').on('click', function () {
    if (pendingLang && (typeof finalLang === 'undefined' || finalLang !== pendingLang)) {

        if (currentLang === 'marathi') {
            $('#station_id, #fir_number, #fir_date, #complainant_name, #officer_name, #police_station_number').val('');
            $('[name="status"]').val('');
        } else if (currentLang === 'english') {
            $('#station_id_mr, #fir_number_mr, #fir_date_mr, #complainant_name_mr, #complainant_number_mr, #officer_name_mr, #police_station_number_mr').val('');
            $('[name="status_mr"]').val('');
        }

        //setLanguage(lang);
        $('[href="#' + pendingLang + '"]').tab('show');

        currentLang = pendingLang;
        pendingLang = null;
        $('#languageConfirmModal').modal('hide');
    }
});
    $('#cancelLanguageBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        $('[href="#' + currentLang + '"]').tab('show');

        pendingLang = null;
        $('#languageConfirmModal').modal('hide');
    });


    function setLanguage(lang) {
        $('#language').val(lang);
    }

</script>

@stop
