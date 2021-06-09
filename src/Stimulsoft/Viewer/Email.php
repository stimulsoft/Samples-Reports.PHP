<?php

namespace Stimulsoft\Viewer;

class Email extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'defaultEmailAddress' => 'string',
		'defaultEmailMessage' => 'string',
		'defaultEmailSubject' => 'string',
		'showEmailDialog' => 'boolean',
		'showExportDialog' => 'boolean',
	);

	protected static $defaults = array(
		'defaultEmailAddress' => '',
		'defaultEmailMessage' => '',
		'defaultEmailSubject' => '',
		'showEmailDialog' => true,
		'showExportDialog' => true,
	);
}
