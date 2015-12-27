<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Facets;

use ICanBoogie\Facets\Criterion\BooleanCriterion;
use ICanBoogie\Facets\Criterion\BasicCriterion;
use ICanBoogie\Facets\Criterion\DateCriterion;

use Icybee\Modules\Nodes\Node;

return [

	'facets' => [

		'nodes' => [

			Node::NID => NidCriterion::class,
			Node::UID => UserCriterion::class,
			Node::UUID => BasicCriterion::class,
			Node::TITLE => BasicCriterion::class,
			Node::SLUG => BasicCriterion::class,
			Node::CONSTRUCTOR => BasicCriterion::class,
			Node::CREATED_AT => DateCriterion::class,
			Node::UPDATED_AT => DateCriterion::class,
			Node::IS_ONLINE => BooleanCriterion::class,
			Node::LANGUAGE => BasicCriterion::class,

		]
	]
];
