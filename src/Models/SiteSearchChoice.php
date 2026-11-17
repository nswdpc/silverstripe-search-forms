<?php

declare(strict_types=1);

namespace NSWDPC\Search\Forms\Models;

use NSWDPC\Search\Forms\Forms\SearchForm;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Configure and choose a site-wide (global) search provider
 */
class SiteSearchChoice
{
    use Configurable;

    // configured global search providers
    private static array $global_search_providers = [];

    // cached string class name of global provider
    protected static string $_cache_global_provider = '';

    /**
     * Returns all known configured providers
     * The global search provider classes must exist and implement SilverStripe\View\TemplateGlobalProvider
     */
    public static function getProviders(): array
    {
        $providers = self::config()->get('global_search_providers');
        if (!is_array($providers)) {
            return [];
        }
        return array_filter(
            $providers,
            fn ($providerClass, $providerId): bool => is_string($providerClass) && class_exists($providerClass) && is_subclass_of($providerClass, TemplateGlobalProvider::class),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Returns all known configured providers in a format for selection in a form
     * The key is the provider id, the value is the provider class
     */
    public static function getProvidersForSelection(): array
    {
        $providers = self::getProviders();
        $selection = [];
        foreach ($providers as $providerId => $providerClass) {
            $label = self::getProviderLabel($providerId);
            $selection[$providerId] = $label !== '' ? $label : $providerClass;
        }

        return $selection;
    }

    /**
     * Get the class of a provider, given a providerId
     * Search modules that use this module should declare their global search provider class
     * Returns the classname of the chosen provider or empty string if not found/not valid
     */
    public static function getProvider(string $providerId): string
    {
        $providers = self::getProviders();
        return trim($providers[$providerId] ?? '');
    }

    /**
     * Get the configured provider label
     */
    public static function getProviderLabel(string $providerId): string
    {
        $providerClass = self::getProvider($providerId);
        if ($providerClass !== '') {
            return Config::inst()->get($providerClass, 'global_search_label') ?? '';
        }

        return '';
    }

    /**
     * Return the provider chosen in SiteConfig, provided it is a recognised provider
     * Returns the classname of the chosen provider or empty string if not found/not valid
     */
    public static function getGlobalProvider(): string
    {
        if (self::$_cache_global_provider !== '') {
            // return if already determined
            return self::$_cache_global_provider;
        }

        $config = SiteConfig::current_site_config();
        $chosen = trim($config->SiteSearchChoice ?? '');
        if ($chosen === '') {
            return '';
        }
        self::$_cache_global_provider = self::getProvider($chosen);
        return self::$_cache_global_provider;
    }

    /**
     * Return a global search form
     * These can be created without a session/request context. A form can only be returned
     * if a request is active as that holds session (e.g not flush for global template variables)
     */
    public static function getGlobalSearchForm(Controller $controller, string $type, string $name = 'GlobalSearchForm'): ?SearchForm
    {
        if (Injector::inst()->has(HTTPRequest::class)) {
            $controller->setRequest(Injector::inst()->get(HTTPRequest::class));
            return SearchForm::create(
                $controller,
                $name
            )->setFormMethod('GET')
            ->setFormAction($controller->Link())
            ->setAttribute('data-type', Convert::raw2htmlatt($type))
            ->disableSecurityToken();
        }

        return null;
    }

}
