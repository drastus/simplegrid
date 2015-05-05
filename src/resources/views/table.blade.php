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
				@foreach ($vector as $datum)
					<td>{{ $datum }}</td>
				@endforeach

				@if (isset($actions))
				<td style="width: 15%">
					@if (isset($actions['show']))
					<a href="{{ route($actions['show'], $vector) }}"
						class="btn btn-info btn-xs" @if (isset($remote) and $remote) data-remote="true" @endif>
						{{ trans('views.show') }}</a>
					@endif
					@if (isset($actions['edit']))
					<a href="{{ route($actions['edit'], $vector) }}"
						class="btn btn-warning btn-xs" @if (isset($remote) and $remote) data-remote="true" @endif>
						{{ trans('views.edit') }}</a>
					@endif
					@if (isset($actions['delete']))
					{!! delete_url(route($actions['delete'], $vector)) !!}
					@endif
				</td>
				@endif
			</tr>
			@endforeach
		@endif
	</tbody>
</table>

@if ($with_script)
<script>
	@include('simplegrid::script')
</script>
@endif
