<?php

namespace Icybee\Modules\Nodes\Facets;

use Icybee\Modules\Nodes\Node;

return [

	'facets' => [

		'nodes' => [

			Node::NID => NidCriterion::class,
			Node::SLUG => SlugCriterion::class,
			Node::TITLE => TitleCriterion::class,
			Node::CONSTRUCTOR => ConstructorCriterion::class,
			Node::IS_ONLINE => IsOnlineCriterion::class

		]
	]
];
