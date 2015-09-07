<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Block;

use Brickrouge\Element;
use Brickrouge\Form;

use Icybee\Modules\Nodes\Module;
use Icybee\Modules\Nodes\Node;
use Icybee\Modules\Nodes\TitleSlugCombo;

/**
 * A block used to edit a node.
 */
class EditBlock extends \Icybee\Block\EditBlock
{
	/**
	 * Adds the "Visibility" group.
	 *
	 * The visibility group should be used to group controls related to the visibility of the
	 * record on the site e.g. online status, view exclusion, navigation exclusion...
	 *
	 * The visibility group is created with an initial weight of 400.
	 */
	protected function lazy_get_attributes()
	{
		$attributes = parent::lazy_get_attributes();

		$attributes[Element::GROUPS]['visibility'] = [

			'title' => 'Visibility',
			'weight' => 400

		];

		return $attributes;
	}

	/**
	 * Adds the `title`, `is_online`, `uid` and `site_id` elements.
	 *
	 * The `uid` and `site_id` elements are added according to the context.
	 */
	protected function lazy_get_children()
	{
		$values = $this->values;

		return array_merge(parent::lazy_get_children(), [

			Node::TITLE => new TitleSlugCombo([

				Form::LABEL => 'title',
				Element::REQUIRED => true,
				TitleSlugCombo::T_NODEID => $values[Node::NID],
				TitleSlugCombo::T_SLUG_NAME => 'slug'

			]),

			Node::UID => $this->get_control__user(),
			Node::SITE_ID => $this->get_control__site(),	// TODO-20100906: this should be added by the "sites" modules using the alter event.
			Node::IS_ONLINE => new Element(Element::TYPE_CHECKBOX, [

				Element::LABEL => 'is_online',
				Element::DESCRIPTION => 'is_online',
				Element::GROUP => 'visibility'

			])
		]);
	}

	/**
	 * Returns the control for the user of the node.
	 *
	 * @return Element|null
	 */
	protected function get_control__user()
	{
		$app = $this->app;

		if (!$app->user->has_permission(Module::PERMISSION_ADMINISTER, $this->module))
		{
			return null;
		}

		$users = $app->models['users']->select('uid, username')->order('username')->pairs;

		if (count($users) < 2)
		{
			return null;
		}

		return new Element('select', [

			Form::LABEL => 'User',
			Element::OPTIONS => [ null => '' ] + $users,
			Element::REQUIRED => true,
			Element::DEFAULT_VALUE => $app->user->uid,
			Element::GROUP => 'admin',
			Element::DESCRIPTION => 'user'

		]);
	}

	/**
	 * Returns control for the site the node belongs to.
	 *
	 * @return Element|null
	 */
	protected function get_control__site()
	{
		$app = $this->app;

		if (!$app->user->has_permission(Module::PERMISSION_MODIFY_BELONGING_SITE, $this->module))
		{
			return null;
		}

		$sites = $app->models['sites']->select('site_id, IF(admin_title != "", admin_title, concat(title, ":", language))')->order('admin_title, title')->pairs;

		if (count($sites) < 2)
		{
			$this->attributes[Form::HIDDENS][Node::SITE_ID] = $app->site_id;

			return null;
		}

		return new Element('select', [

			Form::LABEL => 'site_id',
			Element::OPTIONS => [ null => '' ] + $sites,
			Element::GROUP => 'admin',
			Element::DESCRIPTION => 'site_id'

		]);
	}
}
