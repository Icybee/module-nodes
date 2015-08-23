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

use ICanBoogie\ActiveRecord;
use ICanBoogie\ActiveRecord\Query;

use Icybee\ConstructorModel;

/**
 * Nodes model.
 *
 * @method Query offline()
 * @method Query online()
 * @method Query ordered($direction = -1)
 * @method Query similar_language($language = null)
 * @method Query similar_site($site_id = null)
 * @method Query visible()
 *
 * @property-read Query $offline A query scope for offline records.
 * @property-read Query $online A query scope for online records.
 * @property-read Query $ordered A query scope that orders records according to their creation date.
 * @property-read Query $similar_language A query scope for records of a similar language.
 * @property-read Query $similar_site A query scope for records of a similar site.
 * @property-read Query $visible A query scope that combines `online`, `similar_site`, and `similar_language`.
 */
class NodeModel extends ConstructorModel
{
	/**
	 * If the {@link Node::$updated_at} property is not defined it is set to the current datetime.
	 *
	 * If the {@link Node::$slug} property is empty but the {@link Node::$title} property is
	 * defined its value is used.
	 *
	 * The {@link Node:$slug} property is always slugized.
	 *
	 * @inheritdoc
	 */
	public function save(array $properties, $key=null, array $options=[])
	{
		if (!$key && empty($properties[Node::UUID]))
		{
			$properties[Node::UUID] = $this->obtain_uuid();
		}

		if (empty($properties[Node::SLUG]) && isset($properties[Node::TITLE]))
		{
			$properties[Node::SLUG] = $properties[Node::TITLE];
		}

		if (isset($properties[Node::SLUG]))
		{
			$properties[Node::SLUG] = slugize($properties[Node::SLUG], isset($properties[Node::LANGUAGE]) ? $properties[Node::LANGUAGE] : null);
		}

		return parent::save($properties, $key, $options);
	}

	/**
	 * Makes sure the node to delete is not used as a native target by other nodes.
	 *
	 * @throws \Exception if the node to delete is the native target of another node.
	 *
	 * @inheritdoc
	 */
	public function delete($key)
	{
		$native_refs = $this
			->select('nid')
			->filter_by_nativeid($key)
			->all(\PDO::FETCH_COLUMN);

		if ($native_refs)
		{
			throw new \Exception(\ICanBoogie\format('Node record cannot be deleted because it is used as native source by the following records: {0}', [ implode(', ', $native_refs) ]));
		}

		return parent::delete($key);
	}

	/**
	 * Alerts the query to match online records.
	 *
	 * @param Query $query
	 *
	 * @return Query
	 */
	protected function scope_online(Query $query)
	{
		return $query->filter_by_is_online(true);
	}

	/**
	 * Alerts the query to match offline records.
	 *
	 * @param Query $query
	 *
	 * @return Query
	 */
	protected function scope_offline(Query $query)
	{
		return $query->filter_by_is_online(false);
	}

	/**
	 * Alerts the query to match records visible on the current website.
	 *
	 * @param Query $query
	 *
	 * @return Query
	 */
	protected function scope_visible(Query $query)
	{
		return $query->online->similar_site->similar_language;
	}

	/**
	 * Alerts the query to match records of a similar site.
	 *
	 * A record is considered of a similar website when it doesn't belong to a website
	 * `(`siteid = 0')` or it matches the specified website.
	 *
	 * @param Query $query
	 * @param int|null $site_id The identifier of the website to match. If the identifier is
	 * `null` the current website identifier is used instead.
	 *
	 * @return Query
	 */
	protected function scope_similar_site(Query $query, $site_id = null)
	{
		if ($site_id === null)
		{
			$site_id = $this->app->site->siteid;
		}

		return $query->and('siteid = 0 OR siteid = ?', $site_id);
	}

	/**
	 * Alerts the query to match records of a similar language.
	 *
	 * A record is considered of a similar language when it doesn't have a language defined
	 * `(`language` = "")` or it matches the specified language.
	 *
	 * @param Query $query
	 * @param string $language The language to match. If the language is `null` the current
	 * language is used instead.
	 *
	 * @return Query
	 */
	protected function scope_similar_language(Query $query, $language = null)
	{
		if ($language === null)
		{
			$language = $this->app->site->language;
		}

		return $query->and('language = "" OR language = ?', $language);
	}

	/**
	 * Orders the records according to the date they were created.
	 *
	 * @param Query $query
	 * @param int $direction
	 *
	 * @return Query
	 */
	protected function scope_ordered(Query $query, $direction = -1)
	{
		return $query->order('created_at ' . ($direction < 0 ? 'DESC' : 'ASC'));
	}

	/**
	 * Finds the users the records belong to.
	 *
	 * The `user` property of the records is set to the user they belong to.
	 *
	 * @param array $records
	 *
	 * @return array
	 */
	public function including_user(array $records)
	{
		$keys = [];

		foreach ($records as $record)
		{
			$keys[$record->uid] = $record;
		}

		$users = $this->models['users']->find_using_constructor(array_keys($keys));

		/* @var $user \Icybee\Modules\Users\User */

		foreach ($users as $key => $user)
		{
			$keys[$key]->user = $user;
		}

		return $records;
	}

	/**
	 * Returns a UUID.
	 *
	 * @return string
	 */
	public function obtain_uuid()
	{
		for (;;)
		{
			$uuid = \ICanBoogie\generate_v4_uuid();

			if (!$this->filter_by_uuid($uuid)->exists)
			{
				return $uuid;
			}
		}
	}
}
