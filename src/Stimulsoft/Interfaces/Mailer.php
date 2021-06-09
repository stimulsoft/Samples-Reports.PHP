<?php

namespace Stimulsoft\Interfaces;

interface Mailer
{
	/*
	public $Body;

	public $CharSet;

	public $From;

	public $FromName;

	public $Host;

	public $Password;

	public $Port;

	public $SMTPAuth;

	public $SMTPSecure;

	public $Subject;

	public $Username;
	 */

	public function AddAddress($address, $name = '');

	public function AddAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment');

	public function addBCC($address, $name = '');

	public function addCC($address, $name = '');

	public function clearBCCs();

	public function clearCCs();

	public function IsHTML($boolean);

	public function IsSMTP();

	public function Send();
}
