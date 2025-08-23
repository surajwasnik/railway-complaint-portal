@extends('voyager::master')

@section('content')

@section('page_header')
    <div class="container-fluid sp-head sp-user-head sp-user-browse-head">
        <div class="sp-row sp-top-outer">
            <div class="sp-col6">
                <h2 class="page-title">{{ $dataType->getTranslatedAttribute('display_name_plural') }}</h2>
            </div>
            <div class="sp-col6 sp-act icon-btns customer_btn_add">
                @can('export', app($dataType->model_name))
                    <form action="{{ route('users.export') }}" method="POST">
                        {{ method_field('POST') }}
                        {{ csrf_field() }}
                        <input type="hidden" name="user_id" id="export_ids" value="">
                        <button type="submit" class="btn-pr" id="export"><img src="{{url('assets/images/export.svg')}}">{{ __('Export') }}</button>
                    </form>
                @endcan
                @can('add', app($dataType->model_name))
                    <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn-pr">
                        <img src="{{url('assets/images/plus.svg')}}">{{ __('voyager::generic.add_new') }}
                    </a>
                @endcan
            </div>
        </div><!-- ./sp-row -->
        @include('voyager::multilingual.language-selector')
    </div><!-- ./container-fluid -->
@stop
<div class="container">
    <h2>User Listing</h2>
@include('voyager::alerts')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $data)
            <tr>
                <td>{{ $data->id }}</td>
                <td>{{ $data->name }}</td>
                <td>{{ $data->email }}</td>
                <td>{{ $data->role ? $data->role->name : 'N/A' }}</td>
                <td>{{ $data->created_at->format('d-m-Y') }}</td>
                <td>
                    @include('voyager::bread.partials.browse-actions')
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>
{{-- Single delete modal --}}
    <div class="sp-modal delete_modal modal fade" id="delete_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal--header">
                    <span class="btn--close" data-bs-dismiss="modal"></span>
                </div>
                <div class="modal-body">
                    <h2>{{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h2>
                    <div class="opt-btn">
                        <button type="button" class="btn-se" data-bs-dismiss="modal">{{ __('No') }}</button>
                        <form action="#" id="delete_form" method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <input type="submit" class="btn-pr" value="{{ __('Yes') }}">
                        </form>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{ $users->links() }}
</div>
