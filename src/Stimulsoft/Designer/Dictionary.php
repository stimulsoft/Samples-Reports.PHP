<?php

namespace Stimulsoft\Designer;

class Dictionary extends \Stimulsoft\OptionsBase
{
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
