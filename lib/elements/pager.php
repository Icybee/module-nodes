<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brickrouge\Element\Nodes;

class Pager extends \Brickrouge\Pager
{
	public function __construct($type, array $attributes=[])
	{
		parent::__construct($type, $attributes + [

			self::BROWSE_NEXT_LABEL => '<i class="icon-arrow-right"></i>',
			self::BROWSE_PREVIOUS_LABEL => '<i class="icon-arrow-left"></i>',

		]);
	}

	protected function getURL($n)
	{
		return '#' . $n;
	}
}
