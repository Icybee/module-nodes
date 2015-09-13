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

use Brickrouge\A;

use Icybee\Block\ManageBlock;
use Icybee\Block\ManageBlock\Column;

use Icybee\Modules\Nodes\Node;

/**
 * Representation of the `url` column.
 */
class URLColumn extends Column
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, [

			'title' => null,
			'class' => 'cell-fitted',
			'orderable' => false

		]);
	}

	/**
	 * @param Node $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$url = $record->url;

		if (!$url || $url{0} == '#')
		{
			return null;
		}

		return new A('', $url, [

			'title' => $this->t('View this entry on the website'),
			'class' => 'icon-external-link',
			'target' => '_blank'

		]);
	}
}
