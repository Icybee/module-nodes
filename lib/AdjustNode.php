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

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\I18n;

use Brickrouge\A;
use Brickrouge\Element;
use Brickrouge\Pager;
use Brickrouge\Text;
use Icybee\Modules\Nodes\Element\Pagination;

class AdjustNode extends Element
{
	const T_CONSTRUCTOR = '#adjust-constructor';

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(array $attributes = [])
	{
		parent::__construct('div', $attributes + [

			Element::IS => 'AdjustNode',

			self::T_CONSTRUCTOR => 'nodes',

			'data-adjust' => 'adjust-node'

		]);
	}

	/**
	 * Adds the `widget-adjust-node` class name.
	 *
	 * @inheritdoc
	 */
	protected function alter_class_names(array $class_names)
	{
		return parent::alter_class_names($class_names) + [

			'widget-adjust-node' => true

		];
	}

	protected function render_inner_html()
	{
		$rc = new Element('input', [

			'type' => 'hidden',
			'name' => $this['name'],
			'value' => $this['value']

		]) . parent::render_inner_html();

		$constructor = $this[self::T_CONSTRUCTOR];

		$rc .= '<div class="search">';
		$rc .= new Text([ 'class' => 'form-control search', 'placeholder' => $this->t('Search') ]);
		$rc .= $this->get_results([ 'selected' => $this['value'] ], $constructor);
		$rc .= '</div>';

		$this->dataset['constructor'] = $constructor;

		return $rc;
	}

	public function get_results(array $options = [], $constructor = 'nodes')
	{
		$options += [

			'page' => null,
			'search' => null,
			'selected' => null

		];

		list($records, $range) = $this->get_records($constructor, $options);

		$rc = $records ? $this->render_records($records, $range, $options) : $this->get_placeholder($options);

		return '<div class="results">' . $rc . '</div>';
	}

	protected function get_records($constructor, array $options, $limit = 10)
	{
		$model = $this->app->models[$constructor];

		if ($constructor == 'nodes')
		{
			$query = new Query($model);
		}
		else
		{
			$query = $model->filter_by_constructor($constructor);
		}

		$search = $options['search'];

		if ($search)
		{
			$conditions = '';
			$conditions_args = [];
			$words = explode(' ', trim($options['search']));
			$words = array_map('trim', $words);

			foreach ($words as $word)
			{
				$conditions .= ' AND title LIKE ?';
				$conditions_args[] = '%' . $word . '%';
			}

			$query->where(substr($conditions, 4), $conditions_args);
		}

		$query->visible;

		$count = $query->count;
		$page = $options['page'];
		$selected = $options['selected'];

		if ($selected && $page === null)
		{
			$ids = $query->select('nid')->order('updated_at DESC')->all(\PDO::FETCH_COLUMN);
			$positions = array_flip($ids);
			$pos = isset($positions[$selected]) ? $positions[$selected] : 0;
			$page = floor($pos / $limit);
			$ids = array_slice($ids, $page * $limit, $limit);
			$records = $ids ? $model->find($ids) : null;
		}
		else
		{
			$records = $query->order('updated_at DESC')->limit($page * $limit, $limit)->all;
		}

		return [ $records, [

			Pager::T_COUNT => $count,
			Pager::T_LIMIT => $limit,
			Pager::T_POSITION => $page

		] ];
	}

	protected function render_records($records, array $range, array $options)
	{
		$selected = $options['selected'];

		$rc = '<ul class="records">';

		foreach ($records as $record)
		{
			$rc .= $record->nid == $selected ? '<li class="selected">' : '<li>';
			$rc .= $this->render_record($record, $selected, $range, $options) . '</li>';
		}

		$n = count($records);
		$limit = $range[Pager::T_LIMIT];

		if ($n < $limit)
		{
			$rc .= str_repeat('<li class="empty"></li>', $limit - $n);
		}

		$rc .= '</ul>';

		$rc .= new Pagination('div', $range + []);

		return $rc;
	}

	protected function render_record(Node $record, $selected, array $range, array $options)
	{
		$recordid = $record->nid;

		return new A(\ICanBoogie\shorten($record->title), '#', [

			'data-nid' => $recordid,
			'data-title' => $record->title

		]);
	}

	protected function get_placeholder(array $options)
	{
		$search = $options['search'];

		return '<div class="no-response alert">' .

		(
			$search
			? $this->t('Aucun enregistrement ne correspond aux termes de recherche spécifiés (%search)', [ '%search' => $search ])
			: $this->t("Il n'y a pas d'enregistrements")
		)

		. '</div>';
	}
}
