<?php

namespace Icybee\Modules\Nodes;

use Icybee\Modules;

$hooks = Hooks::class . '::';

return [

	'events' => [

		Modules\Modules\ActivateOperation::class . '::process' => $hooks . 'on_modules_activate',
		Modules\Modules\DeactivateOperation::class . '::process' => $hooks . 'on_modules_deactivate',
		Modules\Users\DeleteOperation::class . '::process:before' => $hooks . 'before_delete_user',
		Modules\Users\User::class . '::collect_dependencies' => $hooks . 'on_user_collect_dependencies',

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
