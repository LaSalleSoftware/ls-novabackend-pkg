<?php

/**
 * This file is part of the Lasalle Software Nova back-end package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *
 * ==========================================================================
 *             LARAVEL's NOVA IS A COMMERCIAL PACKAGE!
 * --------------------------------------------------------------------------
 *  NOVA is a *first*-party commercial package for the Laravel Framework, made
 *  by the Laravel Project. You have to pay for it.
 *
 *  So, yes, my LaSalle Software, as FOSS as it may be, depends on a commercial
 *  OSS package.
 * ==========================================================================
 *
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2019-2020 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-novabackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-novabackend-pkg
 *
 */

namespace Lasallesoftware\Novabackend\Nova\Fields;


/**
 * Class Website
 *
 * @package Lasallesoftware\Novabackend\Nova\Fields
 */
class Website extends BaseTextField
{
    /**
     * Create a new custom text field for title.
     *
     * @param  string $name
     * @param  string|null $attribute
     * @param  mixed|null $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->name = __('lasallesoftwarelibrary::general.field_name_website');

        if ($this->identifyForm() == "creation")  {

            $this->help('<ul>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_required') .'</li>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_unique') .'</li>
                     </ul>'
            );
        }

        $this->formatTheValueForTheFormWeAreOn($this->identifyForm());

        $this->specifyShowOnForms();

        $this->sortable();

        $this->creationRules('required', 'unique:websites,url');
        $this->updateRules('required', 'unique:websites,url,{{resourceId}}');
    }

    /**
     * This field will display, or not display, on these forms.
     *
     * @return $this
     */
    private function specifyShowOnForms()
    {
        $this->showOnIndex    = true;
        $this->showOnDetail   = true;
        $this->showOnCreation = true;
        $this->showOnUpdate   = true;

        return $this;
    }

    /**
     * Format this field for the individual forms,
     *
     * @param string  $formType  The form being displayed.
     *                           From Lasallesoftware\Novabackend\Nova\Fields->identifyForm()
     * @return \Closure
     */
    private function formatTheValueForTheFormWeAreOn($formType)
    {
        // if we are on the index form
        if ($formType == "index") {

            $this->asHtml();

            return $this->resolveCallback = function ($value) {

                return '<a href="' . $value . '" target="_blank"/>' . substr($value, 0, 50) . "</a>";
            };

        }

        // if we are creating a new record
        if  ($formType == "creation") {

            // not applicable

        }

        // if we are on the detail (show) form
        if ($formType == "detail") {

            $this->asHtml();

            return $this->resolveCallback = function ($value) {

                return '<a href="' . $value . '" target="_blank"/>' . substr($value, 0, 50) . "</a>";
            };

        }

        // if we are on the update (edit) form
        if ($formType == "update") {

            //$this->setReadOnlyAttribute(true);

            $this->asHtml();

            return $this->resolveCallback = function ($value) {

                return $value;
            };

        }
    }
}
