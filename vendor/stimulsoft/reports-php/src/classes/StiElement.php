<?php

namespace Stimulsoft;

class StiElement
{

### Properties

    /** @var string Gets or sets the component or element ID that will be used for the name of the object when preparing JavaScript code. */
    public $id;

    /** @var bool */
    public $htmlRendered = false;


### HTML

    /**
     * Gets the HTML representation of the component or element.
     * @return string Prepared HTML and JavaScript code for embedding in an HTML template.
     */
    public function getHtml(): string
    {
        $this->htmlRendered = true;
        return '';
    }

    /**
     * Outputs the HTML representation of the component or element.
     */
    public function renderHtml()
    {
        echo $this->getHtml();
    }
}