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

use ICanBoogie\ActiveRecord\RecordNotFound;
use ICanBoogie\Event;
use ICanBoogie\I18n;
use ICanBoogie\Module\Descriptor;
use ICanBoogie\Operation;

use Brickrouge\A;

class Hooks
{
	/*
	 * Events
	 */

	/**
	 * Checks if the user to be deleted has nodes.
	 *
	 * @param Operation\BeforeProcessEvent $event
	 * @param \Icybee\Modules\Users\DeleteOperation $operation
	 */
	static public function before_delete_user(Operation\BeforeProcessEvent $event, \Icybee\Modules\Users\DeleteOperation $operation)
	{
		$uid = $operation->key;
		$count = \ICanBoogie\app()->models['nodes']->filter_by_uid($uid)->count;

		if (!$count)
		{
			return;
		}

		$event->errors['uid'] = $event->errors->format('The user %name is used by :count nodes.', [

			'name' => $operation->record->name, ':count' => $count

		]);
	}

	/**
	 * Adds the orders attached to a member to the dependency collection.
	 *
	 * @param \ICanBoogie\ActiveRecord\CollectDependenciesEvent $event
	 * @param \Icybee\Modules\Users\User $target
	 */
	static public function on_user_collect_dependencies(\ICanBoogie\ActiveRecord\CollectDependenciesEvent $event, \Icybee\Modules\Users\User $target)
	{
		$nodes = \ICanBoogie\app()
		->models['nodes']
		->select('nid, constructor, title')
		->filter_by_uid($target->uid)
		->order('created_at DESC')
		->all(\PDO::FETCH_OBJ);

		/* @var $nodes Node */

		foreach ($nodes as $node)
		{
			$event->add($node->constructor, $node->nid, $node->title, true);
		}
	}

	/*
	 * Markups
	 */

	/**
	 * Retrieves a node.
	 *
	 * <pre>
	 * <p:node
	 *     select = expression
	 *     constructor = string>
	 *     <!-- Content: with-param*, template -->
	 * </p:node>
	 * </pre>
	 *
	 * @param array $args
	 * @param \Patron\Engine $patron
	 * @param mixed $template
	 *
	 * @throws RecordNotFound when the record cannot be found.
	 */
	static public function markup_node(array $args, \Patron\Engine $patron, $template)
	{
		$record = null;
		$constructor = $args['constructor'] ?: 'nodes';
		$select = $args['select'];

		if ($select{0} == ':')
		{
			$select = substr($select, 1);
		}

		$app = \ICanBoogie\app();

		if (is_numeric($select))
		{
			$record = $app->models[$constructor][$select];
		}
		else
		{
			$record = $app->models[$constructor]->filter_by_slug($select)->ordered->own->visible->one;
		}

		if (!$record)
		{
			throw new RecordNotFound('Unable to find record with the provided arguments: ' . json_encode($args), array());
		}

		return $patron($template, $record);
	}

	static public function markup_node_navigation(array $args, \Patron\Engine $patron, $template)
	{
		$app = self::app();
		$app->document->css->add(DIR . 'public/page.css');

		/* @var $record Node */

		$record = $patron->context['this'];

		$list = null;
		$cycle = null;

		$list_url = $record->url('list');

		if ($list_url)
		{
			$list = '<div class="list">' . new A("All records", $list_url) . '</div>';
		}

		$next = null;
		$previous = null;
		$next_record = $record->next;
		$previous_record = $record->previous;

		if ($next_record)
		{
			$title = $next_record->title;

			$next = new A
			(
				\ICanBoogie\shorten($title, 48, 1), $next_record->url, [

					'class' => "next",
					'title' => $app->translate('Next: :title', [ ':title' => $title ])

				]
			);
		}

		if ($previous_record)
		{
			$title = $previous_record->title;

			$previous = new A
			(
				\ICanBoogie\shorten($title, 48, 1), $previous_record->url, [

					'class' => "previous",
					'title' => $app->translate('Previous: :title', [ ':title' => $title ])

				]
			);
		}

		if ($next || $previous)
		{
			$cycle = '<div class="cycle">' . $next . ' ' . $previous . '</div>';
		}

		if ($list || $cycle)
		{
			return '<div class="node-navigation">' . $list . $cycle . '</div>';
		}
	}

	/*
	 * Dashboard
	 */

	static public function dashboard_now()
	{
		$app = self::app();
		$app->document->css->add(DIR . 'public/dashboard.css');

		$counts = $app->models['nodes']->similar_site->count('constructor');

		if (!$counts)
		{
			return '<p class="nothing">' . $app->translate('No record yet') . '</p>';
		}

		$categories = [

			'contents' => [],
			'resources' => [],
			'other' => []

		];

		$default_category = 'other';

		foreach ($counts as $constructor => $count)
		{
			if (!isset($app->modules[$constructor]))
			{
				continue;
			}

			$descriptor = $app->modules->descriptors[$constructor];
			$category = $descriptor[Descriptor::CATEGORY];

			if (!isset($categories[$category]))
			{
				$category = $default_category;
			}

			$title = $app->translate($descriptor[Descriptor::TITLE], [], [ 'scope' => 'module_title' ]);
			$title = $app->translate(strtr($constructor, '.', '_') . '.name.other', [], [ 'default' => $title ]);

			$categories[$category][] = [ $title, $constructor, $count ];
		}

		$head = '';
		$max_by_category = 0;

		foreach ($categories as $category => $entries)
		{
			$max_by_category = max($max_by_category, count($entries));
			$head .= '<th>&nbsp;</th><th>' . $app->translate($category, [], [ 'scope' => 'module_category' ]) . '</th>';
		}

		$body = '';
		$path = $app->site->path;

		for ($i = 0 ; $i < $max_by_category ; $i++)
		{
			$body .= '<tr>';

			foreach ($categories as $category => $entries)
			{
				if (empty($entries[$i]))
				{
					$body .= '<td colspan="2">&nbsp;</td>';

					continue;
				}

				list($title, $constructor, $count) = $entries[$i];

				$body .= <<<EOT
<td class="count">$count</td>
<td class="constructor"><a href="$path/admin/$constructor">$title</a></td>
EOT;
			}

			$body .= '</tr>';
		}

		return <<<EOT
<table>
	<thead><tr>$head</tr></thead>
	<tbody>$body</tbody>
</table>
EOT;
	}

	static public function dashboard_user_modified()
	{
		$app = self::app();
		$app->document->css->add(DIR . 'public/dashboard.css');

		$model = $app->models['nodes'];

		$entries = $model
		->where('uid = ? AND (siteid = 0 OR siteid = ?)', [ $app->user_id, $app->site_id ])
		->order('updated_at desc')
		->limit(10)
		->all;

		if (!$entries)
		{
			return '<p class="nothing">' . $app->translate('No record yet') . '</p>';
		}

		$last_date = null;
		$context = $app->site->path;

		$rc = '<table>';

		foreach ($entries as $record)
		{
			$date = \ICanBoogie\I18n\date_period($record->updated_at);

			if ($date === $last_date)
			{
				$date = '&mdash;';
			}
			else
			{
				$last_date = $date;
			}

			$title = \ICanBoogie\shorten($record->title, 48);
			$title = \ICanBoogie\escape($title);

			$rc .= <<<EOT
	<tr>
	<td class="date light">$date</td>
	<td class="title"><a href="$context/admin/{$record->constructor}/{$record->nid}/edit">{$title}</a></td>
	</tr>
EOT;
		}

		$rc .= '</table>';

		return $rc;
	}

	/*
	 * Support
	 */

	/**
	 * @return \ICanBoogie\Core|\Icybee\Binding\CoreBindings
	 */
	static private function app()
	{
		return \ICanBoogie\app();
	}
}
