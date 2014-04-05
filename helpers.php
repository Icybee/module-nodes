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

/**
 * Converts a string into a slug.
 *
 * A slug is the part of a URL which identifies a page using human-readable keywords. It is
 * common practice to make the slug all lowercase, accented characters are usually replaced
 * by letters from the English alphabet, punctuation marks are generally removed, and long
 * page titles may also be truncated to keep the final URL to a reasonable length.
 *
 * @param string $str The string to convert into a slug.
 * @param string $language The language of the string.
 *
 * @return string
 *
 * @see http://en.wikipedia.org/wiki/Clean_URL
 */
function slugize($str, $language=null)
{
	return Helpers::slugize($str, $language);
}

class Helpers
{
	static private $jumptable = [

		'slugize' => [ __CLASS__, 'slugize' ]

	];

	/**
	 * Calls the callback of a patchable function.
	 *
	 * @param string $name Name of the function.
	 * @param array $arguments Arguments.
	 *
	 * @return mixed
	 */
	static public function __callstatic($name, array $arguments)
	{
		return call_user_func_array(self::$jumptable[$name], $arguments);
	}

	/**
	 * Patches a patchable function.
	 *
	 * @param string $name Name of the function.
	 * @param collable $callback Callback.
	 *
	 * @throws \RuntimeException is attempt to patch an undefined function.
	 */
	static public function patch($name, $callback)
	{
		if (empty(self::$jumptable[$name]))
		{
			throw new \RuntimeException("Undefined patchable: $name.");
		}

		self::$jumptable[$name] = $callback;
	}

	/*
	 * Default implementations
	 */

	static private function slugize($str, $language=null)
	{
		return \ICanBoogie\normalize($str);
	}
}