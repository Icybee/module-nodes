<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes;

class ViewProvider extends \ICanBoogie\ActiveRecord\Fetcher
{
	/**
	 * Alters the initial query with the following scopes:
	 *
	 * - `own`: @see \Icybee\Modules\Nodes\Model::scope_own
	 * - `similar_site`: @see \Icybee\Modules\Nodes\Model::scope_similar_site
	 * - `similar_language`: @see \Icybee\Modules\Nodes\Model::scope_similar_language
	 */
	protected function create_initial_query()
	{
		return parent::create_initial_query()
		->own
		->similar_site
		->similar_language;
	}

	/**
	 * If the `nid` conditions is defined, only this condition is kept.
	 *
	 * TODO-20140521: We might want to avoid this in the future because if conditions are extracted
	 * form the URL we have no way of knowing that the URL is deprecated.
	 */
	public function alter_conditions(array &$conditions, array $modifiers)
	{
		parent::alter_conditions($conditions, $modifiers);

		if (!empty($conditions['nid']))
		{
			$conditions = [ 'nid' => $conditions['nid'] ];

			return;
		}
	}
}
