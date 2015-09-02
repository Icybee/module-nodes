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

use ICanBoogie\Facets\BooleanCriterion;
use ICanBoogie\Facets\Criterion;
use ICanBoogie\Facets\DateTimeCriterion;

use Icybee\Modules\Nodes\Node;

return [

	'facets' => [

		'nodes' => [

			Node::NID => NidCriterion::class,
			Node::UID => UserCriterion::class,
			Node::UUID => Criterion::class,
			Node::TITLE => Criterion::class,
			Node::SLUG => Criterion::class,
			Node::CONSTRUCTOR => Criterion::class,
			Node::CREATED_AT => DateTimeCriterion::class,
			Node::UPDATED_AT => DateTimeCriterion::class,
			Node::IS_ONLINE => BooleanCriterion::class,
			Node::LANGUAGE => Criterion::class,

		]
	]
];
