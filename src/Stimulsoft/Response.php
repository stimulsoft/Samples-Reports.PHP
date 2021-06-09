<?php

namespace Stimulsoft;

class Response
{
	/**
	 * outputs the final result object
	 *
	 * @param
	 * @param bool $exit will stop PHP execution if tree (default)
	 *
	 */
	public static function json($result, $exit = true)
	{
		$result->object = null;
		echo \defined('JSON_UNESCAPED_SLASHES') ? \json_encode($result, JSON_UNESCAPED_SLASHES) : \json_encode($result);

		if ($exit) {
			exit;
		}
	}
}
