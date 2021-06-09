<?php

namespace Stimulsoft;

class Result
{
	public $success = true;

	public $notice = null;

	public $object = null;

	public static function success($notice = null, $object = null)
	{
		$result = new \Stimulsoft\Result();
		$result->success = true;
		$result->notice = $notice;
		$result->object = $object;

		return $result;
	}

	public static function error($notice = null)
	{
		$result = new \Stimulsoft\Result();
		$result->success = false;
		$result->notice = $notice;

		return $result;
	}
}
