<?php

return [

	'element.title' => [

		'is_online' => "Include or exclude the record from the site"

	],

	'element.description' => [

		'is_online' => "Only published records are available to visitors. However, unpublished
		records may be available to users who have permission.",

		'slug' => "The <q>slug</q> is the version of the title used in URLs. Written in lowercase,
		it contains only unaccentuated letters, numbers and hyphens. If empty when saving,
		the <q>slug</q> is automatically created from the title.",

		'site_id' => "Because you have permission, you can choose the belonging site for the
		record. A record belonging to a site inherits its language and only appears on this
		site.",

		'user' => "Because you have permission, you can choose the user owner of the record."

	],

	'title' => [

		'visibility' => 'Visibility'

	],

	'label' => [

		'is_online' => 'Published',
		'site_id' => 'Belonging site',
		'slug' => 'Slug',
		'title' => 'Title',
		'user' => 'User'

	],

	'nodes.manage.column' => [

		'constructor' => 'Constructor',
		'created_at' => 'Date created',
		'date' => 'Date',
		'is_online' => 'Published',
		'title' => 'Title',
		'translations' => 'Translations',
		'uid' => 'User',
		'updated_at' => 'Date updated'

	],

	'module_category.other' => 'Other',
	'module_title.nodes' => 'Nodes',

	'offline.operation' => [

		'title' => 'Put records offline',
		'short_title' => 'Offline',
		'continue' => 'Put offline',
		'cancel' => "Don't put offline",

		'confirm' => [

			'one' => 'Are you sure you want to put the selected record offline?',
			'other' => 'Are you sure you want to put the :count selected records offline?'

		]

	],

	'online.operation' => [

		'title' => 'Put records online',
		'short_title' => 'Online',
		'continue' => 'Put online',
		'cancel' => "Don't put online",

		'confirm' => [

			'one' => 'Are you sure you want to put the selected record online?',
			'other' => 'Are you sure you want to put the :count selected records online?'

		]

	],

	'change_user.operation' => [

		'title' => "Change records user",
		'short_title' => "Change user",
		'continue' => "Change",
		'cancel' => "Don't change",

		'confirm' => [

			'one' => 'Are you sure you want to change the user of the selected record?',
			'other' => 'Are you sure you want to change the user of the :count selected records?'

		]

	],

	'option' => [

		'save_mode_display' => 'Save and display'

	],

	'titleslugcombo.element' => [

		'auto' => 'auto',
		'edit' => 'Click to edit',
		'fold' => 'Hide the <q>slug</q> input field',
		'reset' => 'Reset',
		'view' => 'View on website'

	]

];
