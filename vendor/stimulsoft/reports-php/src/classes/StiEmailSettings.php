<?php

namespace Stimulsoft;

class StiEmailSettings
{
    /** @var string Email address of the sender. */
    public $from;

    /** @var string Name and surname of the sender. */
    public $name;

    /** @var string Email address of the recipient. */
    public $to;

    /** @var string Email Subject. */
    public $subject;

    /** @var string Text of the Email. */
    public $message;

    /** @var string Attached file name. */
    public $attachmentName;

    /** @var string Charset for the message. */
    public $charset = 'UTF-8';

    /** @var string Address of the SMTP server. */
    public $host;

    /** @var int Port of the SMTP server. */
    public $port = 465;

    /** @var string The secure connection prefix - ssl or tls. */
    public $secure = 'ssl';

    /** @var string Login (Username or Email). */
    public $login;

    /** @var string Password */
    public $password;

    /** @var array The array of 'cc' addresses. */
    public $cc = [];

    /** @var array The array of 'bcc' addresses. */
    public $bcc = [];
}