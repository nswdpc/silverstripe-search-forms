<?php

namespace NSWDPC\Search\Forms\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\Form;

/**
 * Simple search form
 */
class SearchForm extends Form {

    /**
     * Helper method to return the search query value present int the URL
     */
    public function SearchQuery(): string {
        $controller = Controller::curr();
        $q = $controller->getRequest()->getVar('q');
        if(!is_scalar($q)) {
            $q = '';
        } else {
            $q = trim(strip_tags($q));
        }
        return $q;
    }

}
