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
		return array
		(
			array('constructor', array(), 'nodes'),
			array('constructor', array('constructor' => 'images'), 'images'),

			array('language', array('site' => null), null),
			array('language', array('site' => Site::from(array('language' => 'fr'))), 'fr'),
			array('language', array('site' => Site::from(array('language' => 'fr')), 'language' => 'en'), 'en'),

			array('slug', array(), ''),
			array('slug', array('title' => 'The quick brown fox'), 'the-quick-brown-fox'),
			array('slug', array('title' => 'The quick brown fox', 'slug' => 'quick-fox'), 'quick-fox'),
		);
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
		global $core;

		return array
		(
			array('css_class', array(), 'node constructor-nodes'),
			array('css_class', array('nid' => 13), 'node node-13 constructor-nodes'),
			array('css_class', array('nid' => 13, 'slug' => 'quick-brown-fox'), 'node node-13 node-slug-quick-brown-fox constructor-nodes'),
			array('css_class', array('nid' => 13, 'slug' => 'quick-brown-fox', 'constructor' => 'news'), 'node node-13 node-slug-quick-brown-fox constructor-news'),

			array('site', array(), null),
			array('site', array('siteid' => 1), $core->models['sites'][1]),

			array('user', array(), null),
			array('user', array('uid' => 1), $core->models['users'][1])
		);
	}

	public function test_set_site()
	{
		global $core;

		$node = new Node;
		$node->site = $core->models['sites'][1];
		$this->assertInstanceOf('Icybee\Modules\Sites\Site', $node->site);
		$this->assertEquals(1, $node->siteid);
	}

	public function test_set_user()
	{
		global $core;

		$node = new Node;
		$node->user = $core->models['users'][1];
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
}