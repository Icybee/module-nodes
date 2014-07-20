<?php

namespace Icybee\Modules\Nodes;

return [

	'facets' => [

		'nodes' => [

			Node::NID => __NAMESPACE__ . '\NidCriterion',
			Node::SLUG => __NAMESPACE__ . '\SlugCriterion',
			Node::TITLE => __NAMESPACE__ . '\TitleCriterion',
			Node::CONSTRUCTOR => __NAMESPACE__ . '\ConstructorCriterion',
			Node::IS_ONLINE => __NAMESPACE__ . '\IsOnlineCriterion'

		]

	]

];