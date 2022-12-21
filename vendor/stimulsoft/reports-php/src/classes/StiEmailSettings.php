<?php

namespace Stimulsoft;

class StiEmailSettings
{
    /** Email address of the sender. */
    public $from;

    /** Name and surname of the sender. */
    public $name;

    /** Email address of the recipient. */
    public $to;

    /** Email Subject. */
    public $subject;

    /** Text of the Email. */
    public $message;

    /** Attached file name. */
    public $attachmentName;

    /** Charset for the message. */
    public $charset = 'UTF-8';

    /** Address of the SMTP server. */
    public $host;

    /** Port of the SMTP server. */
    public $port = 465;

    /** The secure connection prefix - ssl or tls. */
    public $secure = 'ssl';

    /** Login (Username or Email). */
    public $login;

    /** Password */
    public $password;

    /** The array of 'cc' addresses. */
    public $cc = array();

    /** The array of 'bcc' addresses. */
    public $bcc = array();
}