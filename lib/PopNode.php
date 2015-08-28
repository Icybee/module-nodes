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

use Brickrouge\Element;
use ICanBoogie\ActiveRecord\Model;

class PopNode extends \Brickrouge\Widget
{
	const T_CONSTRUCTOR = '#popnode-constructor';

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(array $attributes=[])
	{
		parent::__construct('div', $attributes + [

			self::T_CONSTRUCTOR => 'nodes',

			'placeholder' => 'Sélectionner un enregistrement',
			'class' => 'spinner',
			'data-adjust' => 'adjust-node',
			'tabindex' => 0

		]);
	}

	protected function alter_dataset(array $dataset)
	{
		return parent::alter_dataset($dataset + [

			'constructor' => $this[self::T_CONSTRUCTOR],
			'placeholder' => $this['placeholder']

		]);
	}

	protected function render_inner_html()
	{
		$rc = parent::render_inner_html();

		$constructor = $this[self::T_CONSTRUCTOR];
		$value = $this['value'] ?: $this[self::DEFAULT_VALUE];
		$record = null;

		if ($value)
		{
			$model = $this->app->models[$constructor];

			try
			{
				$record = is_numeric($value) ? $model[$value] : $this->getEntry($model, $value);
			}
			catch (\Exception $e)
			{
				\ICanBoogie\log_error('Missing record %nid', [ '%nid' => $value ]);
			}
		}

		if (!$record)
		{
			$this->add_class('placeholder');
			$value = null;
		}

		$rc .= new Element('input', [ 'type' => 'hidden', 'name' => $this['name'], 'value' => $value ]);

		$placeholder = $this['placeholder'];

		if ($placeholder)
		{
			$rc .= '<em class="spinner-placeholder">' . \ICanBoogie\escape($placeholder) . '</em>';
		}

		$rc .= '<span class="spinner-content">' . $this->getPreview($record) . '</span>';

		return $rc;
	}

	/**
	 * Returns the record from a model matching a value.
	 *
	 * @param Model|NodeModel $model
	 * @param string $value
	 *
	 * @return Node
	 */
	protected function getEntry(Model $model, $value)
	{
		return $model->where('title = ? OR slug = ?', $value, $value)->order('created_at DESC')->one;
	}

	protected function getPreview($entry)
	{
		if (!$entry)
		{
			return '';
		}

		$title = $entry->title;
		$label = \ICanBoogie\shorten($title, 32, .75, $shortened);

		$rc  = '<span class="title"' . ($shortened ? ' title="' . \Brickrouge\escape($title) . '"' : '') . '>';
		$rc .= \Brickrouge\escape($label) . '</span>';

		return $rc;
	}
}
