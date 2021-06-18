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
 * @copyright  (c) 2019-2021 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-novabackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-novabackend-pkg
 *
 */

namespace Lasallesoftware\Novabackend\Nova\Fields;

// Laravel Nova class
use Laravel\Nova\Fields\Field;


/**
 * Class BaseField
 *
 * Nova fields extend Laravel\Nova\Fields\Field. For my custom fields, I also have base fields for
 * my custom text fields, my custom datetime fields, etc. These base fields for my custom text fields (etc)
 * extend this class, so ultimately all my custom fields do extend Laravel\Nova\Fields\Field.
 *
 * @package Lasallesoftware\Novabackend\Nova\Fields
 */
class BaseField extends Field
{
    /**
     * The edit form url ends with "../{resourceName}/{resourceId}/update-fields".
     * So, to check if we are on the edit form, check if the URL ends with "update-fields".
     *
     * @return bool
     */
    public function isEditForm()
    {
        return substr(Url()->current(), -strlen('update-fields')) === 'update-fields';
    }

    /**
     * What form are we on?
     *
     * These are the four forms, with their ending url segments:
     *
     *  index form    = /{resourceName}
     *  creation form = /{resourceName}/creation-fields
     *  detail form   = /{resourceName}/{resourceId}
     *  update form   = /{resourceName}/update-fields
     *
     * If we are on this form.... then this method returns...
     *  index                      "index"
     *  creation                   "creation"
     *  detail                     "detail"
     *  update                     "update"
     *
     * @return mixed
     */
    public function identifyForm()
    {
        // segment the url by explode-ing
        $currentUrl = url()->current();
        $explodeCurrentUrl = explode('/', $currentUrl);

        // grab the last segment
        $count = count($explodeCurrentUrl);
        $lastUrlSegment = $explodeCurrentUrl[$count-1];

        // return the form that we are on
        if ($lastUrlSegment == "creation-fields") {
            return "creation";
        }

        if ($lastUrlSegment == "update-fields") {
            return "update";
        }

        if (is_numeric($lastUrlSegment)) {
            return "detail";
        }

        return "index";
    }
}
