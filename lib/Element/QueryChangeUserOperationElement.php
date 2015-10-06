<?php

namespace Icybee\Modules\Nodes\Element;

use Brickrouge\Element;

use Icybee\Modules\Users\Element\PickUser;
use Icybee\Element\QueryOperationElement;

class QueryChangeUserOperationElement extends QueryOperationElement
{
	protected function create_confirm_children(array $options, array $attributes)
	{
		return array_merge(parent::create_confirm_children($options, $attributes), [

			'params' => new Element('div', [

				Element::CHILDREN => [

					'uid' =>  new PickUser([

						Element::LABEL => 'New user',
						Element::LABEL_POSITION => Element::LABEL_POSITION_ABOVE,
						Element::REQUIRED => true

					])

				]

			])

		]);
	}
}
