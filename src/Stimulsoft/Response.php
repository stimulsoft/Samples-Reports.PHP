<?php

namespace Stimulsoft;

class Response
{
	/**
	 * outputs the final result object
	 *
	 * @param mixed $result data to encode to json
	 * @param bool $exit will echo out the json and stop PHP execution if true (default)
	 *
	 * @return string of json
	 */
	public static function json($result, $exit = true)
	{
		$result->object = null;
		$json = \defined('JSON_UNESCAPED_SLASHES') ? \json_encode($result, JSON_UNESCAPED_SLASHES) : \json_encode($result);

		if ($exit) {
			echo $json;
			exit;
		}

		return $json;
	}
}
