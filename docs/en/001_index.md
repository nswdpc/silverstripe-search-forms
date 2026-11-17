# Documentation

## Adding a search provider

Search modules that implement this module can register a global search provider via project/module YML configuration:

```yml
---
Name: myapp-globalsearch
After:
  - '#nswdpc-search-forms-providers'
---
NSWDPC\Search\Forms\Models\SiteSearchChoice:
  global_search_providers:
      MySearchProvider: Some\Search\ProviderModel
```

When configured, provided `ProviderModel` exists and implements `SilverStripe\View\TemplateGlobalProvider`, the class will appear as a selection in the Search tab of site settings.

`ProviderModel` should return a form created from `SiteSearchChoice::getGlobalSearchForm()` as a global template variable that can be used globally on a website e.g. `{$SiteWideSearchForm}`:

```php
<?php
namespace Some\Search;

use NSWDPC\Search\Forms\Forms\SearchForm;
use NSWDPC\Search\Forms\Models\SiteSearchChoice;

class ProviderModel {

    protected static ?SearchForm $global_search_form = null;

    /**
     * Provide a sitewide search form, only if this provider is the global search provider
     */
    public static function get_sitewide_search_form(): ?SearchForm
    {
        // cache the form in this property to avoid retrieving it again
        if(!is_null(self::$global_search_form)) {
            return self::$global_search_form;
        }
        if(SiteSearchChoice::getGlobalProvider() === self::class) {
            // the page that displays search results, filter/results page is up to you
            if($globalSearchPage = SearchPage::get()->filter(['IsGlobalSearch' => 1])->first()) {
                // the controller for the page
                $globalSearchPageController = SearchPageController::create($globalSearchPage);
                self::$global_search_form = SiteSearchChoice::getGlobalSearchForm($globalSearchPageController, 'mysearchtype');
                return self::$global_search_form;
            }
        }

        return null;
    }

    /**
     * Provide template global variables
     */
    public static function get_template_global_variables()
    {
        $vars = [];
        if(static::get_sitewide_search_form()) {
            $vars = [
                'SiteWideSearchForm' => 'get_sitewide_search_form'
            ];
        }
        return $vars;
    }

}
```
