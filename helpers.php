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
 * Note: This helper is patchable.
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
