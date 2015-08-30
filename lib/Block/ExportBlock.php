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

use ICanBoogie\Operation;

use Brickrouge\Button;
use Brickrouge\Form;

use Icybee\Modules\Nodes\Module;

class ExportBlock extends Form
{
	public function __construct(Module $module, array $attributes = [])
	{
		parent::__construct($attributes + [

			Form::HIDDENS => [

				Operation::DESTINATION => $module->id,
				Operation::NAME => 'export'

			],

			Form::ACTIONS => new Button('Export', [ 'class' => 'btn-primary', 'type' => 'submit' ]),

			'class' => 'form-primary'

		]);
	}
}
