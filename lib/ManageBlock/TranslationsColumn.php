<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\ManageBlock;

use Icybee\ManageBlock\Column;
use Icybee\Modules\Nodes\ManageBlock;
use Icybee\Modules\Nodes\Node;

/**
 * Representation of the `translations` column.
 */
class TranslationsColumn extends Column
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'title' => null,
			'orderable' => false,
			'class' => 'cell-fitted'

		]);
	}

	/**
	 * @param Node[] $records
	 *
	 * @inheritdoc
	 */
	public function alter_records(array $records)
	{
		$records = parent::alter_records($records);

		$this->resolve_translations($records);

		return $records;
	}

	protected $translations_by_records;

	/**
	 * @param Node[] $records
	 */
	protected function resolve_translations(array $records)
	{
		$translations = [];
		$translations_by_records = [];

		/* @var $app \ICanBoogie\Core|\Icybee\Binding\CoreBindings */

		$app = \ICanBoogie\app();
		$site = $app->site;
		$sites = $app->models['sites'];
		$site_translations = $site->translations;

		if (!$site_translations)
		{
			return;
		}

		$site_translations_ids = [];

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
			$native_ids = [];

			foreach ($records as $record)
			{
				$native_ids[] = $record->nid;
			}

			if (!$native_ids)
			{
				return;
			}

			$translations_raw = $app->models['nodes']
				->select('siteid, nativeid, language, nid')
				->where([ 'nativeid' => $native_ids, 'siteid' => $site_translations_ids ])
				->order('FIELD(siteid, ' . implode(',', $site_translations_ids) . ')')
				->all;

			if (!$translations_raw)
			{
				return;
			}

			foreach ($translations_raw as $translation)
			{
				$translations_by_records[$translation['nativeid']][$translation['nid']] = [

					'site' => $sites[$translation['siteid']],
					'siteid' => $translation['siteid'],
					'language' => $translation['language']

				];
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

		$infos = $app->models['nodes']
			->select('siteid, language')
			->where('nid IN(' . $ids . ')')
			->order('FIELD(nid, ' . $ids . ')')
			->all;

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

	/**
	 * @param Node $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		if (empty($this->translations_by_records[$record->nid]))
		{
			return null;
		}

		$translations = $this->translations_by_records[$record->nid];

		$rc = '';

		foreach ($translations as $native_id => $translation)
		{
			$rc .= ', <a href="' . $translation['site']->url . '/admin/' . $record->constructor . '/' . $native_id . '/edit">' . $translation['language'] . '</a>';
		}

		return '<span class="translations">' . substr($rc, 2) . '</span>';
	}
}
