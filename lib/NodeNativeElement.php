<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\I18n;

use ICanBoogie\I18n;

use Brickrouge\Element;
use Brickrouge\Form;

use Icybee\Modules\Pages\Blueprint;
use Icybee\Modules\Pages\Model as PagesModel;
use Icybee\Modules\Sites\Site;

/**
 * An element to select the language of a node.
 *
 * @property-read \Icybee\Modules\Pages\Model $pages_model
 * @property-read \Icybee\Modules\Sites\Site $native_site
 */
class NodeNativeElement extends Element
{
	const CONSTRUCTOR = '#node-native-constructor';

	protected function get_pages_model()
	{
		return $this->app->models['pages'];
	}

	protected function get_native_site()
	{
		return $this->app->site->native;
	}

	public function __construct(array $attributes = [])
	{
		$site = $this->app->site;
		$native = $this->native_site->language;

		parent::__construct('select', $attributes + [

			Form::LABEL => 'nativeid',
			Element::GROUP => 'i18n',
			Element::DESCRIPTION => $this->t('nativeid', [

				'native' => $native,
				'language' => $site->language

			], [ 'scope' => 'element.description' ])

		]);
	}

	protected function render_inner_html_for_select()
	{
		$native_site = $this->native_site;
		$constructor = $this[self::CONSTRUCTOR];
		$options = $constructor == 'pages'
			? $this->create_options_for_pages($native_site, $constructor)
			: $this->create_options_for_nodes($native_site, $constructor);

		$this[self::OPTIONS] = [ null => $this->t('none', [], [ 'scope' => 'option' ]) ] + $options;

		return parent::render_inner_html_for_select();
	}

	private function create_options_for_pages(Site $native_site)
	{
		$options = [];

		$query = $this->pages_model
		->select('nid, parentid, title')
		->filter_by_siteid($native_site->siteid)
		->ordered;

		$blueprint = Blueprint::from($query);

		foreach ($blueprint->ordered_nodes as $node)
		{
			$options[$node->nid] = str_repeat("\xC2\xA0", $node->depth * 4) . \ICanBoogie\shorten($node->title);
		}

		return $options;
	}

	private function create_options_for_nodes(Site $native_site, $constructor)
	{
		$options = $this->app->models['nodes']
		->select('nid, title')
		->filter_by_constructor_and_language($constructor, $native_site->language)
		->order('title')
		->pairs;

		foreach ($options as &$label)
		{
			$label = \ICanBoogie\shorten($label);
		}

		return $options;
	}
}
