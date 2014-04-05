<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes;

class HelpersTest extends \PHPUnit_Framework_TestCase
{
	public function test_slugize()
	{
		$text = "Example title";

		$this->assertEquals(Helpers::slugize($text), slugize($text));

		Helpers::patch('slugize', function() {

			return 'slugize-override';

		});

		$this->assertEquals('slugize-override', slugize($text));

		Helpers::patch('slugize', __NAMESPACE__ . '\Helpers::slugize');
	}
}