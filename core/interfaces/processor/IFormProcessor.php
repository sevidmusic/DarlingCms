<?php
/**
 * Created by Sevi Dolnnelly Foreman.
 * Date: 5/26/18
 * Time: 10:24 PM
 */

namespace DarlingCms\interfaces\processor;

use DarlingCms\interfaces\html\IHtmlForm;

/**
 * Interface IFormProcessor. Defines the basic contract of an object that processes a form.
 * @package DarlingCms\interfaces\processor
 */
interface IFormProcessor
{
    /**
     * Determines if the form to be processed was submitted.
     * @return bool True if the form to be processed was submitted, false otherwise.
     */
    public function formSubmitted(): bool;

    /**
     * Returns an array of submitted values, or an empty array if there are no submitted values.
     * @return array An array of submitted values, or an empty array if there are no submitted values.
     */
    public function getSubmittedValues(): array;

    /**
     * Processes the form.
     * @return bool True if form was processed successfully, false otherwise.
     */
    public function processForm(): bool;

    /**
     * Returns an array of the values that were successfully processed, or an empty array if
     * no values were processed.
     * @return array An array of the values that were successfully processed, or an empty array
     *               if no values were processed.
     */
    public function getProcessedValues(): array;

    /**
     * Returns the IHtmlForm implementation instance processed by this IFormProcessor implementation.
     * @return IHtmlForm The IHtmlForm implementation instance processed by this IFormProcessor implementation.
     */
    public function getForm(): IHtmlForm;
}
