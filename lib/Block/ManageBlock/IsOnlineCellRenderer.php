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

use Icybee\Block\ManageBlock\BooleanCellRenderer;
use Icybee\Modules\Nodes\Node;

/**
 * Renderer for the `is_online` column cell.
 */
class IsOnlineCellRenderer extends BooleanCellRenderer
{
	/**
	 * Adds a title to the decorator checkbox element.
	 *
	 * @param Node $record
	 *
	 * @inheritdoc
	 */
	public function __invoke($record, $property)
	{
		$element = parent::__invoke($record, $property);
		$element['title'] = "Publish or unpublish the record form the website";

		return $element;
	}
}
