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

// LaSalle Software class
use Lasallesoftware\Novabackend\Nova\Fields\BaseTextField;

// Laravel facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * Class UpdatedBy
 *
 * Although the actual updated_by field is of type number (the mysql field type is INT), Nova offers features specific
 * to the text type, that are not available with the number field type. So, I am using the text field type.
 *
 * @package Lasallesoftware\Novabackend\Nova\Fields
 */
class UpdatedBy extends BaseTextField
{
    /**
     * The field's vue component.
     *
     * @var string
     */
    public $component = 'text-field';

    /**
     * Create a new custom text field for updated_by.
     *
     * @param  string $name
     * @param  string|null $attribute
     * @param  mixed|null $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->name = $this->name = __('lasallesoftwarelibrarybackend::general.field_name_updated_by');

        $this->nullable();

        $this->withMeta(['type' => 'number']);

        $this->formatTheValueForTheFormWeAreOn($this->identifyForm());

        $this->specifyShowOnForms();

        $this->setReadOnlyAttribute(true);
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
        $this->showOnCreation = false;
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
                return $value;
            };

        }

        // if we are creating a new record
        if  ($formType == "creation") {
            // not applicable
        }

        // if we are on the detail (show) form
        if ($formType == "detail") {

            return $this->resolveCallback = function ($value) {

                if (! isset($value)) {
                    return "—";
                }

                $user = DB::table('personbydomains')->where('id', $value )->first();
                return $user->person_first_name
                    . ' ' .  $user->person_surname
                    . ' (' . $value . ')'
                ;
            };

        }

        // if we are on the update (edit) form
        if ($formType == "update") {

            return $this->withMeta([
                "value" => Auth::id()
            ]);

        }
    }
}
