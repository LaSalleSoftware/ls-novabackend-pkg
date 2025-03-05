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
 * @copyright  (c) 2019-2025 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-novabackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-novabackend-pkg
 *
 */

namespace Lasallesoftware\Novabackend\Nova\Fields;

// LaSalle Software
use Lasallesoftware\Novabackend\Nova\Fields\BaseTextField;

// Laravel Nova classes
use Laravel\Nova\Fields\Text;

// Laravel Facade
use Illuminate\Support\Facades\Crypt;


/**
 * Class TextEncrypted
 *
 * @package Lasallesoftware\Novabackend\Nova\Fields
 */
class TextEncrypted extends BaseTextField
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'text-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

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

        $this->name = $name;

        $this->sanitize();

        $this->formatTheValueForTheFormWeAreOn($this->identifyForm());

        $this->specifyShowOnForms();
    }

    /**
     * This field will display, or not display, on these forms.
     *
     * @return $this
     */
    private function specifyShowOnForms()
    {
        $this->showOnIndex    = false;
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

            return $this->resolveCallback = function ($value) {

                // Nova 2.9+ wants this if statement. 
                if (! isset($value)) {
                    return "";
                }
                $value = Crypt::decrypt($value);

                return substr($value, 0, 50) . "...";
            };

        }

        // if we are creating a new record
        if  ($formType == "creation") {

            // not applicable

        }

        // if we are on the detail (show) form
        if ($formType == "detail") {

            return $this->resolveCallback = function ($value) {

                return Crypt::decrypt($value);
            };

        }

        // if we are on the update (edit) form
        if ($formType == "update") {

            return $this->resolveCallback = function ($value) {

                return Crypt::decrypt($value);
            };

        }
    }

    /**
     * Sanitize data
     *
     * @return closure
     */
    private function sanitize()
    {
        return $this->resolveCallback = function ($value) {
            return trim($value);
        };
    }
}