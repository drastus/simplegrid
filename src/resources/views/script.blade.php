@if (!isset($data))
	var renderActions = function (data, type, row) {
		var actions = '';
		@if (isset($actions['show']))
		actions += '<a href="{{ route($actions['show']) }}"\
			class="btn btn-info btn-xs" @if (isset($remote) and $remote) data-remote="true" @endif>\
			{{ trans('views.show') }}</a>';
		@endif
		@if (isset($actions['edit']))
		actions += '<a href="{{ route($actions['edit']) }}"\
			class="btn btn-warning btn-xs" @if (isset($remote) and $remote) data-remote="true" @endif>\
			{{ trans('views.edit') }}</a>';
		@endif
		@if (isset($actions['delete']))
		actions += ' {!! delete_url(route($actions['delete'])) !!}';
		@endif
		actions = actions.replace(/%7B\w+%7D/gm, row['DT_RowId']);
		return actions;
	}
@endif

	$(function () {
		$('.table').DataTable({
			@if (!isset($data))
			serverSide: true,
			ajax: {
				@if (isset($source))
				url: @if (is_array($source)) '{{ route($source[0], $source[1]) }}' @else '{{ route($source) }}' @endif,
				@else
				url: '{{ Request::url() }}',
				@endif
				@if ($params)
				data: function (d) {
					@foreach ($params as $name=>$value)
					d['{{ $name }}'] = {!! json_encode($value) !!};
					@endforeach
				} @endif
			},
			@endif
			@if (isset($options))
				@foreach ($options as $key=>$value)
					{!! json_encode($key) !!}:
					@if (!is_array($value))
						{!! json_encode($value) !!},
					@elseif (key($value) === 0)
						[
							@foreach ($value as $el)
								{!! json_encode($el) !!},
							@endforeach
						],
					@else
						{
							@foreach ($value as $k => $v)
								{!! json_encode($k) !!}: {!! json_encode($v) !!},
							@endforeach
						},
					@endif
				@endforeach
			@endif
			@if (isset($actions))
			columnDefs: [
				{
					@if (!isset($data))
					render: renderActions,
					data: null,
					@endif
					width: '15%',
					sortable: false,
					targets: [-1]
				},
			]
			@endif
		});
	});
