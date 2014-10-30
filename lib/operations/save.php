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

use ICanBoogie\DateTime;

/**
 * Saves a node.
 *
 * @property string $uuid A v4 UUID suitable for a new record.
 */
class SaveOperation extends \ICanBoogie\SaveOperation
{
	/**
	 * Overrides the method to handle the following properties:
	 *
	 * - `uid`: Only users with the PERMISSION_ADMINISTER permission can choose the user of a record.
	 * If the user saving a record has no such permission, the Node::UID property is removed from
	 * the properties created by the parent method. If a record is created (the operation's key is
	 * empty) the identifier of the current user is used.
	 *
	 * - `siteid`: If the user is creating a new record or the user has no permission to choose the
	 * record's site, the property is set to the value of the working site's id.
	 *
	 * - `created_at`: Is set to "now" for new records.
	 *
	 * - `updated_at`: Is set to "now".
	 *
	 * Also, the following default values are used:
	 *
	 * - `uid`: 0
	 * - `nativeid`: 0
	 * - `language`: an empty string
	 */
	protected function lazy_get_properties()
	{
		$properties = parent::lazy_get_properties() + [

			Node::UID => 0,
			Node::NATIVEID => 0,
			Node::LANGUAGE => ''

		];

		$user = $this->app->user;
		$key = $this->key;

		# uid

		if (!$user->has_permission(Module::PERMISSION_ADMINISTER, $this->module))
		{
			unset($properties[Node::UID]);
		}

		if (empty($properties[Node::UID]))
		{
			if (!$key)
			{
				$properties[Node::UID] = $user->uid;
			}
			else
			{
				unset($properties[Node::UID]);
			}
		}

		# siteid

		if (!$key || !$user->has_permission(Module::PERMISSION_MODIFY_BELONGING_SITE))
		{
			$properties[Node::SITEID] = $this->app->site_id;
		}

		if (!$key)
		{
			$properties[Node::CREATED_AT] = DateTime::now();
			$properties[Node::UUID] = $this->uuid;
		}

		# updated_at

		$properties[Node::UPDATED_AT] = DateTime::now();

		return $properties;
	}

	/**
	 * Returns the form from the `edit` block if the getter wasn't able to retrieve the form. This
	 * is currently used to create records using XHR.
	 */
	protected function lazy_get_form()
	{
		$form = parent::lazy_get_form();

		if ($form)
		{
			return $form;
		}

		$block = $this->module->getBlock('edit', $this->key);

		return $block->element;
	}

	/**
	 * Return a v4 UUID suitable for a new record.
	 *
	 * @return string
	 */
	protected function lazy_get_uuid()
	{
		return $this->module->model->obtain_uuid();
	}

	/**
	 * Overrides the method to provide a nicer log message.
	 */
	protected function process()
	{
		$rc = parent::process();

		$this->response->message = $this->format($rc['mode'] == 'update' ? '%title has been updated in :module.' : '%title has been created in :module.', [

			'title' => \ICanBoogie\shorten($this->record->title),
			'module' => $this->module->title

		]);

		return $rc;
	}
}
