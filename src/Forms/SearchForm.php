<?php

namespace NSWDPC\Search\Forms;

use SilverStripe\Forms\Form;

/**
 * Simple search form
 */
class SearchForm extends Form {

    /**
     * Helper method to return the search query value present int the URL
     */
    public function SearchQuery(): string {
        return (string)$this->getController()->getRequest()->getVar('q');
    }

}
