<?php

namespace Icybee\Modules\Nodes;

$hooks = Hooks::class . '::';

return [

	'system-nodes-now' => [

		'title' => "From a glance",
		'callback' => $hooks . 'dashboard_now',
		'column' => 0

	],

	'system-nodes-user-modified' => [

		'title' => "Your last modifications",
		'callback' => $hooks . 'dashboard_user_modified',
		'column' => 0
	]

];
