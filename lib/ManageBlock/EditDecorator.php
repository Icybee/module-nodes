<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\ManageBlock;

use Brickrouge\Element;

class EditDecorator extends \Icybee\ManageBlock\EditDecorator
{
	/**
	 * The component is shortened if it's longer than 52 characters, in which case the title of
	 * the element is modified to include the original component.
	 */
	public function render()
	{
		$element = parent::render();
		$component = $this->component;
		$shortened = null;
		$html = $component
			? \ICanBoogie\escape(\ICanBoogie\shorten($component, 52, .75, $shortened))
			: $this->app->translate('<em>no title</em>');

		if (!$shortened)
		{
			return $element;
		}

		$element[Element::INNER_HTML] = str_replace('…', '<span class="light">…</span>', $html);
		$element['title'] = $this->app->translate('manage.edit_named', [ ':title' => $component ? $component : 'unnamed' ]);

		return $element;
	}
}
