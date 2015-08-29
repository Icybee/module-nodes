<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Operation;

use Icybee\Modules\Nodes\Element\QueryChangeUserOperationElement;
use Icybee\Operation\Module\QueryOperation;

class QueryOperationOperation extends QueryOperation
{
	protected function query_online()
	{
		return [

			'params' => [

				'keys' => $this->request['keys']

			]

		];
	}

	protected function query_offline()
	{
		return [

			'params' => [

				'keys' => $this->request['keys']

			]

		];
	}

	protected function query_change_user()
	{
		return [

			'params' => [

				'keys' => $this->request['keys']

			],

			'element_class' => QueryChangeUserOperationElement::class

		];
	}
}
