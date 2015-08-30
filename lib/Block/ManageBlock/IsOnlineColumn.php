<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Block\ManageBlock;

use Icybee\Block\ManageBlock\BooleanColumn;
use Icybee\Modules\Nodes\Block\ManageBlock;

/**
 * Representation of the `is_online` column.
 */
class IsOnlineColumn extends BooleanColumn
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'filters' => [

				'options' => [

					'=1' => 'Online',
					'=0' => 'Offline'

				]

			],

			'cell_renderer' => IsOnlineCellRenderer::class

		]);
	}
}
