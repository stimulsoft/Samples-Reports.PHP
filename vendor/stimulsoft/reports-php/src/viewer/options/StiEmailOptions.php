<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls the export options. */
class StiEmailOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a value which allows to display the Email dialog, or send Email with the default settings. */
    public $showEmailDialog = true;

    /** @var bool Gets or sets a value which allows to display the export dialog for Email, or export report for Email with the default settings. */
    public $showExportDialog = true;

    /** @var string Gets or sets the default email address of the message created in the viewer. */
    public $defaultEmailAddress = '';

    /** @var string Gets or sets the default subject of the message created in the viewer. */
    public $defaultEmailSubject = '';

    /** @var string Gets or sets the default text of the message created in the viewer. */
    public $defaultEmailMessage = '';


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.email';
    }
}