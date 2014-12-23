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

use ICanBoogie\I18n;

use Brickrouge;
use Brickrouge\Element;
use Brickrouge\Text;

class TitleSlugCombo extends \Brickrouge\Widget
{
	const T_NODEID = '#node-id';
	const T_SLUG_NAME = '#slug-name';

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	private $title_el;
	private $slug_tease;
	private $slug_el;

	public function __construct(array $attributes=[])
	{
		$attributes += [

			self::T_SLUG_NAME => null,
			Element::LABEL => null,
			Element::LABEL_POSITION => 'before'

		];

		parent::__construct('div', $attributes + [

			Element::CHILDREN => [

				$this->title_el = new Text([

					Element::LABEL_POSITION => $attributes[Element::LABEL_POSITION],
					Element::REQUIRED => true

				]),

				$this->slug_tease = new Element('span', [

					self::INNER_HTML => '&nbsp;',

					'class' => 'slug-reminder small'

				]),

				'<a href="#slug-collapse" class="small">' . I18n\t('fold', [], [ 'scope' => 'titleslugcombo.element' ]) . '</a>',

				'<div class="slug">',

				$this->slug_el = new Text([

					Element::LABEL => 'slug',
					Element::LABEL_POSITION => 'above',
					Element::DESCRIPTION => 'slug',

					'name' => $attributes[self::T_SLUG_NAME]

				]),

				'</div>'
			],

			'data-auto-label' => '<em>' . I18n\t('auto', [], [ 'scope' => 'titleslugcombo.element' ]) . '</em>'

		]);
	}

	public function offsetSet($offset, $value)
	{
		if ($offset == 'name')
		{
			$this->title_el['name'] = $value;

			if (!$this->slug_el['name'])
			{
				$this->slug_el['name'] = $value . 'slug';
			}
		}

		parent::offsetSet($offset, $value);
	}

	protected function render_inner_html()
	{
		$slug = $this->slug_el['value'];

		$tease = '<strong>Slug&nbsp;:</strong> ';
		$tease .= '<a href="#slug-edit" title="' . I18n\t('edit', [], [ 'scope' => 'titleslugcombo.element' ]) . '">' . ($slug ? \ICanBoogie\escape(\ICanBoogie\shorten($slug)) : $this->dataset['auto-label']) . '</a>';
		$tease .= ' <span>&ndash; <a href="slug-delete" class="warn">' . I18n\t('reset', [], [ 'scope' => 'titleslugcombo.element' ]) . '</a></span>';

		$this->slug_tease->inner_html = $tease;

		$rc = parent::render_inner_html();

		$nid = $this[self::T_NODEID];

		if ($nid)
		{
			$node = $this->app->models['nodes'][$nid];

			if ($node && $node->url && $node->url[0] != '#')
			{
				$url = $node->url;
				$url_label = \ICanBoogie\shorten($url, 64);

				$rc .= <<<EOT
<p class="small light"><strong>URL&nbsp;:</strong>$url_label</p>
EOT;
			}
		}

		return $rc;
	}
}
