<?php

namespace NSWDPC\SearchForms\Forms;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\TextField;

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
