<?php

namespace Stimulsoft\Events;

use Stimulsoft\StiEmailSettings;
use Stimulsoft\StiHandler;

class StiEmailEventArgs extends StiExportEventArgs
{

### Properties

    /** @var StiEmailSettings Settings for sending the exported report by Email. */
    public $settings;


### Helpers

    protected function setSettings($value)
    {
        if ($value !== null) {
            $this->settings = new StiEmailSettings();

            foreach ($value as $name => $argsValue) {
                if ($name == 'email')
                    $name = 'to';

                if (property_exists($this->settings, $name))
                    $this->settings->$name = $argsValue;
            }

            if (StiHandler::$legacyMode)
                $this->emailSettings = $this->settings;
        }
    }
}