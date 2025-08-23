<div class="sp-action">
    @can('read', app($dataType->model_name))
    <a href="{{ route('voyager.'.$dataType->slug.'.show', $data['id']) }}" title="View" class="view">
       read
    </a>
    @endcan
    @can('edit', app($dataType->model_name))
    <a href="{{ route('voyager.'.$dataType->slug.'.edit', $data['id']) }}" title="Edit" class="edit">
        edit
    </a>
    @endcan
    @can('delete', app($dataType->model_name))
    <a href="javascript:;" title="Delete" class="delete" data-id="{{ $data['id'] }}" id="delete-{{ $data['id'] }}">
        Delete
    </a>
    @endcan
</div>
