<?php

namespace Stimulsoft;

class EmailSettings
{
	// Email address of the sender
	public $from = null;

	// Name and surname of the sender
	public $name = null;

	// Email address of the recipient
	public $to = null;

	// Email Subject
	public $subject = null;

	// Text of the Email
	public $message = null;

	// Attached file name
	public $attachmentName = null;

	// Charset for the message
	public $charset = 'UTF-8';

	// Address of the SMTP server
	public $host = null;

	// Port of the SMTP server
	public $port = 465;

	// The secure connection prefix - ssl or tls
	public $secure = 'ssl';

	// Mailer (sendmail, qmail, smtp, mail, etc)
	public $mailer = 'smtp';

	// Login (Username or Email) */
	public $login = null;

	// Password
	public $password = null;

	// The array of 'cc' addresses.
	public $cc = array();

	// The array of 'bcc' addresses.
	public $bcc = array();
}
