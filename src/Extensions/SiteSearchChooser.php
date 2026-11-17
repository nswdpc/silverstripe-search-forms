<?php

declare(strict_types=1);

namespace NSWDPC\Search\Forms\Extensions;

use NSWDPC\Search\Forms\Models\SiteSearchChoice;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;

/**
 * Allows a admin to choose a search option for the site
 */
class SiteSearchChooser extends Extension
{

    private static array $db = [
        // a string provided Id to identify a global site-wide search provider
        'SiteSearchChoice' => 'Varchar(16)'
    ];

    /**
     * Apply the search choice field to the provider
     */
    public function updateCmsFields(&$fields)
    {
        $providers = SiteSearchChoice::getProvidersForSelection();
        if($providers === []) {
            $fields->removeByName(['SiteSearchChoice']);
        } else {
            $fields->addFieldToTab(
                'Root.Search',
                DropdownField::create(
                    'SiteSearchChoice',
                    _t(self::class . '.CHOOSE_A_SITE_SEARCH_PROVIDER', 'Choose a site-wide search provider'),
                    $providers
                )->setEmptyString(_t(self::class . '.CHOOSE_A_SITE_SEARCH_PROVIDER', 'Choose a site-wide search provider'))
            );
        }
    }

}
