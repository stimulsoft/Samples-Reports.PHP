<?php

namespace Stimulsoft\Events;

use Stimulsoft\StiComponent;
use Stimulsoft\StiFunctions;
use Stimulsoft\StiHandler;

class StiReportEventArgs extends StiEventArgs
{

### Properties

    /** @var object The current report JSON object with the set of all properties. */
    public $report;

    /** @var string The name of the report file to save. */
    public $fileName;

    /** @var bool A flag indicating that the wizard was used when creating the report. */
    public $isWizardUsed;

    /** @var bool A flag indicating that the report was saved automatically. */
    public $autoSave;

    /**
     * @var string The current report object as a JSON string.
     * @deprecated Please use the '$args->getReportJson()' method.
     */
    public $reportJson;

    /**
     * @var array|null Predefined data object for building the report. Please use the 'regReportData()' method to set it.
     */
    public $data;


### Helpers

    protected function getHandler()
    {
        if ($this->sender instanceof StiHandler)
            return $this->sender;

        if ($this->sender instanceof StiComponent)
            return $this->sender->handler;

        return null;
    }

    protected function setProperty($name, $value)
    {
        parent::setProperty($name, $value);

        if ($name == 'report') {
            $this->report = clone $value;

            if (StiHandler::$legacyMode)
                $this->reportJson = $this->getReportJson();
        }
    }

    /** @return string|bool Report as a JSON string, or false if the conversion is unsuccessful. */
    public function getReportJson()
    {
        return json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /** @param string $value Report as a JSON string. */
    public function setReportJson(string $value): bool
    {
        $report = json_decode($value);
        if ($report !== null) {
            $this->report = $report;
            return true;
        }

        return false;
    }

    /**
     * Sets the data that will be passed to the report generator before building the report.
     * It can be an XML or JSON string, as well as an array or a data object that will be serialized into a JSON string.
     * @param string $name The name of the data source in the report.
     * @param mixed|string|array $data Report data as a string, array, or object.
     * @param bool $synchronize If true, data synchronization will be called after the data is registered.
     */
    public function regReportData(string $name, $data, $synchronize = false) {
        $stringData = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!StiFunctions::isNullOrEmpty($stringData))
            $this->data = [
                "name" => $name,
                "data" => $stringData,
                "synchronize" => $synchronize
            ];
    }
}