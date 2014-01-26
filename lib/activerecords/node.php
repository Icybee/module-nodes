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
use ICanBoogie\DateTime;

use Icybee\Modules\Sites\Site;
use Icybee\Modules\Users\User;

/**
 * A node representation.
 *
 * @property DateTime $created_at The date and time at which the node was created.
 * @property DateTime $updated_at The date and time at which the node was updated.
 * @property Node $native
 * @property User $user The user owning the node.
 * @property Site $site The site associated with the node.
 * @property-read Node $next
 * @property-read Node $previous
 * @property-read Node $translation
 * @property-read array[string]Node $translations
 * @property-read array[string]int $translations_keys
 * @property array[string]mixed $css_class_names {@see Node::get_css_class_names}.
 * @property string $css_class {@see Node::get_css_class}.
 */
class Node extends ActiveRecord implements \Brickrouge\CSSClassNames
{
	use \Brickrouge\CSSClassNamesProperty;

	const NID = 'nid';
	const UID = 'uid';
	const SITEID = 'siteid';
	const TITLE = 'title';
	const SLUG = 'slug';
	const CONSTRUCTOR = 'constructor';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const IS_ONLINE = 'is_online';
	const LANGUAGE = 'language';
	const NATIVEID = 'nativeid';

	/**
	 * Node key.
	 *
	 * @var int
	 */
	public $nid;

	/**
	 * Identifier of the owner of the node.
	 *
	 * @var int
	 */
	public $uid;

	/**
	 * Return the user owning the node.
	 *
	 * @return User
	 */
	protected function get_user()
	{
		return $this->uid ? ActiveRecord\get_model('users')->find($this->uid) : null;
	}

	/**
	 * Updates the {@link $uid} property using a {@link User} instance.
	 *
	 * @param User $user
	 */
	protected function set_user(User $user)
	{
		$this->uid = $user->uid;
	}

	/**
	 * Identifier of the site the node belongs to.
	 *
	 * The property is empty of the node is not bound to a website.
	 *
	 * @var int
	 */
	public $siteid;

	/**
	 * Returns the {@link Site} instance associated with the node.
	 *
	 * @return Site
	 */
	protected function get_site()
	{
		return $this->siteid ? ActiveRecord\get_model('sites')->find($this->siteid) : null;
	}

	/**
	 * Updates the {@link $siteid} property using a {@link Site} instance.
	 *
	 * @param Site $site
	 */
	protected function set_site(Site $site)
	{
		$this->siteid = $site->siteid;
	}

	/**
	 * Title of the node.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Slug of the node.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Returns the slug of the node.
	 *
	 * This function is only called if the {@link slug} property was empty during construct.
	 * By default it returns a normalized version of the {@link title} property.
	 *
	 * @return string
	 */
	protected function get_slug()
	{
		return \ICanBoogie\normalize($this->title);
	}

	/**
	 * Constructor of the node.
	 *
	 * @var string
	 */
	public $constructor;

	/**
	 * Returns the constructor of the page.
	 *
	 * This function is only called if the {@link constructor} property was empty during construct.
	 * By default it returns the identifier of the model managing the node.
	 *
	 * @return string
	 */
	protected function get_constructor()
	{
		return $this->model_id;
	}

	/**
	 * The date and time the node was created.
	 *
	 * @var \ICanBoogie\DateTime
	 */
	private $created_at;

	/**
	 * Returns the date and time the node was created.
	 *
	 * @return \ICanBoogie\DateTime
	 */
	protected function get_created_at()
	{
		$datetime = $this->created_at;

		if ($datetime instanceof DateTime)
		{
			return $datetime;
		}

		return $this->created_at = ($datetime === null) ? DateTime::none() : new DateTime($datetime, 'utc');
	}

	/**
	 * Sets the date and time the node was created.
	 *
	 * @param \DateTime|string $datetime
	 */
	protected function set_created_at($datetime)
	{
		$this->created_at = $datetime;
	}

	/**
	 * The date and time the node was updated.
	 *
	 * @var \ICanBoogie\DateTime
	 */
	private $updated_at;

	/**
	 * Returns the date and time the node was updated.
	 *
	 * @return \ICanBoogie\DateTime
	 */
	protected function get_updated_at()
	{
		$datetime = $this->updated_at;

		if ($datetime instanceof DateTime)
		{
			return $datetime;
		}

		return $this->updated_at = ($datetime === null) ? DateTime::none() : new DateTime($datetime, 'utc');
	}

	/**
	 * Sets the date and time the node was updated.
	 *
	 * @param \DateTime|string $datetime
	 */
	protected function set_updated_at($datetime)
	{
		$this->updated_at = $datetime;
	}

	/**
	 * Whether the node is online or not.
	 *
	 * @var bool
	 */
	public $is_online;

	/**
	 * Language of the node.
	 *
	 * The property is empty of the node is not bound to a language.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * Returns the language for the page.
	 *
	 * This function is only called if the {@link language} property was empty during construct. By
	 * default it returns the language of the {@link site} associated with the node.
	 *
	 * @return string
	 */
	protected function get_language()
	{
		return $this->site ? $this->site->language : null;
	}

	/**
	 * Identifier of the node this node is translating.
	 *
	 * The property is empty if the node is not translating another node.
	 *
	 * @var int
	 */
	public $nativeid;

	/**
	 * Creates a Node instance.
	 *
	 * The following properties are unset if they are empty, so that their getter may return
	 * a fallback value:
	 *
	 * - {@link constructor}: Defaults to the model identifier. {@link get_constructor}.
	 * - {@link language}: Defaults to the associated site's language. {@link get_language}.
	 * - {@link slug}: Defaults to a normalize title. {@link get_slug}.
	 */
	public function __construct($model='nodes')
	{
		if (empty($this->constructor))
		{
			unset($this->constructor);
		}

		if (empty($this->language))
		{
			unset($this->language);
		}

		if (empty($this->slug))
		{
			unset($this->slug);
		}

		parent::__construct($model);
	}

	/**
	 * Fires {@link \Brickrouge\AlterCSSClassNamesEvent} after the {@link $css_class_names} property
	 * was get.
	 */
	public function __get($property)
	{
		$value = parent::__get($property);

		if ($property === 'css_class_names')
		{
			new \Brickrouge\AlterCSSClassNamesEvent($this, $value);
		}

		return $value;
	}

	/**
	 * Return the previous visible sibling for the node.
	 *
	 * @return Node|bool
	 */
	protected function lazy_get_previous()
	{
		return $this->model->own->visible
		->where('nid != ? AND created_at <= ?', $this->nid, $this->created_at)
		->order('created_at DESC')
		->one;
	}

	/**
	* Return the next visible sibling for the node.
	*
	* @return Node|bool
	*/
	protected function lazy_get_next()
	{
		return $this->model->own->visible
		->where('nid != ? AND created_at > ?', $this->nid, $this->created_at)
		->order('created_at')
		->one;
	}

	static private $translations_keys;

	protected function lazy_get_translations_keys()
	{
		global $core;

		$native_language = $this->siteid ? $this->site->native->language : $core->language;

		if (!self::$translations_keys)
		{
			$groups = $core->models['nodes']->select('nativeid, nid, language')->where('nativeid != 0')->order('language')->all(\PDO::FETCH_GROUP | \PDO::FETCH_NUM);
			$keys = array();

			foreach ($groups as $native_id => $group)
			{
				foreach ($group as $row)
				{
					list($nativeid, $tlanguage) = $row;

					$keys[$native_id][$nativeid] = $tlanguage;
				}
			}

			foreach ($keys as $native_id => $translations)
			{
				$all = array($native_id => $native_language) + $translations;

				foreach ($translations as $nativeid => $tlanguage)
				{
					$keys[$nativeid] = $all;
					unset($keys[$nativeid][$nativeid]);
				}
			}

			self::$translations_keys = $keys;
		}

		$nid = $this->nid;

		return isset(self::$translations_keys[$nid]) ? self::$translations_keys[$nid] : null;
	}

	/**
	 * Returns the translation in the specified language for the record, or the record itself if no
	 * translation can be found.
	 *
	 * @param string $language The language for the translation. If the language is empty, the
	 * current language (as defined by `$core->language`) is used.
	 *
	 * @return Node The translation for the record, or the record itself if
	 * no translation could be found.
	 */
	public function translation($language=null)
	{
		global $core;

		if (!$language)
		{
			$language = $core->language;
		}

		$translations = $this->translations_keys;

		if ($translations)
		{
			$translations = array_flip($translations);

			if (isset($translations[$language]))
			{
				return $this->model->find($translations[$language]);
			}
		}

		return $this;
	}

	protected function lazy_get_translation()
	{
		return $this->translation();
	}

	protected function lazy_get_translations()
	{
		$translations = $this->translations_keys;

		if (!$translations)
		{
			return;
		}

		return $this->model->find(array_keys($translations));
	}

	/**
	 *
	 * Return the native node for this translated node.
	 */
	protected function lazy_get_native()
	{
		return $this->nativeid ? $this->model[$this->nativeid] : $this;
	}

	/**
	 * Returns the CSS class names of the node.
	 *
	 * @return array[string]mixed
	 */
	protected function get_css_class_names()
	{
		$nid = $this->nid;
		$slug = $this->slug;

		return array
		(
			'type' => 'node',
			'id' => $nid ? "node-{$nid}" : null,
			'slug' => $slug ? "node-slug-{$slug}" : null,
			'constructor' => 'constructor-' . \ICanBoogie\normalize($this->constructor)
		);
	}
}