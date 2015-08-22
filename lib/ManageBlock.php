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

use Brickrouge\Document;
use Brickrouge\Element;

use Icybee\ManageBlock\DateTimeColumn;
use Icybee\Modules\Users\ManageBlock\UserColumn;

/**
 * A block to manage nodes.
 */
class ManageBlock extends \Icybee\ManageBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(Module $module, array $attributes = [])
	{
		parent::__construct($module, $attributes + [

			self::T_ORDER_BY => [ Node::UPDATED_AT, 'desc' ]

		]);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `title`: An instance of {@link ManageBlock\TitleColumn}.
	 * - `url`: An instance of {@link ManageBlock\URLColumn}.
	 * - `is_online`: An instance of {@link ManageBlock\IsOnlineColumn}.
	 * - `uid`: An instance of {@link \Icybee\Modules\Users\ManageBlock\UserColumn}.
	 * - `created_at`: An instance of {@link \Icybee\ManageBlock\DateTimeColumn}.
	 * - `updated_at`: An instance of {@link \Icybee\ManageBlock\DateTimeColumn}.
	 *
	 * @inheritdoc
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			Node::TITLE      => ManageBlock\TitleColumn::class,
			'url'            => ManageBlock\URLColumn::class,
			Node::IS_ONLINE  => ManageBlock\IsOnlineColumn::class,
			Node::UID        => UserColumn::class,
			Node::CREATED_AT => DateTimeColumn::class,
			Node::UPDATED_AT => DateTimeColumn::class

		]);
	}

	/**
	 * Adds the following jobs:
	 *
	 * - `online`: Set the selected records online.
	 * - `offline`: Set the selected records offline.
	 *
	 * @inheritdoc
	 */
	protected function get_available_jobs()
	{
		return array_merge(parent::get_available_jobs(), [

			'online' => $this->t('online.operation.short_title'),
			'offline' => $this->t('offline.operation.short_title')

		]);
	}

	/**
	 * Alters the query with the `filter_by_constructor` and `similar_site`.
	 *
	 * @inheritdoc
	 */
	protected function alter_query(Query $query, array $filters)
	{
		return parent::alter_query($query, $filters)
		->filter_by_constructor($this->module->id)
		->similar_site;
	}

	// TODO-20130629: refactor this
	protected function __deprecated__render_cell_title($record, $property)
	{
		static $languages;
		static $languages_count;

		$app = $this->app;

		if ($languages === null)
		{
			$languages = $app->models['sites']->count('language');
			$languages_count = count($languages);
		}

		$title = $record->$property;
		$label = $title ? \ICanBoogie\escape(\ICanBoogie\shorten($title, 52, .75, $shortened)) : $this->t('<em>no title</em>');

		if ($shortened)
		{
			$label = str_replace('…', '<span class="light">…</span>', $label);
		}

		$rc = '';

		if ($rc)
		{
			$rc .= ' ';
		}

		$rc .= new Element('a', [

			Element::INNER_HTML => $label,

			'class' => 'edit',
			'href' => $this->app->url_for("admin:{$record->constructor}:edit", $record),
			'title' => $shortened ? $this->t('edit_named', [ ':title' => $title ? $title : 'unnamed' ]) : $this->t('edit'),

		]);

		$metas = '';

		$language = $record->language;

		if ($languages_count > 1 && $language != $app->site->language)
		{
			$metas .= ', <span class="language">' . ($language ? $language : 'multilingue') . '</span>';
		}

		if (!$record->siteid)
		{
			$metas .= ', multisite';
		}

		if ($metas)
		{
			$rc .= '<span class="metas small light">:' . substr($metas, 2) . '</span>';
		}

		return $rc;
	}
}
