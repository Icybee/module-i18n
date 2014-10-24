<?php

namespace Icybee\Modules\I18n;

$hooks = __NAMESPACE__ . '\Hooks::';

return [

	'events' => [

		'Icybee\Modules\Nodes\EditBlock::alter_children' => $hooks . 'on_nodes_editblock_alter_children'

	]

];
