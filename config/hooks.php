<?php

namespace Icybee\Modules\Nodes;

use Icybee\Modules;

$hooks = Hooks::class . '::';

return [

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
