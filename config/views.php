<?php

namespace Icybee\Modules\Nodes;

use Icybee\Modules\Views\ViewOptions as Options;

return [

	'nodes' => [

		'view' => [

			Options::TITLE => "Record detail",
			Options::PROVIDER_CLASSNAME => Options::PROVIDER_CLASSNAME_AUTO,
			Options::RENDERS => Options::RENDERS_ONE

		],

		'list' => [

			Options::TITLE => "Records list",
			Options::PROVIDER_CLASSNAME => Options::PROVIDER_CLASSNAME_AUTO,
			Options::RENDERS => Options::RENDERS_MANY,
			Options::DEFAULT_CONDITIONS => [

				'order' => '-created_at'

			]
		],

		'home' => [

			Options::TITLE => "Records home",
			Options::PROVIDER_CLASSNAME => Options::PROVIDER_CLASSNAME_AUTO,
			Options::RENDERS => Options::RENDERS_MANY,
			Options::DEFAULT_CONDITIONS => [

				'order' => '-created_at'

			]
		]

	]

];
