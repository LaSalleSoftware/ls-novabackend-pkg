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

// Laravel facade
use Illuminate\Support\Facades\Auth;


/**
 * Class Uuid
 *
 * @package Lasallesoftware\Novabackend\Nova\Fields
 */
class Uuid extends BaseTextField
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

        $this->name = '';   // blank so that users do not see "UUID" for the hidden field that does not display
                            // in the creation & update forms

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
        $this->showOnDetail   = false;
        $this->showOnCreation = true;
        $this->showOnUpdate   = true;

        return $this;
    }

    /**
     * Format this field for the individual forms,
     *
     * @param string  $formType  The form being displayed.
     *                           From Lasallesoftware\Novabackend\Nova\Fields->identifyForm()
     * @param string  $uuid
     * @return \Closure
     */
    private function formatTheValueForTheFormWeAreOn($formType)
    {
        // if we are on the index form
        if ($formType == "index") {

            // not applicable

        }

        // if we are creating a new record
        if ($formType == "creation") {

            $this->doTheUuidStuff(7);
        }

        // if we are on the detail (show) form
        if ($formType == "detail") {

            // not applicable

        }

        // if we are on the update (edit) form
        if ($formType == "update") {

            $this->doTheUuidStuff(8);

        }
    }

    /**
     * Do the UUID stuff!
     *
     * @param  int     $lasallesoftware_event_id   The ID from the lookup_lasallesoftware_events db table
     * @return string
     */
    private function doTheUuidStuff($lasallesoftware_event_id = 6)
    {
        $uuid = $this->getUuid($lasallesoftware_event_id);

        $this->withMeta(['type'  => 'hidden']);
        $this->withMeta(['value' => $uuid]);
    }

    /**
     * Create the UUID
     *
     * @param  in     $lasallesoftware_event_id   The ID from the lookup_lasallesoftware_events db table
     * @return string
     */
    private function getUuid($lasallesoftware_event_id)
    {
        $lasallesoftware_event_id = $lasallesoftware_event_id;
        $uuidComment              = "Created by a Nova form";
        $uuidCreatedby            = Auth::id();

        $uuidComment = "from Lasallesoftware\Novabackend\Nova\Fields\Uuid";

        // https://laravel.com/docs/5.8/helpers#method-resolve
        $uuidGenerator = resolve('Lasallesoftware\Librarybackend\UniversallyUniqueIDentifiers\UuidGenerator');

        return $uuidGenerator->createUuid($lasallesoftware_event_id, $uuidComment, $uuidCreatedby);
    }
}
