<?php

namespace Icybee\Modules\Nodes;

$hooks = __NAMESPACE__ . '\Hooks::';

return [

	'events' => [

		'Icybee\Modules\Modules\ActivateOperation::process' => $hooks . 'on_modules_activate',
		'Icybee\Modules\Modules\DeactivateOperation::process' => $hooks . 'on_modules_deactivate',
		'Icybee\Modules\Users\DeleteOperation::process:before' => $hooks . 'before_delete_user',
		'Icybee\Modules\Users\User::collect_dependencies' => $hooks . 'on_user_collect_dependencies',

	],

	'patron.markups' => [

		'node' => [

			$hooks . 'markup_node', [

				'select' => [ 'required' => true ],
				'constructor' => null

			]

		],

		'node:navigation' => [

			$hooks . 'markup_node_navigation'

		]

	]

];
