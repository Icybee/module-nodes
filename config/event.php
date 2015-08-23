<?php

namespace Icybee\Modules\Nodes;

use Icybee;

$hooks = Hooks::class . '::';

return [

	Icybee\Modules\Users\DeleteOperation::class . '::process:before' => $hooks . 'before_delete_user',
	Icybee\Modules\Users\User::class . '::collect_dependencies' => $hooks . 'on_user_collect_dependencies',

];
