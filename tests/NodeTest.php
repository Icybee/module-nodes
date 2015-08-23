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

use Icybee\Modules\Sites\Site;
use ICanBoogie\DateTime;

class NodeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provide_test_fallback_properties
	 */
	public function test_fallback_properties($property, $fixture, $expected)
	{
		$node = Node::from($fixture);
		$this->assertSame($expected, $node->$property);

		if (array_key_exists($property, $fixture))
		{
			$this->assertArrayHasKey($property, $node->to_array());
			$this->assertArrayHasKey($property, $node->__sleep());
		}
		else
		{
			$this->assertArrayNotHasKey($property, $node->to_array());
			$this->assertArrayNotHasKey($property, $node->__sleep());
		}
	}

	public function provide_test_fallback_properties()
	{
		return [

			[ 'constructor', [], 'nodes' ],
			[ 'constructor', [ 'constructor' => 'images' ], 'images' ],

			[ 'language', [ 'site' => null ], null ],
			[ 'language', [ 'site' => Site::from([ 'language' => 'fr' ]) ], 'fr' ],
			[ 'language', [ 'site' => Site::from([ 'language' => 'fr' ]), 'language' => 'en' ], 'en' ],

			[ 'slug', [], '' ],
			[ 'slug', [ 'title' => 'The quick brown fox' ], 'the-quick-brown-fox' ],
			[ 'slug', [ 'title' => 'The quick brown fox', 'slug' => 'quick-fox' ], 'quick-fox' ],

		];
	}

	/**
	 * @dataProvider provide_test_get_property
	 */
	public function test_get_property($property, $fixture, $expected)
	{
		$this->assertSame($expected, Node::from($fixture)->$property);
	}

	public function provide_test_get_property()
	{
		$app = \ICanBoogie\app();

		return [

			[ 'css_class', [], 'node constructor-nodes' ],
			[ 'css_class', [ 'nid' => 13 ], 'node node-13 constructor-nodes' ],
			[ 'css_class', [ 'nid' => 13, 'slug' => 'quick-brown-fox' ], 'node node-13 node-slug-quick-brown-fox constructor-nodes' ],
			[ 'css_class', [ 'nid' => 13, 'slug' => 'quick-brown-fox', 'constructor' => 'news' ], 'node node-13 node-slug-quick-brown-fox constructor-news' ],

			[ 'site', [], null ],
			[ 'site', [ 'siteid' => 1 ], $app->models['sites'][1] ],

			[ 'user', [], null ],
			[ 'user', [ 'uid' => 1 ], $app->models['users'][1] ]

		];
	}

	public function test_set_site()
	{
		$node = new Node;
		$node->site = \ICanBoogie\app()->models['sites'][1];
		$this->assertInstanceOf('Icybee\Modules\Sites\Site', $node->site);
		$this->assertEquals(1, $node->siteid);
	}

	public function test_set_user()
	{
		$node = new Node;
		$node->user = \ICanBoogie\app()->models['users'][1];
		$this->assertInstanceOf('Icybee\Modules\Users\User', $node->user);
		$this->assertEquals(1, $node->uid);
	}

	public function test_created_at()
	{
		$node = new Node;
		$this->assertInstanceOf('ICanBoogie\DateTime', $node->created_at);
		$this->assertTrue($node->created_at->is_empty);

		$node->created_at = 'now';
		$this->assertInstanceOf('ICanBoogie\DateTime', $node->created_at);

		$this->assertArrayHasKey('created_at', $node->__sleep());
		$this->assertArrayHasKey('created_at', $node->to_array());
	}

	public function test_create_at_MUST_be_modified_if_empty_during_save()
	{
		$node = new Node;
		$node->title = "Example";
		$node->save();

		$now = DateTime::now()->utc;
		$this->assertFalse($node->created_at->is_empty);
		$this->assertEquals($now, $node->created_at);

		$node->delete();
	}

	public function test_created_at_MUST_NOT_be_modified_if_not_empty_during_save()
	{
		$node = new Node;
		$node->title = "Example";

		$created_at = new DateTime('-2 week');
		$node->created_at = $created_at;
		$node->save();
		$this->assertEquals($created_at->utc, $node->created_at->utc);

		$node->delete();
	}

	public function test_updated_at()
	{
		$node = new Node;
		$this->assertInstanceOf('ICanBoogie\DateTime', $node->updated_at);
		$this->assertTrue($node->updated_at->is_empty);

		$node->updated_at = 'now';
		$this->assertInstanceOf('ICanBoogie\DateTime', $node->updated_at);

		$this->assertArrayHasKey('updated_at', $node->__sleep());
		$this->assertArrayHasKey('updated_at', $node->to_array());
	}

	public function test_create_at_MUST_be_modified_during_save()
	{
		$node = new Node;
		$node->title = "Example";
		$this->assertTrue($node->updated_at->is_empty);
		$node->save();

		$now = DateTime::now()->utc;
		$this->assertFalse($node->created_at->is_empty);
		$this->assertEquals($now, $node->created_at);

		$node->updated_at = '-2 week';
		$node->save();
		$this->assertEquals($now, $node->created_at);

		$node->delete();
	}

	/**
	 * @expectedException \ICanBoogie\ActiveRecord\StatementNotValid
	 */
	public function test_save_empty()
	{
		$node = new Node;
		$node->save();
	}

	public function test_save_with_title()
	{
		$node = new Node;
		$node->title = 'example';
		$node->save();
	}

	public function test_uuid()
	{
		$node = new Node;
		$node->title = "example";
		$this->assertNull($node->uuid);
		$node->save();
		$this->assertNotNull($node->uuid);
	}
}
