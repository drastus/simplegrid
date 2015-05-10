<table class="table table-bordered table-striped"
@if (isset($id)) id="{{ $id }}" @endif>
	<thead>
		<tr>
			@foreach ($labels as $label)
			<th>{{ $label }}</th>
			@endforeach
			@if (isset($actions)) <th>{{ trans('views.actions') }}</th> @endif
		</tr>
	</thead>
	<tbody>
		@if (isset($data))
			@foreach ($data as $vector)
			<tr>
				@foreach ($vector as $column=>$datum)
					@if (in_array($column, $columns))<td>{{ $datum }}</td>@endif
				@endforeach

				@if (isset($actions))
				<td style="width: 15%">
					@if (isset($actions['show']))
					<a href="{{ route($actions['show'], $vector['id']) }}"
						class="btn btn-info btn-xs" @if (isset($remote) and $remote) data-remote="true" @endif>
						{{ trans('views.show') }}</a>
					@endif
					@if (isset($actions['edit']))
					<a href="{{ route($actions['edit'], $vector['id']) }}"
						class="btn btn-warning btn-xs" @if (isset($remote) and $remote) data-remote="true" @endif>
						{{ trans('views.edit') }}</a>
					@endif
					@if (isset($actions['delete']))
					{!! delete_url(route($actions['delete'], $vector['id'])) !!}
					@endif
				</td>
				@endif
			</tr>
			@endforeach
		@endif
	</tbody>
</table>

@if ($withScript)
<script>
	@include('simplegrid::script')
</script>
@endif
