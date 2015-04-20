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

use ICanBoogie\Event;
use ICanBoogie\Modules;
use ICanBoogie\Operation\BeforeProcessEvent;

use Brickrouge\Element;
use Brickrouge\Form;

use Icybee\ManageBlock;
use Icybee\Modules\Nodes\Node;
use Icybee\Modules\Nodes\ManageBlock as NodeManageBlock;
use Icybee\Modules\Nodes\SaveOperation as NodeSaveOperation;

class Hooks
{
	/**
	 * Alters system.nodes module and submodules edit block with I18n options, allowing the user
	 * to select a language for the node and a native source target.
	 *
	 * Only the native target selector is added if the `language` property is defined in the
	 * HIDDENS array, indicating that the language is already set and cannot be modified by the
	 * user.
	 *
	 * The I18n options are not added if the following conditions are met:
	 *
	 * - The working site has no native target
	 * - The "i18n" module is disabled
	 * - Only one language is used by all the sites available.
	 * - The `language` property is defined in the CHILDREN array but is empty, indicating that
	 * the language is irrelevant for the node.
	 *
	 * @param \Icybee\EditBlock\AlterChildrenEvent $event
	 */
	static public function on_nodes_editblock_alter_children(\Icybee\EditBlock\AlterChildrenEvent $event, \Icybee\Modules\Nodes\EditBlock $block)
	{
		$app = \ICanBoogie\app();

		$site = $app->site;

		if (!$site->nativeid || !isset($app->modules['i18n']))
		{
			return;
		}

		$module = $event->module;
		$languages = $module->model->where('language != ""')->count('language');

		if (0 && !count($languages)/* || current($languages) == $core->site->language*/)
		{
			return;
		}

		$children = &$event->children;
//var_dump($children);
		if (array_key_exists(Node::LANGUAGE, $children) && !$children[Node::LANGUAGE])
		{
			return;
		}

		$event->attributes[Element::GROUPS]['i18n'] = [

			'title' => 'i18n',
			'weight' => 100

		];

		if (!array_key_exists(Node::LANGUAGE, $event->attributes[Form::HIDDENS]))
		{
			$children[Node::LANGUAGE] = new NodeLanguageElement([

				Element::GROUP => 'i18n',
				'data-native-language' => $site->native->language,
				'data-site-language' => $site->language

			]);
		}

		$children[Node::NATIVEID] = new NodeNativeElement([

			Element::GROUP => 'i18n',
			NodeNativeElement::CONSTRUCTOR => $module->id

		]);
	}

	static public function on_nodes_manageblock_register_columns(ManageBlock\RegisterColumnsEvent $event, NodeManageBlock $target)
	{
		$event->add(new NodeManageBlock\TranslationsColumn($target, 't10s'), 'after:title');
	}

	static public function before_node_save_operation(BeforeProcessEvent $event, NodeSaveOperation $target)
	{
		if ($event->request[Node::LANGUAGE] !== null)
		{
			return;
		}

		$event->request[Node::LANGUAGE] = $target->app->site->language;
	}
}
