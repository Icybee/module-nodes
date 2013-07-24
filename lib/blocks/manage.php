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
use ICanBoogie\Route;

use Brickrouge\A;
use Brickrouge\Document;
use Brickrouge\Element;

use Icybee\ManageBlock\Column;

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

	public function __construct(Module $module, array $attributes=array())
	{
		parent::__construct
		(
			$module, $attributes + array
			(
				self::T_ORDER_BY => array(Node::MODIFIED, 'desc')
			)
		);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `title`: An instance of {@link ManageBlock\TitleColumn}.
	 * - `url`: An instance of {@link ManageBlock\URLColumn}.
	 * - `is_online`: An instance of {@link ManageBlock\IsOnlineColumn}.
	 * - `uid`: An instance of {@link \Icybee\Modules\Users\ManageBlock\UserColumn}.
	 * - `created`: An instance of {@link \Icybee\ManageBlock\DateTimeColumn}.
	 * - `modified`: An instance of {@link \Icybee\ManageBlock\DateTimeColumn}.
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), array
		(
			Node::TITLE => __CLASS__ . '\TitleColumn',
			'url' => __CLASS__ . '\URLColumn',
			Node::IS_ONLINE => __CLASS__ . '\IsOnlineColumn',
			Node::UID => 'Icybee\Modules\Users\ManageBlock\UserColumn',
			Node::CREATED => 'Icybee\ManageBlock\DateTimeColumn',
			Node::MODIFIED => 'Icybee\ManageBlock\DateTimeColumn'
		));
	}

	/**
	 * Adds the following jobs:
	 *
	 * - `online`: Set the selected records online.
	 * - `offline`: Set the selected records offline.
	 */
	protected function get_available_jobs()
	{
		return array_merge(parent::get_available_jobs(), array
		(
			'online' => I18n\t('online.operation.short_title'),
			'offline' => I18n\t('offline.operation.short_title')
		));
	}

	/*
	protected function parseColumns($columns)
	{
		$translations = $this->model->where('constructor = ? AND nativeid != 0', (string) $this->module)->count();

		if ($translations)
		{
			$expanded = array();

			foreach ($columns as $identifier => $column)
			{
				$expanded[$identifier] = $column;

				if ($identifier == 'is_online')
				{
					$expanded['translations'] = __CLASS__ . '\TranslationsColumn';
				}
			}

			$columns = $expanded;
		}

		return parent::parseColumns($columns);
	}
	*/

	/**
	 * Alters the query with the 'is_online' and 'uid' filters. Also adds a condition on the
	 * siteid, which must be the same as the current site or zero.
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
		global $core;
		static $languages;
		static $languages_count;

		if ($languages === null)
		{
			$languages = $core->models['sites']->count('language');
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

		$rc .= new Element
		(
			'a', array
			(
				Element::INNER_HTML => $label,

				'class' => 'edit',
				'href' => \ICanBoogie\Routing\contextualize("/admin/{$record->constructor}/{$record->nid}/edit"),
				'title' => $shortened ? $this->t('edit_named', array(':title' => $title ? $title : 'unnamed')) : $this->t('edit'),
			)
		);

		$metas = '';

		$language = $record->language;

		if ($languages_count > 1 && $language != $core->site->language)
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

namespace Icybee\Modules\Nodes\ManageBlock;

use Brickrouge\A;
use Brickrouge\Element;

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\I18n;

use Icybee\ManageBlock\Column;
use Icybee\ManageBlock\FilterDecorator;

class EditDecorator extends \Icybee\ManageBlock\EditDecorator
{
	/**
	 * The component is shortened if it's longer than 52 characters, in which case the title of
	 * the element is modified to include the original component.
	 */
	public function render()
	{
		$element = parent::render();
		$component = $this->component;
		$html = $component ? \ICanBoogie\escape(\ICanBoogie\shorten($component, 52, .75, $shortened)) : I18n\t('<em>no title</em>');

		if (!$shortened)
		{
			return $element;
		}

		$element[Element::INNER_HTML] = str_replace('…', '<span class="light">…</span>', $html);
		$element['title'] = I18n\t('manage.edit_named', array(':title' => $component ? $component : 'unnamed'));

		return $element;
	}
}

/**
 * Representation of the `title` column.
 */
class TitleColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, array
			(
				'discreet' => false
			)
		);
	}

	public function render_cell($record)
	{
		return new EditDecorator($record->title, $record);
	}
}

/**
 * Representation of the `url` column.
 */
class URLColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, array
			(
				'title' => null,
				'class' => 'cell-fitted',
				'orderable' => false
			)
		);
	}

	public function render_cell($record)
	{
		$url = $record->url;

		if (!$url || $url{0} == '#')
		{
			return;
		}

		return new A
		(
			'', $url, array
			(
				'title' => $this->manager->t('View this entry on the website'),
				'class' => 'icon-external-link',
				'target' => '_blank'
			)
		);
	}
}

/**
 * Representation of the `is_online` column.
 */
class IsOnlineColumn extends \Icybee\ManageBlock\BooleanColumn
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'filters' => array
				(
					'options' => array
					(
						'=1' => 'Online',
						'=0' => 'Offline'
					)
				),

				'cell_renderer' => __NAMESPACE__ . '\IsOnlineCellRenderer'
			)
		);
	}
}

/**
 * Renderer for the `is_online` column cell.
 */
class IsOnlineCellRenderer extends \Icybee\ManageBlock\BooleanCellRenderer
{
	/**
	 * Adds a title to the decorator checkbox element.
	 */
	public function __invoke($record, $property)
	{
		$element = parent::__invoke($record, $property);
		$element['title'] = "Publish or unpublish the record form the website";

		return $element;
	}
}

/**
 * Representation of the `translations` column.
 */
class TranslationsColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'orderable' => false
			)
		);
	}

	public function alter_records(array $records)
	{
		$records = parent::alter_records($records);

		$this->resolve_translations($records);

		return $records;
	}

	protected $translations_by_records;

	protected function resolve_translations(array $records)
	{
		global $core;

		$translations = array();
		$translations_by_records = array();

		$site = $core->site;
		$sites = $core->models['sites'];
		$site_translations = $site->translations;

		if (!$site_translations)
		{
			return;
		}

		$site_translations_ids = array();

		foreach ($site_translations as $site_translation)
		{
			$site_translations_ids[] = $site_translation->siteid;
		}

		if ($site->nativeid)
		{
			foreach ($records as $record)
			{
				$nativeid = $record->nativeid;

				if (!$nativeid)
				{
					continue;
				}

				$translations[$nativeid] = true;
				$translations_by_records[$record->nid][$nativeid] = true;
			}
		}
		else
		{
			$native_ids = array();

			foreach ($records as $record)
			{
				$native_ids[] = $record->nid;
			}

			if (!$native_ids)
			{
				return;
			}

			$translations_raw = $core->models['nodes']->select('siteid, nativeid, language, nid')->where(array('nativeid' => $native_ids, 'siteid' => $site_translations_ids))->order('FIELD(siteid, ' . implode(',', $site_translations_ids) . ')')->all;

			if (!$translations_raw)
			{
				return;
			}

			foreach ($translations_raw as $translation)
			{
				$translations_by_records[$translation['nativeid']][$translation['nid']] = array
				(
					'site' => $sites[$translation['siteid']],
					'siteid' => $translation['siteid'],
					'language' => $translation['language']
				);
			}

			$this->translations_by_records = $translations_by_records;

			return;
		}

		if (!$translations)
		{
			return;
		}

		$translations = array_keys($translations);
		$ids = implode(',', $translations);

		$infos = $core->models['nodes']->select('siteid, language')->where('nid IN(' . $ids . ')')->order('FIELD(nid, ' . $ids . ')')->all;

		$translations = array_combine($translations, $infos);

		foreach ($translations_by_records as $nid => $nt)
		{
			foreach (array_keys($nt) as $nativeid)
			{
				$translation = $translations[$nativeid];
				$translation['site'] = $sites[$translation['siteid']];

				$translations_by_records[$nid][$nativeid] = $translation;
			}
		}

		$this->translations_by_records = $translations_by_records;
	}

	public function render_cell($record)
	{
		if (empty($this->translations_by_records[$record->nid]))
		{
			return;
		}

		$translations = $this->translations_by_records[$record->nid];

		$rc = '';

		foreach ($translations as $nativeid => $translation)
		{
			$rc .= ', <a href="' . $translation['site']->url . '/admin/' . $record->constructor . '/' . $nativeid . '/edit">' . $translation['language'] . '</a>';
		}

		return '<span class="translations">' . substr($rc, 2) . '</span>';
	}
}