<?php

/**
 * This file is part of the Lasalle Software Nova back-end package.
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
 *
 * @see       https://lasallesoftware.ca
 * @see       https://packagist.org/packages/lasallesoftware/lsv2-novabackend-pkg
 * @see       https://github.com/LaSalleSoftware/lsv2-novabackend-pkg
 */

namespace Lasallesoftware\Novabackend\Nova\Fields;

// LaSalle Software class

/**
 * Class LookupTitle.
 *
 * Designed specifically for use with lookup tables
 */
class LookupTitleInstalleddomain extends BaseTextField
{
    /**
     * Create a new custom text field for title.
     *
     * @param string      $name
     * @param null|string $attribute
     * @param null|mixed  $resolveCallback
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->name = __('lasallesoftwarelibrary::general.field_name_lookup_title');

        $this->help(
            '<ul>
                         <li>'.__('lasallesoftwarelibrary::general.field_help_lookup_name').'</li>
                         <li>'.__('lasallesoftwarelibrary::general.field_help_brief').'</li>
                         <li>'.__('lasallesoftwarelibrary::general.field_help_max_255_chars').'</li>
                         <li>'.__('lasallesoftwarelibrary::general.field_help_required').'</li>
                         <li>'.__('lasallesoftwarelibrary::general.field_help_unique').'</li>
                     </ul>'
        );

        $this->sanitize();

        $this->specifyShowOnForms();

        $this->sortable();

        $this->rules('required', 'max:255');
    }

    /**
     * This field will display, or not display, on these forms.
     *
     * @return $this
     */
    private function specifyShowOnForms()
    {
        $this->showOnIndex = true;
        $this->showOnDetail = true;
        $this->showOnCreation = true;
        $this->showOnUpdate = true;

        return $this;
    }

    /**
     * Sanitize data.
     *
     * @return closure
     */
    private function sanitize()
    {
        return $this->resolveCallback = function ($value) {
            //return trim(ucwords($value));
            return trim($value);
        };
    }
}