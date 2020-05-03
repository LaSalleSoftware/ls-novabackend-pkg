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
 * @license    http://opensource.org/licenses/MIT MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/lsv2-novabackend-pkg
 * @link       https://github.com/LaSalleSoftware/lsv2-novabackend-pkg
 *
 */

// return $this->withMeta(['value' => $this->user_id ?? auth()->user()->id]);

namespace Lasallesoftware\Novabackend\Nova\Fields;

// LaSalle Software class
use Lasallesoftware\Novabackend\Nova\Fields\BaseField;

// Laravel class
use Illuminate\Support\Carbon;

/**
 * Class BaseTextField
 *
 * Common methods for my custom text fields.
 *
 * @package Lasallesoftware\Novabackend\Nova\Fields
 */
class BaseTextField extends BaseField
{
    /**
     * The field's vue component.
     *
     * @var string
     */
    public $component = 'text-field';




    /**
     * Set the value attribute.
     *
     * For text and textarea fields only.
     *
     * @param  string $value
     * @return $this
     */
    public function setValueAttribute($value)
    {
        $this->withMeta(['extraAttributes' => [
            'value' => $value
        ]]);

        return $this;
    }

    /**
     * Display the field as raw HTML using Vue.
     *
     * @return $this
     */
    public function asHtml()
    {
        return $this->withMeta(['asHtml' => true]);
    }


    /**
     * This is a text field created for a datetime field for the express purpose of making the field
     * read-only in the create and update forms. Even though this is now a new Nova feature (v2.0.1),
     * I can't quite figure it out, and I have this feature(s) nailed for myself now. So... I'm keeping this
     * stuff. This method converts the date as text to a date as an actual date in order to specify the specific
     * datetime formatting.
     *
     * @return \Closure
     */
    public function formatDatetimeTextField()
    {
        return $this->resolveCallback = function ($value) {

            if (! isset($value)) { 
                return "--";
            }

            if (($timestamp = strtotime($value)) === false) {
                return $value->format('l F dS, Y, g:i:sa');
            }

            return date('l F dS, Y, g:i:sa', $timestamp);
        };
    }

    /**
     * For new creation, and for first-time updates, populate the un-populated db datetime field
     *
     * @return mixed
     */
    public function hydrateFieldWithTheCurrentDatetime()
    {
        return $this->withMeta([
            "value" => Carbon::now(null)->format('Y-m-d H:i:s')
        ]);
    }
}
