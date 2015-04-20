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

use Brickrouge\Document;
use Brickrouge\Element;
use Brickrouge\Form;

/**
 * An element to select the language of a node.
 */
class NodeLanguageElement extends Element
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->js->add(DIR . 'public/admin.js');
	}

	public function __construct(array $attributes = [])
	{
		parent::__construct('select', $attributes + [

			Form::LABEL => 'language',
			Element::DESCRIPTION => 'language',
			Element::OPTIONS => [

				null => $this->t('neutral', [], [ 'scope' => 'option' ])

			]

			+ $this->collect_options()

		]);
	}

	protected function collect_options()
	{
		$app = $this->app;
		$languages = $app->models['sites']->count('language');
		$locale_languages = $app->locale['languages'];

		foreach (array_keys($languages) as $language)
		{
			$languages[$language] = \ICanBoogie\capitalize($locale_languages[$language]);
		}

		return $languages;
	}
}
