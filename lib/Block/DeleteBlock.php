<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Block;

use Icybee\Modules\Nodes\Node;

/**
 * A block used to confirm the deletion of a node.
 *
 * @property Node $record
 */
class DeleteBlock extends \Icybee\Block\DeleteBlock
{
	/**
	 * Returns the title of the node.
	 */
	protected function get_record_name()
	{
		return $this->record->title;
	}
}
