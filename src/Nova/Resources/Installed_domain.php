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
use Lasallesoftware\Library\Authentication\Models\Personbydomain;
use Lasallesoftware\Library\Nova\Resources\BaseResource;
use Lasallesoftware\Library\Nova\Fields\LookupTitle;
use Lasallesoftware\Library\Nova\Fields\LookupDescription;
use Lasallesoftware\Library\Nova\Fields\LookupEnabled;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel classes
use Illuminate\Http\Request;

// Laravel facade
use Illuminate\Support\Facades\Auth;


/**
 * Class Installed_domain
 *
 * @package Lasallesoftware\Library\Nova\Resources\BaseResource
 */
class Installed_domain extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Library\\Profiles\\Models\\Installed_domain';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Lookup Tables';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title',
    ];


    /**
     * Determine if this resource is available for navigation.
     *
     * Only owners and super admins can see this resource in navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return Personbydomain::find(Auth::id())->IsOwner();
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('lasallesoftwarelibrary::general.resource_label_plural_installed_domains');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwarelibrary::general.resource_label_singular_installed_domains');
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

            LookupTitle::make('title')
                ->creationRules('unique:installed_domains,title')
                ->updateRules('unique:installed_domains,title,{{resourceId}}'),

            LookupDescription::make('description'),

            LookupEnabled::make('enabled'),


            Heading::make( __('lasallesoftwarelibrary::general.field_heading_system_fields'))
                ->hideFromDetail(),

            new Panel(__('lasallesoftwarelibrary::general.panel_system_fields'), $this->systemFields()),

            //HasMany::make('Category', 'category', 'Lasallesoftware\Blogbackend\Nova\Resources\Category'),
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
     * https://nova.laravel.com/docs/1.0/resources/authorization.html#relatable-filtering
     *
     *
     *   ==> SEE NOTE IN indexQuery() method below!! <==
     *
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        // for owners, display all the installed_domains
        if (Personbydomain::find(Auth::id())->IsOwner()) {
            return $query;
        }

        // otherwise, display only the installed domain that that user belongs
        return $query->where('id', Personbydomain::find(Auth::id())->installed_domain_id);
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
     *   * Only owners get to see the index listing
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
        /** ****************************************************
         *                 SPECIAL NOTE!!
         *  ****************************************************
         *
         *     indexQuery() regulates relatableQuery()
         *
         * So, if indexQuery() says "no records", relatableQuery() lists no records, regardless of what the
         * relatableQuery() method figures out on its own.
         *
         * So... have a hacky workaround here, at least it is just 2 lines.
         *
         * My workaround is: if the form is not the "installed_domains" resource, then return the full index listing.
         *                   otherwise, do the usual index listing restrictions for "installed_domains".
         *
         */

        $explodeCurrentUrl = explode('/', url()->current());
        if (array_key_exists(5, $explodeCurrentUrl)) {
            if ($explodeCurrentUrl[5] != "installed_domains") return $query;
        };

        // owners see all installed domains
        if (auth()->user()->hasRole('owner')) {
            return $query;
        }

        // super admins & admins are not allowed to see installed domains
        return $query->where('id', '=',0);
    }
}
