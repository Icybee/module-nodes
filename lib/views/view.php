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

use ICanBoogie\AuthenticationRequired;

use Icybee\Modules\Views\ViewOptions;

class View extends \Icybee\Modules\Views\View
{
	protected function get_important_conditions()
	{
		$conditions = parent::get_important_conditions();

		if ($this->renders == ViewOptions::RENDERS_MANY)
		{
			$conditions['is_online'] = true;
		}

		return $conditions;
	}

	/**
	 * @throws AuthenticationRequired
	 *
	 * @inheritdoc
	 */
	protected function provide($provider, array $conditions)
	{
		$app = $this->app;

		$rc = parent::provide($provider, $conditions);

		if ($rc instanceof Node) // FIXME-20140521: always array now
		{
			if (!$rc->is_online)
			{
				if (!$app->user->has_permission(\ICanBoogie\Module::PERMISSION_ACCESS, $rc->constructor))
				{
					throw new AuthenticationRequired;
				}

				$rc->title .= ' ✎';
			}

			$context = $app->request->context;

			$page = isset($context->page) ? $context->page : null;

			if ($page)
			{
				$page->title = $rc->title;
				$page->node = $rc; // COMPAT-20150324
				$context->node = $rc;
			}
		}

		return $rc;
	}
}
