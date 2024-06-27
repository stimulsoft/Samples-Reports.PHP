<?php

namespace Stimulsoft\Export\Enums;

/**
 * Enumeration describes possible user access privileges to the pdf document.
 * User access privileges are managed by the user password.
 * Owner with the correct owner password has all possible privileges for the content of the pdf document and a rule for setting document permissions.
 */
class StiUserAccessPrivileges
{
    /** User password allows only opening the pdf document, decrypt it, and display it on the screen. */
    const None = 0;

    /** User password allows opening the pdf document, decrypt it, display it on the screen and print its content. */
    const PrintDocument = 1;

    /** User password allows modifying the content of the pdf document. */
    const ModifyContents = 2;

    /** User password allows copying text and graphics objects from the content of the pdf document. */
    const CopyTextAndGraphics = 4;

    /** User password allows adding or modifying text annotations in the content of the pdf document. */
    const AddOrModifyTextAnnotations = 8;

    /** User password allows all modifications on the content of the pdf document. */
    const All = 15;
}