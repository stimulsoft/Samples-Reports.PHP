<?php

namespace Stimulsoft\Designer;

use Stimulsoft\Designer\Enums\StiDesignerPermissions;
use Stimulsoft\Designer\Enums\StiNewReportDictionary;
use Stimulsoft\Designer\Enums\StiUseAliases;
use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the dictionary. */
class StiDictionaryOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a visibility of the other category in the new connection form. */
    public $showAdaptersInNewConnectionForm = true;

    /** @var bool Gets or sets a visibility of the dictionary in the designer. */
    public $showDictionary = true;

    /** @var StiUseAliases [enum] Gets or sets a value which indicates that using aliases in the dictionary.*/
    public $useAliases = StiUseAliases::Auto;

    /** @var bool Gets or sets a visibility of the Properties item in the dictionary context menu. */
    public $showDictionaryContextMenuProperties = true;

    /** @var bool Gets or sets a visibility of the Actions in the dictionary. */
    public $showDictionaryActions = true;

    /** Gets or sets a value which indicates what to do with the dictionary when creating a new report in the designer.*/
    public $newReportDictionary = StiNewReportDictionary::Auto;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of permissions for datasources in the designer. */
    public $dataSourcesPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for data transformations in the designer. */
    public $dataTransformationsPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for datasources in the designer. */
    public $dataConnectionsPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for columns in the designer. */
    public $dataColumnsPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for relations in the designer. */
    public $dataRelationsPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for business objects in the designer. */
    public $businessObjectsPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for variables in the designer. */
    public $variablesPermissions = StiDesignerPermissions::All;

    /** @var StiDesignerPermissions [enum] Gets or sets a value of connections for resources in the designer. */
    public $resourcesPermissions = StiDesignerPermissions::All;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.dictionary';
    }
}