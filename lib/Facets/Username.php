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

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\Facets\Criterion;

class UsernameCriterion extends Criterion
{
	public function alter_query(Query $query)
	{
		/*
		$username_query = $query->model->models['users']
			->select('uid, username');

		$query->join($username_query, [ 'as' => 'username' ]);
		*/

//		TODO: Query tries content.uid = username.uid, but `uid` is only available in node

		$query->join('INNER JOIN (SELECT uid, username FROM {prefix}users) AS username ON (node.uid = username.uid)');

		return $query;
	}
}
