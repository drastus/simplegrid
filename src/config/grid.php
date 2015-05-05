<?php

return [
	// standard options
	'options' => [
	],
	// standard column filters
	'filter' => function ($column, $value, $object) {
		if ($value instanceof \DateTime) {
			return $value->format('Y-m-d H:i');
		}
		elseif ($value === true) {
			return '✓';
		}
		elseif ($value === false) {
			return '✗';
		}
		return $value;
	}
];
