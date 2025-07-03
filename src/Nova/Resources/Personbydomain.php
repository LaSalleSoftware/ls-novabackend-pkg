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

namespace Lasallesoftware\Novabackend\Nova\Resources;

// LaSalle Software classes
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain as Personbydomainmodel;
use Lasallesoftware\Librarybackend\Rules\PersonbydomainsCannotbanselfRule;
use Lasallesoftware\Novabackend\Nova\Fields\Uuid;
use Lasallesoftware\Novabackend\Nova\Resources\BaseResource;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\PasswordConfirmation;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel class
use Illuminate\Http\Request;

// Laravel facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * Class Personbydomain
 *
 * @package Lasallesoftware\Novabackend\Nova\Resources\BaseResource
 */
class Personbydomain extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Librarybackend\\Authentication\\Models\\Personbydomain';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Auth';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name_calculated';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'person_first_name', 'email', 'installed_domain_title',
    ];

    /**
     * Determine if this resource is available for navigation.
     *
     * All roles can see this resource in navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return Personbydomainmodel::find(Auth::id())->IsOwner() || 
            Personbydomainmodel::find(Auth::id())->IsSuperadministrator() ||
            Personbydomainmodel::find(Auth::id())->IsAdministrator()
        ;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:personbydomains,email')
                ->updateRules('unique:personbydomains,email,{{resourceId}}')
                ->help(
                    __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_email_preamble1') .
                    '<ul>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_email_preamble2') .'</li>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_email_see_website') .'</li>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_email_full') .'</li>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_email_not_new') .'</li>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_email_unique') .'</li>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                     </ul>')
            ,

            DateTime::make(__('lasallesoftwarelibrarybackend::general.field_name_email_verified_at'), 'email_verified_at')
                ->nullable()
                ->onlyOnDetail()
                // ->format('MMMM DD YYYY, hh:mm a')
            ,

            Password::make(__('lasallesoftwarelibrarybackend::general.field_name_password'), 'password')
                //->help('<ul>
                //           <li>'.__('lasallesoftwarelibrarybackend::general.field_help_required').'</li>
                //     </ul>'
                //)
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6', 'confirmed')
                ->updateRules('nullable', 'string', 'min:6,{{resourceId}}', 'confirmed')
            ,

            PasswordConfirmation::make(__('lasallesoftwarelibrarybackend::general.field_name_passwordconfirmation'))
                ->creationRules('required')
            ,

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_domain_fields'), $this->domainFields()),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_persons_fields'), $this->personsFields()),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_banned_fields'), $this->bannedFields()),

            new Panel('Owner Only Fields', $this->ownersOnlyFields()),
            
            new Panel('Owner and Superadmin Only Fields', $this->ownersAndSuperadminsOnlyFields()),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_system_fields'), $this->systemFields()),

            Uuid::make('uuid'),
        ];
    }

    
    /**
     * Get the persons fields for the resource.
     *
     * @return array
     */
    protected function personsFields()
    {
        return [
            BelongsTo::make(__('lasallesoftwarelibrarybackend::general.field_name_person'), 'person', 'Lasallesoftware\Novabackend\Nova\Resources\Person')
                ->help('<ul>
                            <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_person_associate') .'</li>
                            <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_person_setup') .'</li>
                            <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_person_createlink') .'</li>
                            <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_person_searchbox') .'</li>
                            <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_person_reminder') .'</li>
                            <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                     </ul>'
                )
                ->searchable()
                ->hideWhenUpdating()
                ->rules('required')
            ,

            Number::make(__('lasallesoftwarelibrarybackend::general.field_name_person_id'), 'person_id')
                ->readonly()
                ->hideFromIndex()
                ->hideWhenCreating()
            ,

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_first_name'), 'person_first_name')
                ->readonly()
                ->hideFromIndex()
                ->hideWhenCreating()
            ,

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_surname'), 'person_surname')
                ->readonly()
                ->hideFromIndex()
                ->hideWhenCreating()
            ,
        ];
    }

    /**
     * Get the domain fields for the resource.
     *
     * @return array
     */
    protected function domainFields()
    {
        return [

            BelongsTo::make(
                __('lasallesoftwarelibrarybackend::general.field_name_domain_name'),
                'installed_domain',
                'Lasallesoftware\Novabackend\Nova\Resources\Installed_domain')
                ->help('<ul>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_personbydomain_domain_message') .'</li>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                 </ul>'
                )
                ->hideFromIndex()
                ->hideWhenUpdating()
                ->hideFromDetail()
                ->rules('required')
            ,

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_domain_id'), 'installed_domain_id')
                ->readonly()
                ->hideFromIndex()
                ->hideWhenCreating()
            ,

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_domain_name'), 'installed_domain_title')
                ->readonly()
                ->hideWhenCreating()
            ,
        ];
    }

    /**
     * Get the banned fields for the resource.
     *
     * @return array
     */
    protected function bannedFields()
    {
        return [

            Boolean::make(__('lasallesoftwarelibrarybackend::general.field_name_banned_enabled'), 'banned_enabled')
                ->rules('required', new PersonbydomainsCannotbanselfRule)
            ,

            DateTime::make(__('lasallesoftwarelibrarybackend::general.field_name_banned_date'), 'banned_at')
                ->nullable()
                ->onlyOnDetail()
                // ->format('MMMM DD YYYY, hh:mm a')
                ->help('<ul>
                        <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                    </ul>'
                )
            ,

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_banned_comments'), 'banned_comments')
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_max_255_chars') .'</li>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->hideFromIndex()
            ,
        ];
    }

    /**
     * Get fields that are viewable by owners only
     *
     * @return array
     */
    protected function ownersOnlyFields()
    {
        if (Personbydomain::find(Auth::id())->IsOwner()) {  
            return [
                BelongsToMany::make('Client'),
            ];
        }
    }

    /**
     * Get fields that are viewable by owners and super admins only
     *
     * @return array
     */
    protected function ownersAndSuperadminsOnlyFields()
    {
        if (Personbydomain::find(Auth::id())->IsOwner() || Personbydomain::find(Auth::id())->IsSuperadministrator()) {  
            return [
                BelongsToMany::make(
                    __('lasallesoftwarelibrarybackend::general.resource_label_singular_lookup_roles'),
                    'lookup_role',
                    'Lasallesoftware\Novabackend\Nova\Resources\Lookup_role')
                ,

                BelongsToMany::make('Client'),
            ];
        }
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the lookup_role model may be attached to the personbydomains.
     *
     *                   *** NOTE THAT THE MODEL IS PLURALIZED! ***
     *
     * From https://github.com/laravel/nova-issues/issues/1131?source=post_page#issuecomment-460056467
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableLookup_roles(NovaRequest $request, $query)
    {
        // The "attach & attach again" button is a problem. I cannot suppress it (did I miss it?). Very glad to find this
        // relatable{model}() method, which resides within the resource. So I can rely less on the resource being attached's
        // relatableQuery() method from now on. So... with this method I can check for a record with the personbydomain_id,
        // and if found, prevent another record insertion by populating the drop-down with nothing.
        if (DB::table('personbydomain_lookup_roles')->where('personbydomain_id', $request->resourceId)->first()) {
            return $query->where('id', 0);
        }

        // if the user is an owner, then display all the roles
        if (Personbydomainmodel::find(Auth::id())->IsOwner()) return $query;

        // if the user is a super admin, then display the super admin and admin roles only
        if (Personbydomainmodel::find(Auth::id())->IsSuperadministrator()) return $query->where('id', 2)->orWhere('id', 3);

        // still here? then display nothing!
        return $query->where('id', 0);

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
        // owners see all records
        if (auth()->user()->hasRole('owner')) {
            return $query;
        }

        // super admins see all records belonging to their domain
        if (auth()->user()->hasRole('superadministrator')) {
            return $query
                ->where('installed_domain_id', DB::table('personbydomains')->where('id', Auth::id())->pluck('installed_domain_id')->first())
                ;
        }

        // still here -- maybe still here by entering the endpoint in the browser
        return $query->where('id', Auth::id());
    }
}