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

use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Nodes\Block\ManageBlock;
use Icybee\Modules\Nodes\Node;

/**
 * Representation of the `title` column.
 */
class TitleColumn extends Column
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, [

			'discreet' => false

		]);
	}

	/**
	 * @param Node $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return new EditDecorator($record->title, $record);
	}
}

