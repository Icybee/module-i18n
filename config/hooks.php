<?php

namespace Icybee\Modules\I18n;

use Icybee\Modules\Nodes\EditBlock as NodeEditBlock;
use Icybee\Modules\Nodes\ManageBlock as NodeManageBlock;
use Icybee\Modules\Nodes\SaveOperation as NodeSaveOperation;

$hooks = Hooks::class . '::';

return [

	'events' => [

		NodeEditBlock::class . '::alter_children' => $hooks . 'on_nodes_editblock_alter_children',
		NodeManageBlock::class . '::register_columns' => $hooks . 'on_nodes_manageblock_register_columns',
		NodeSaveOperation::class . '::process:before' => $hooks . 'before_node_save_operation'

	]

];
