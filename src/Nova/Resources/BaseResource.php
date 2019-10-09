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
 * @copyright  (c) 2019 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/lsv2-novabackend-pkg
 * @link       https://github.com/LaSalleSoftware/lsv2-novabackend-pkg
 *
 */

namespace Lasallesoftware\Novabackend\Nova\Resources;

// LaSalle Software classes
use Lasallesoftware\Novabackend\Nova\Fields\CreatedAt;
use Lasallesoftware\Novabackend\Nova\Fields\CreatedBy;
use Lasallesoftware\Novabackend\Nova\Fields\UpdatedAt;
use Lasallesoftware\Novabackend\Nova\Fields\UpdatedBy;

// Nova class
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;


/**
 * Class BaseResource
 *
 * @package Lasallesoftware\Novabackend\Nova\Resources\BaseResource
 */
abstract class BaseResource extends Resource
{
    /**
     * Display the system panel
     *
     * return array
     */
    public function systemPanel()
    {
        return [

            // Nova v2.0.8 now displays panels on the create and update views in addition to the detail view.
            // So, now that there is a separate panel for this, I should remove my own heading.

            //Heading::make( __('lasallesoftwarelibrary::general.field_heading_system_fields')),

            new Panel(__('lasallesoftwarelibrary::general.panel_system_fields'), $this->systemFields()),
        ];
    }

    /**
     * Get the featured image fields for this resource.
     *
     * @return array
     */
    public function featuredimageFields()
    {
        return [
            Image::make( __('lasallesoftwarelibrary::general.field_name_featured_image_upload'))
                ->disableDownload()
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex()
            ,

            Textarea::make(__('lasallesoftwarelibrary::general.field_name_featured_image_code'))
                ->alwaysShow()
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_optional') .'</li>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_featured_image_code1') .'</li>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_featured_image_code2') .'</li>
                     </ul>'
                )
                ->hideFromIndex()
            ,

            Text::make(__('lasallesoftwarelibrary::general.field_name_featured_image_external'))
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_optional') .'</li>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_featured_image_external1') .'</li>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_featured_image_external2') .'</li>
                         <li>'. __('lasallesoftwarelibrary::general.field_help_featured_image_external3') .'</li>
                     </ul>'
                )
                ->hideFromIndex()
            ,
        ];
    }

    /**
     * Get the system fields for this resource.
     *
     * @return array
     */
    public function systemFields()
    {
        return [
            CreatedAt::make('created_at'),
            CreatedBy::make('created_by'),

            UpdatedAt::make('updated_at'),
            UpdatedBy::make('updated_by'),
        ];
    }

    /**
     * Get the relatable query for a resource.
     *
     * This method produces the query used to populate the drop-downs for profile tables related to the
     * person and company tables.
     *
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getRelatableQueryForThisResource($query)
    {
        // segment the url by explode-ing
        $currentUrl        = url()->current();
        $explodeCurrentUrl = explode('/', $currentUrl);

        // isolate the url segment
        $count   = count($explodeCurrentUrl);
        $segment = $explodeCurrentUrl[$count - 4];

        // is the profiles dropdown for the person db table, or for the company db table?
        if ($segment == 'people') {
            return $query->whereDoesntHave('person', function(){
                return;
            });
        }

        if ($segment == 'companies') {
            return $query->whereDoesntHave('company', function(){
                return;
            });
        }

        return $query;
    }
}
