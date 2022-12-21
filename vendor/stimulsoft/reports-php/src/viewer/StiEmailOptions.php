<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponentOptions;

/** A class which controls the export options. */
class StiEmailOptions extends StiComponentOptions
{
    /** Gets or sets a value which allows to display the Email dialog, or send Email with the default settings. */
    public $showEmailDialog = true;

    /** Gets or sets a value which allows to display the export dialog for Email, or export report for Email with the default settings. */
    public $showExportDialog = true;

    /** Gets or sets the default email address of the message created in the viewer. */
    public $defaultEmailAddress = '';

    /** Gets or sets the default subject of the message created in the viewer. */
    public $defaultEmailSubject = '';

    /** Gets or sets the default text of the message created in the viewer. */
    public $defaultEmailMessage = '';
}