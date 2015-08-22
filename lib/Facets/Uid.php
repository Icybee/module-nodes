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

use ICanBoogie\Facets\Criterion;

class UserCriterion extends Criterion
{
	public function __construct($id, array $options = [])
	{
		parent::__construct($id, $options + [

			'column_name' => 'uid'

		]);
	}
}
