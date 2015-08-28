<?php

return [

	'dashboard.title' => [

		'system-nodes-now' => "D'un coup d'oeil",
		'system-nodes-user-modified' => "Vos dernières modifications"

	],

	'element.title' => [

		'is_online' => "Inclure ou exclure l'enregistrement du site"

	],

	'element.description' => [

		'is_online' => "Seuls les enregistrements publiés sont disponibles pour les visiteurs.
		Cependant, les enregistrements non publiés peuvent être disponibles pour les utilisateurs
		qui en ont l'autorisation.",

		'slug' => "Le <q>slug</q> est la version du titre utilisable dans les URL. Écrit en
		minuscules, il ne contient que lettres non accentuées, chiffres et traits d'union. S'il
		est vide lors de l'enregistrement, le <q>slug</q> est automatiquement créé à partir du
		titre.",

		'siteid' => "Parce que vous en avez la permission, vous pouvez choisir le site
		d'appartenance pour l'enregistrement. Un enregistrement appartenant à un site en hérite la
		langue et n'est visible que sur ce site.",

		'user' => "Parce que vous en avez la permission, vous pouvez choisir l'utilisateur
		propriétaire de cet enregistrement."

	],

	'group.legend' => [

		'Admin' => 'Administration',
		'Advanced' => 'Options avancées',
		'Visibility' => 'Visibilité'

	],

	'label' => [

		'is_online' => 'Publié',
		'siteid' => "Site d'appartenance",
		'title' => 'Titre',
		'user' => 'Utilisateur'

	],

	'nodes.manage.column' => [

		'constructor' => 'Constructeur',
		'uid' => 'Utilisateur',
		'title' => 'Titre',
		'translations' => 'Traductions'

	],

	'module_category.other' => 'Autre',
	'module_title.nodes' => 'Nœuds',

	'offline.operation' => [

		'title' => "Dépublier des enregistrements",
		'short_title' => "Dépublier",
		'continue' => "Dépublier",
		'cancel' => "Ne pas dépublier",

		'confirm' => [

			'one' => "Êtes-vous sûr de vouloir dépublier l'enregistrement sélectionné ?",
			'other' => "Êtes-vous sûr de vouloir dépublier les :count enregistrements sélectionnés ?"

		]

	],

	'online.operation' => [

		'title' => "Publier des enregistrements",
		'short_title' => "Publier",
		'continue' => "Publier",
		'cancel' => "Ne pas publier",

		'confirm' => [

			'one' => "Êtes-vous sûr de vouloir publier l'enregistrement sélectionné ?",
			'other' => "Êtes-vous sûr de vouloir publier les :count enregistrements sélectionnés ?"

		]

	],

	'change_user.operation' => [

		'title' => "Changer l'utilisateur d'enregistrements",
		'short_title' => "Changer utilisateur",
		'continue' => "Changer",
		'cancel' => "Ne pas changer",

		'confirm' => [

			'one' => "Êtes-vous sûr de vouloir changer l'utilisaeur de l'enregistrement sélectionné ?",
			'other' => "Êtes-vous sûr de vouloir changer l'utilisateur des :count enregistrements sélectionnés ?"

		]

	],

	'option' => [

		'save_mode_display' => 'Enregistrer et afficher'

	],

	'titleslugcombo.element' => [

		'auto' => 'auto',
		'edit' => 'Cliquer pour éditer',
		'fold' => 'Cacher le champ de saisie du <q>slug</q>',
		'reset' => 'Mettre à zéro',
		'view' => 'Voir sur le site'

	],

	'permission' => [

		'modify belonging site' => "Modifier le site d'appartenance"

	],

	# operation/save

	'%title has been updated in :module.' => '%title a été mis à jour dans :module.',
	'%title has been created in :module.' => '%title a été créé dans :module.',
	"%title is now online" => "%title est maintenant publié",
	"%title is now offline" => "%title est maintenant dépublié",

	'The requested record was not found.' => "L'enregistrement demandé n'a pu être trouvé.",
	'Next: :title' => 'Suivant : :title', // il y a un espace non sécable ici
	'Previous: :title' => 'Précédent : :title', // il y a un espace non sécable ici

];
