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
 * @copyright  (c) 2019-2022 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 *
 * @see       https://lasallesoftware.ca
 * @see       https://packagist.org/packages/lasallesoftware/ls-novabackend-pkg
 * @see       https://github.com/LaSalleSoftware/ls-novabackend-pkg
 */

namespace Lasallesoftware\Novabackend\Nova\Resources;

// LaSalle Software classes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;

// Laravel Nova classes
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain;
use Lasallesoftware\Novabackend\Nova\Fields\LookupDescription;

// Laravel classes
use Lasallesoftware\Novabackend\Nova\Fields\LookupEnabled;

// Laravel facade
use Lasallesoftware\Novabackend\Nova\Fields\LookupTitle;

/**
 * Class Installed_domain.
 */
class Installed_domain extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Librarybackend\\Profiles\\Models\\Installed_domain';

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
        return __('lasallesoftwarelibrarybackend::general.resource_label_plural_installed_domains');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwarelibrarybackend::general.resource_label_singular_installed_domains');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            LookupTitle::make('title')
                ->creationRules('unique:installed_domains,title')
                ->updateRules('unique:installed_domains,title,{{resourceId}}'),

            LookupDescription::make('description'),

            LookupEnabled::make('enabled'),

            HasMany::make('Personbydomain')->singularLabel('Personbydomain'),

            HasMany::make(
                __('lasallesoftwarelibrarybackend::general.resource_label_plural_installed_domains_jwt_keys'),
                'installed_domains_jwt_key',
                'Lasallesoftware\Novabackend\Nova\Resources\Installed_domains_jwt_key'
            )
                ->singularLabel(__('lasallesoftwarelibrarybackend::general.resource_label_singular_installed_domains_jwt_keys')),

            //HasMany::make('Category', 'category', 'Lasallesoftware\Blogbackend\Nova\Resources\Category'),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_system_fields'), $this->systemFields()),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        /** ****************************************************
         *                 SPECIAL NOTE!!
         *  ****************************************************.
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
         */
        $explodeCurrentUrl = explode('/', url()->current());
        if (array_key_exists(5, $explodeCurrentUrl)) {
            if ('installed_domains' != $explodeCurrentUrl[5]) {
                return $query;
            }
        }

        // owners see all installed domains
        if (auth()->user()->hasRole('owner')) {
            return $query;
        }

        // super admins & admins allowed to see their installed domains only, and where applicable (especially posts),
        // assign resources to their installed domains only.
        return $query->where('id', Personbydomain::find(Auth::id())->installed_domain_id);
    }
}
