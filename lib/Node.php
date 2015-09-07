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

use Brickrouge\AlterCSSClassNamesEvent;
use Brickrouge\CSSClassNames;
use Brickrouge\CSSClassNamesProperty;

use ICanBoogie\Binding\PrototypedBindings as CoreBindings;
use Icybee\Modules\Registry\Binding\NodeBindings as RegistryBindings;
use Icybee\Modules\Sites\Binding\NodeBindings as SiteBindings;
use Icybee\Modules\Sites\Site;
use Icybee\Modules\Users\User;

/**
 * A node representation.
 *
 * @method string url($type)
 * @property-read string $url
 * @property-read string $absolute_url
 *
 * @property-read NodeModel $model
 * @property Node $native
 * @property User $user The user owning the node.
 * @property Site $site The site associated with the node.
 * @property-read Node $next
 * @property-read Node $previous
 * @property-read Node $translation
 * @property-read Node[] $translations
 * @property-read array $translations_keys
 * @property array $css_class_names {@see Node::get_css_class_names}.
 * @property string $css_class {@see Node::get_css_class}.
 */
class Node extends ActiveRecord implements CSSClassNames
{
	use CoreBindings;
	use SiteBindings;
	use RegistryBindings;

	use CSSClassNamesProperty;
	use ActiveRecord\CreatedAtProperty;
	use ActiveRecord\UpdatedAtProperty;

	const MODEL_ID = 'nodes';

	const NID = 'nid';
	const UID = 'uid';
	const SITEID = 'siteid';
	const UUID = 'uuid';
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
	public $uid = 0;

	/**
	 * Return the user owning the node.
	 *
	 * @return User
	 */
	protected function get_user()
	{
		return $this->uid ? $this->model->models['users'][$this->uid] : null;
	}

	/**
	 * Updates the {@link $uid} property using a {@link User} instance.
	 *
	 * @param User $user
	 */
	protected function set_user(User $user = null)
	{
		$this->uid = $user ? $user->uid : 0;
	}

	/**
	 * Identifier of the site the node belongs to.
	 *
	 * The property is empty of the node is not bound to a website.
	 *
	 * @var int
	 */
	public $siteid = 0;

	/**
	 * Returns the {@link Site} instance associated with the node.
	 *
	 * @return Site
	 */
	protected function get_site()
	{
		return $this->siteid ? $this->model->models['sites'][$this->siteid] : null;
	}

	/**
	 * Updates the {@link $siteid} property using a {@link Site} instance.
	 *
	 * @param Site $site
	 */
	protected function set_site(Site $site = null)
	{
		$this->siteid = $site ? $site->siteid : 0;
	}

	/**
	 * A v4 UUID.
	 *
	 * @var string
	 */
	public $uuid;

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
	 * This function is only invoked if the {@link slug} property was empty during construct.
	 * By default it returns a normalized version of the {@link title} property.
	 *
	 * @return string
	 */
	protected function get_slug()
	{
		return slugize($this->title, $this->language);
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
	 * Whether the node is online or not.
	 *
	 * @var bool
	 */
	public $is_online = false;

	/**
	 * Language of the node.
	 *
	 * The property is empty of the node is not bound to a language.
	 *
	 * @var string
	 */
	public $language = '';

	/**
	 * Returns the language for the site.
	 *
	 * This function is only invoked if the {@link language} property is inaccessible. By
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
	public $nativeid = 0;

	/**
	 * Creates a Node instance.
	 *
	 * The following properties are unset if they are empty, so that their getter may return
	 * a fallback value:
	 *
	 * - {@link constructor}: Defaults to the model identifier. {@link get_constructor}.
	 * - {@link language}: Defaults to the associated site's language. {@link get_language}.
	 * - {@link slug}: Defaults to a normalize title. {@link get_slug}.
	 *
	 * @inheritdoc
	 */
	public function __construct($model = self::MODEL_ID)
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
	 * Fires {@link AlterCSSClassNamesEvent} after the {@link $css_class_names} property
	 * was get.
	 *
	 * @inheritdoc
	 */
	public function __get($property)
	{
		$value = parent::__get($property);

		if ($property === 'css_class_names')
		{
			new AlterCSSClassNamesEvent($this, $value);
		}

		return $value;
	}

	/**
	 * Obtains a UUID from the model if the {@link $uuid} property is empty.
	 *
	 * @inheritdoc
	 */
	public function save()
	{
		if (!$this->uuid)
		{
			$this->uuid = $this->model->obtain_uuid();
		}

		if ($this->get_created_at()->is_empty)
		{
			$this->set_created_at('now');
		}

		$this->set_updated_at('now');

		return parent::save();
	}

	/**
	 * Sets {@link $created_at} to "now" if it is empty, and sets {@link $updated_at} to "now"
	 * before passing the method to the parent class.
	 *
	 * Adds `language` if it is not defined.
	 *
	 * @inheritdoc
	 */
	protected function alter_persistent_properties(array $properties, ActiveRecord\Model $model)
	{
		return parent::alter_persistent_properties($properties, $model) + [

			'language' => ''

		];
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
		$app = $this->app;
		$native_language = $this->siteid ? $this->site->native->language : $app->language;

		if (!self::$translations_keys)
		{
			$keys = [];
			$groups = $this->model->models['nodes']
				->select('nativeid, nid, language')
				->where('nativeid != 0')
				->order('language')
				->all(\PDO::FETCH_GROUP | \PDO::FETCH_NUM);

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
				$all = [ $native_id => $native_language ] + $translations;

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
	 * current language (as defined by `$app->language`) is used.
	 *
	 * @return Node The translation for the record, or the record itself if
	 * no translation could be found.
	 */
	public function translation($language = null)
	{
		if (!$language)
		{
			$language = $this->app->language;
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
			return [];
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
	 * @return array
	 */
	protected function get_css_class_names()
	{
		$nid = $this->nid;
		$slug = $this->slug;

		return [

			'type' => 'node',
			'id' => $nid ? "node-{$nid}" : null,
			'slug' => $slug ? "node-slug-{$slug}" : null,
			'constructor' => 'constructor-' . \ICanBoogie\normalize($this->constructor)

		];
	}
}
