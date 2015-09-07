<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Operation;

use ICanBoogie\Errors;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Module;
use ICanBoogie\Operation;

use Brickrouge\Form;
use Icybee\Binding\Core\PrototypedBindings;

class ImportOperation extends Operation
{
	use PrototypedBindings;

	protected $keys_translations = [];

	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER

		] + parent::get_controls();
	}

	protected function validate(Errors $errors)
	{
		return true;
	}

	protected function process()
	{
		$data = $this->preparse_data();
		$data = $this->parse_data($data);

		$save = Request::from([

			'path' => Operation::encode("{$this->module}/save")

		], [ $_SERVER ] );

		#
		# override form
		#

		$this->app->events->attach(function(Operation\GetFormEvent $event, SaveOperation $operation) use($save) {

			if ($event->request !== $save)
			{
				return;
			}

			$event->form = new Form;

		});

		$this->import($data, $save);

		$this->response->message = "Records were successfully imported.";

		return true;
	}

	protected function preparse_data()
	{
		$json = file_get_contents(\ICanBoogie\DOCUMENT_ROOT . 'export.json');
		$data = json_decode($json);

		if (!$data)
		{
			throw new \Exception(\ICanBoogie\format("Failed to decode JSON: !json", [ 'json' => $json ]));
		}

		return (array) $data->rc;
	}

	protected function parse_data(array $data)
	{
		$site = $this->app->site;
		$site_id = $site->siteid;
		$language = $site->language;

		$is_translating = true;

		foreach ($data as $nid => $node)
		{
			$node->siteid = $site_id;

			if ($is_translating)
			{
				$node->nativeid = $nid;
			}

			$node->language = $language;
		}

		return $data;
	}

	protected function import(array $data, Request $request)
	{
		foreach ($data as $nid => $node)
		{
			$request->params = (array) $node;

			/* @var $response \ICanBoogie\Operation\Response */

			$response = $request->post();

			$this->keys_translations[$nid] = $response->rc['key'];
		}
	}
}
