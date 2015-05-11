<?php namespace Drastus\SimpleGrid;

use Illuminate\Support\Facades\Request;

class SimpleGrid {
	/**
	 * @param  Query	 $query
	 * @param  array	 $columns
	 * @param  array     $options
	 * @return array
	 */
	public function data($query, $columns, $options = [])
	{
		// searching
		if (isset($options['search']) && Request::input('search')['value'] !== '') {
			if (!is_array($options['search'])) $options['search'] = [$options['search']];
			$query->where(function ($query) use ($options) {
				foreach ($options['search'] as $search) {
					$query->orWhere($search, 'LIKE', '%'.Request::input('search')['value'].'%');
				}
			});
		}

		// sorting
		foreach (Request::input('order') as $order) {
			if (Request::input('columns')[$order['column']]['orderable'] == 'true') {
				$query = $query->orderBy($columns[(int)$order['column']], $order['dir']);
			}
		}

		$columns_ = [];
		foreach ($columns as $value) {
			$column_ = explode('.', $value);
			$columns_[] = $column_[count($column_)-1];
		}
		$columns = $columns_;

		// counting objects
		$total = $query->count();

		// limiting
		$query = $query
			->take(Request::input('length'))
			->skip(Request::input('start'));

		// getting objects
		$objects = $query->get();

		// column filtering and reformatting for DT
		$data = [];
		foreach ($objects as $object) {
			$object_data = [];
			foreach ($columns as $column) {
				$value = $object->$column;
				// custom column filtering
				if (isset($options['filter'])) {
					$value = $options['filter']($object, $column, $value);
				}
				// standard column filtering
				$filter = config('grid.filter');
				if ($filter) {
					$value = $filter($object, $column, $value);
				}
				$object_data[] = $value;
			}
			$object_data['DT_RowId'] = $object->id;
			$data[] = $object_data;
		}

		return [
			'draw' => (int)(Request::input('draw')),
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
			'data' => $data,
		];
	}

	public function shouldHandle()
	{
		$draw = \Input::get('draw', null);
		return (!is_null($draw) && is_numeric($draw));
	}

	protected function isArrayAssociative($array)
	{
		return (array_values($array) !== $array);
	}

	protected function makeAutoLabels($labels, $model, $columns)
	{
		$class_name = snake_case(str_replace('\\', '', get_class($model)));
		$auto_labels = [];
		foreach ($columns as $column) {
			$trans = trans('models.' . $class_name . '.' . $column);
			if ($trans != 'models.' . $class_name . '.' . $column) {
				$auto_labels[] = $trans;
			}
			elseif (strpos($column, '.') !== false) {
				$column_ = explode('.', $column);
				$column = $column_[count($column_)-1];
				$trans = trans('models.' . $class_name . '.' . $column);
				if ($trans != 'models.' . $class_name . '.' . $column) {
					$auto_labels[] = $trans;
				}
				else {
					$auto_labels[] = ucfirst(str_replace('_', ' ', $column));
				}
			}
			else {
				$auto_labels[] = ucfirst(str_replace('_', ' ', $column));
			}
		}
		$labels = $labels + $auto_labels;
		ksort($labels);

		return $labels;
	}

	protected function prepareLabels($options)
	{
		if (!isset($options['labels']) && isset($options['data'][0])) {
			if ($options['data'][0] instanceof \Illuminate\Database\Eloquent\Model) {
				$model = $options['data'][0];
				$columns = isset($options['columns'])?
						$options['columns']:
						array_keys($options['data'][0]->toArray());
				$options['labels'] = $this->makeAutoLabels([], $model, $columns);
			}
			else
				$options['labels'] = array_keys($options['data'][0]);
		}

		if (!isset($options['labels'])) {
			$options['labels'] = [];
		}

		if (isset($options['query']) && isset($options['columns'])) {
			$model = $options['query']->getModel();
			$options['labels'] = $this->makeAutoLabels($options['labels'], $model, $options['columns']);
		}

		return $options;
	}

	protected function getMultiArrayValue($array, $keys)
	{
		$value = $array;
		foreach ($keys as $key) {
			$value = $value[$key];
		}
		return $value;
	}

	protected function prepareData($options)
	{
		if (isset($options['data'])) {
			if (isset($options['columns'])) {
				foreach ($options['data'] as $key=>$vector) {
					$new_vector = [];
					$array = is_object($vector)? $vector->toArray(): $vector;
					$columns = (isset($options['actions']) && $options['actions'] && isset($options['keyColumns']))?
						array_merge($options['columns'], $options['keyColumns']):
						$options['columns'];
					foreach ($columns as $column) {
						$new_vector[$column] = $this->getMultiArrayValue($array, explode('.', $column));
					}
					$options['data'][$key] = $new_vector;
				}
			}
			else {
				foreach ($options['data'] as $key=>$vector) {
					$options['data'][$key] = $vector->toArray();
					// no relation attrs allowed
				}
				$options['columns'] = array_keys($options['data'][0]);
			}
		}

		return $options;
	}

	/**
	 * @param  array  $options
	 * @return View
	 */
	public function render($options)
	{
		$default_options = ['withScript'=>true, 'options'=>[]];
		$config_options = config('grid.options');
		$options = $options + $default_options;
		$options['options'] = $options['options'] + $config_options;
		$options['keyColumns'] = config('grid.keyColumns');

		$options = $this->prepareLabels($options);
		$options = $this->prepareData($options);

		return \View::make('simplegrid::table', $options)->render();
	}
}
