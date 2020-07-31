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

namespace Lasallesoftware\Novabackend\Nova\Resources;

// LaSalle Software classes
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain;
use Lasallesoftware\Novabackend\Nova\Fields\CreatedAt;
use Lasallesoftware\Novabackend\Nova\Fields\CreatedBy;
use Lasallesoftware\Novabackend\Nova\Fields\UpdatedAt;
use Lasallesoftware\Novabackend\Nova\Fields\UpdatedBy;

// Nova class
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;

// Laravel facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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

            //Heading::make( __('lasallesoftwarelibrarybackend::general.field_heading_system_fields')),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_system_fields'), $this->systemFields()),
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
            CreatedAt::make(__('lasallesoftwarelibrarybackend::general.field_name_created_at')),
            CreatedBy::make(__('lasallesoftwarelibrarybackend::general.field_name_created_by')),

            UpdatedAt::make(__('lasallesoftwarelibrarybackend::general.field_name_updated_at')),
            UpdatedBy::make(__('lasallesoftwarelibrarybackend::general.field_name_updated_by')),
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

    /**
     * Ensure that there is a leading slash, but no trailing slash, in the path.
     *
     * @param  string  $customPath
     * @return string
     */
    public function cleanCustomImagePath($customPath)
    {
        // The path needs a leading slash
        $path = (substr($customPath, 0,1) == '/') ? $customPath : '/' . $customPath;

        // The path must not have a trailing slash
        return (substr($path, -1) == '/') ? substr($path, 0, -1) : $path;
    }

    /**
     * Get the "client_id" field of the "personbydomain_client" db table, given the 
     * "personbydomain_id" via the Auth() facade. 
     * 
     * Created for the pocast related Nova resources "public static function indexQuery(NovaRequest $request, $query)"
     * method can call this common static function. 
     * 
     * If a user belongs to the "Client" role, then they can see only podcast records belonging to their "client_id". Hence,
     * this method!
     *
     * @return int    
     */
    public static function getClientId()
    {
        return DB::table('personbydomain_client')
            ->where('personbydomain_id', Auth::id())
            ->pluck('client_id')
            ->first()
        ;
    }

    /**
     * Permission for a user with a client role
     *
     * @param  int   $personbydomain_id            The personbydomain ID
     * @return bool
     */
    public static function getNavigationPermissionForClient($personbydomain_id)
    {
        // Owner sees everything!
        if (Personbydomain::find($personbydomain_id)->IsOwner()) return true;

        // A user with a client user role, *and* has is associated with a client database table record is a thumbs up
        if ( (Personbydomain::find($personbydomain_id)->IsClient()) && (Personbydomain::find($personbydomain_id)->getClientId($personbydomain_id) > 0) ) {
            return true;
        }

        return false;
    }
}