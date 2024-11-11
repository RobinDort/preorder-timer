<?php

namespace RobinDort\PreorderTimer\Backend\PreorderSettings;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\Input;
use Contao\Database;
use Contao\System;


class PreorderTimerSettings extends BackendModule {

    protected $strTemplate = "be_preorder_settings";


    public function __construct()
	{
		parent::__construct();
	}


    /**
     * {@inheritDoc}
     */
    public function compile() {}

    public function generate() {
         // Validate the form submission and the CSRF token
         if (Input::post('FORM_SUBMIT') === 'tl_preorder_settings_form' && !Input::post('REQUEST_TOKEN')) {
            // CSRF Token failed or missing
            throw new Exception('Invalid CSRF Token');
        }

        if (Input::post('FORM_SUBMIT') === 'tl_preorder_settings_form') {
            $preorderDate = Input::post('preorder_date_shop_closed');

            // Save the entered date into the new table
            Database::getInstance()->prepare("INSERT INTO tl_preorder_settings (shop_closed_date) VALUES (?)")
                                   ->execute($preorderDate);

            // Add a success flash message
            System::setFlashMessage(
                'Datum wurde erfolgreich gesichert.'
            );
        }

        $this->Template = new BackendTemplate($this->strTemplate);
        $this->compile();

        return $this->Template->parse();
    }
}
?>  