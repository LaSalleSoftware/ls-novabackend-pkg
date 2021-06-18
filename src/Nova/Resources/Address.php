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

namespace Lasallesoftware\Novabackend\Nova\Resources;

// LaSalle Software classes
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain;
use Lasallesoftware\Novabackend\Nova\Fields\AddressMaplink;
use Lasallesoftware\Novabackend\Nova\Fields\Comments;
use Lasallesoftware\Novabackend\Nova\Fields\LookupDescription;
use Lasallesoftware\Novabackend\Nova\Fields\Uuid;
use Lasallesoftware\Novabackend\Nova\Resources\BaseResource;
use Lasallesoftware\Librarybackend\Rules\AddressesUniqueRule;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Place;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel class
use Illuminate\Http\Request;

// Laravel facade
use Illuminate\Support\Facades\Auth;


/**
 * Class Address
 *
 * @package Lasallesoftware\Novabackend\Nova\Resources\BaseResource
 */
class Address extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Librarybackend\\Profiles\\Models\\Address';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Profiles';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'address_calculated';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'address_calculated',
    ];

    public static $priority = 420;


    /**
     * Determine if this resource is available for navigation.
     *
     * Only the owner role can see this resource in navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return Personbydomain::find(Auth::id())->IsOwner() || Personbydomain::find(Auth::id())->IsSuperadministrator();
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('lasallesoftwarelibrarybackend::general.resource_label_plural_addresses');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwarelibrarybackend::general.resource_label_singular_addresses');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Heading::make(__('lasallesoftwarelibrarybackend::general.field_heading_addresses_address')),

            Text::make('Title')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_max_255_chars') .'</li>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Address', 'address_calculated')
                ->sortable()
                ->onlyOnIndex(),

            Place::make('Address', 'address_line_1')
                ->state('province')
                ->sortable()
                ->hideFromIndex()
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                     </ul>'
                )
                ->rules('required', new AddressesUniqueRule),
            //Place::make('Address', 'address_line_1')->hideFromIndex()->countries(['US', 'CA']),

            Text::make('Address Line 2')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Address Line 3')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Address Line 4')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('City')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Province')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Country::make('Country')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Postal Code', 'postal_code')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Latitude', 'latitude')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make('Longitude', 'longitude')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex(),


            Heading::make(__('lasallesoftwarelibrarybackend::general.field_heading_addresses_general_info')),

            LookupDescription::make('description')
                ->hideFromIndex(),

            Comments::make('comments')
                ->hideFromIndex(),

            Image::make( __('lasallesoftwarelibrarybackend::general.field_name_featured_image'))
                ->disk(config('lasallesoftware-library.lasalle_filesystem_disk_where_images_are_stored'))
                ->disableDownload()
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->squared('true')
                ->path(config('lasallesoftware-library.image_path_for_address_nova_resource')),

            AddressMaplink::make('map_link'),

            Heading::make( __('lasallesoftwarelibrarybackend::general.field_heading_address_type'))
                ->hideFromDetail(),

            BelongsTo::make('Address Type', 'lookup_address_type', 'Lasallesoftware\Novabackend\Nova\Resources\Lookup_address_type')
                ->help('<ul>
                           <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                     </ul>'
                )
                ->rules('required')
                ->sortable(),

            BelongsToMany::make('Person')
                ->singularLabel('Person'),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_system_fields'), $this->systemFields()),

            Uuid::make('uuid'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * This method is in the Laravel\Nova\PerformsQueries trait.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        self::getRelatableQueryForThisResource($query);
    }

    /**
     * Build an "index" query for the given resource.
     *
     * Overrides Laravel\Nova\Actions\ActionResource::indexQuery
     *
     * Since Laravel's policies do *NOT* include an action for the controller's INDEX action,
     * we have to override Nova's resource indexQuery method.
     *
     * So, we are going to mimick here what the "index" policy would do.
     *
     * Only owners see the index listing.
     *
     *
     * Called from a resource's indexQuery() method.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        // owners see all posts
        if ((auth()->user()->hasRole('owner')) || (auth()->user()->hasRole('superadministrator'))) {
            return $query;
        }

        // still here -- maybe still here by entering the endpoint in the browser
        return $query->where('id', 0);
    }
}
