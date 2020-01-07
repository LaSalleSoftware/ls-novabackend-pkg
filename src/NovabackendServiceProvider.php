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

namespace Lasallesoftware\Novabackend;

// Laravel class
// https://github.com/laravel/framework/blob/5.6/src/Illuminate/Support/ServiceProvider.php
use Illuminate\Support\ServiceProvider;

// Laravel Nova class
use Laravel\Nova\Nova;


/**
 * Class NovabackendServiceProvider
 *
 * @package Lasallesoftware\Novabackend
 */
class NovabackendServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * "Within the register method, you should only bind things into the service container.
     * You should never attempt to register any event listeners, routes, or any other piece of functionality within
     * the register method. Otherwise, you may accidentally use a service that is provided by a service provider
     * which has not loaded yet."
     * (https://laravel.com/docs/5.6/providers#the-register-method(
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('lsnovabackend', function ($app) {
            return new LSNovabackend();
        });

        $this->registerNovaResources();
    }

    /**
     * Register the Nova resources for this package.
     *
     * @return void
     */
    protected function registerNovaResources()
    {
        Nova::resources([
            \Lasallesoftware\Novabackend\Nova\Resources\Address::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Company::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Email::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Login::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_address_type::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Installed_domain::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Installed_domains_jwt_key::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_email_type::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_lasallesoftware_event::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_role::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_social_type::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_telephone_type::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Lookup_website_type::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Person::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Personbydomain::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Social::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Telephone::class,
            \Lasallesoftware\Novabackend\Nova\Resources\Website::class,
        ]);
    }


    /**
     * Bootstrap any package services.
     *
     * "So, what if we need to register a view composer within our service provider?
     * This should be done within the boot method. This method is called after all other service providers
     * have been registered, meaning you have access to all other services that have been registered by the framework"
     * (https://laravel.com/docs/5.6/providers)
     *
     * @return void
     */
    public function boot()
    {
        // blank
    }
}
