<?php

namespace Stimulsoft\Designer;

class Dictionary extends \Stimulsoft\OptionsBase
{
	// permission constants, | together
	const PERMISSION_NONE = 0;
	const PERMISSION_CREATE = 1;
	const PERMISSION_DELETE = 2;
	const PERMISSION_MODIFY = 4;
	const PERMISSION_VIEW = 8;
	const PERMISSION_MODIFYVIEW = 12;
	const PERMISSION_ALL = 15;

	protected static $validFields = array(
		'businessObjectsPermissions' => 'integer',
		'dataColumnsPermissions' => 'integer',
		'dataConnectionsPermissions' => 'integer',
		'dataRelationsPermissions' => 'integer',
		'dataSourcesPermissions' => 'integer',
		'resourcesPermissions' => 'integer',
		'showAdaptersInNewConnectionForm' => 'boolean',
		'showDictionary' => 'boolean',
		'variablesPermissions' => 'integer',
	);

	protected static $defaults = array(
		'businessObjectsPermissions' => 15,
		'dataColumnsPermissions' => 15,
		'dataConnectionsPermissions' => 15,
		'dataRelationsPermissions' => 15,
		'dataSourcesPermissions' => 15,
		'resourcesPermissions' => 15,
		'showAdaptersInNewConnectionForm' => true,
		'showDictionary' => true,
		'variablesPermissions' => 15,
	);
}
