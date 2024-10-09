<?php

namespace Stimulsoft\Enums;

class StiHtmlMode
{
    /** @var int Renders only JavaScript code to insert into JavaScript block on an HTML page. */
    const Scripts = 0;

    /** @var int Renders the full JavaScript code and the necessary HTML tags to insert into the HTML page inside the BODY. */
    const HtmlScripts = 1;

    /** @var int Renders a fully HTML page with all scripts and tags to return as a response instead of an HTML template. */
    const HtmlPage = 2;
}