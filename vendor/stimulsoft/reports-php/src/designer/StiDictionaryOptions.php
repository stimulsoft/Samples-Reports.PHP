<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the dictionary. */
class StiDictionaryOptions extends StiComponentOptions
{
    protected $enums = [
        'useAliases', 'newReportDictionary', 'dataSourcesPermissions', 'dataConnectionsPermissions', 'dataColumnsPermissions',
        'dataRelationsPermissions', 'businessObjectsPermissions', 'variablesPermissions', 'resourcesPermissions'
    ];

    /** Gets or sets a visibility of the other category in the new connection form. */
    public $showAdaptersInNewConnectionForm = true;

    /** Gets or sets a visibility of the dictionary in the designer. */
    public $showDictionary = true;

    /** Gets or sets a value which indicates that using aliases in the dictionary.*/
    public $useAliases = StiUseAliases::Auto;

    /** Gets or sets a visibility of the Properties item in the dictionary context menu. */
    public $showDictionaryContextMenuProperties = true;

    /** Gets or sets a value which indicates what to do with the dictionary when creating a new report in the designer.*/
    public $newReportDictionary = StiNewReportDictionary::Auto;

    /** Gets or sets a value of permissions for datasources in the designer. */
    public $dataSourcesPermissions = StiDesignerPermissions::All;

    /** Gets or sets a value of connections for datasources in the designer. */
    public $dataConnectionsPermissions = StiDesignerPermissions::All;

    /** Gets or sets a value of connections for columns in the designer. */
    public $dataColumnsPermissions = StiDesignerPermissions::All;

    /** Gets or sets a value of connections for relations in the designer. */
    public $dataRelationsPermissions = StiDesignerPermissions::All;

    /** Gets or sets a value of connections for business objects in the designer. */
    public $businessObjectsPermissions = StiDesignerPermissions::All;

    /** Gets or sets a value of connections for variables in the designer. */
    public $variablesPermissions = StiDesignerPermissions::All;

    /** Gets or sets a value of connections for resources in the designer. */
    public $resourcesPermissions = StiDesignerPermissions::All;
}