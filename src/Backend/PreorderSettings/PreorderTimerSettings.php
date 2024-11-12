<?php

namespace RobinDort\PreorderTimer\Backend\PreorderSettings;

use RobinDort\PreorderTimer\Backend\Validation\PreorderStatusInteractor;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\Input;
use Contao\Database;
use Contao\Message;


class PreorderTimerSettings extends BackendModule {

    protected $strTemplate = "be_preorder_settings";

    private $specialClosedDays;


    public function __construct()
	{
		parent::__construct();

         // Get the already saved special closed days
        $preorderStatusInteractor = new PreorderStatusInteractor();
        $this->specialClosedDays = $preorderStatusInteractor->extractSpecialClosedDays();
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
            $preorderStatus = Input::post('preorder_status');
            $time = time();

            // Save the entered date into the new table
            Database::getInstance()->prepare("INSERT INTO tl_preorder_settings (tstamp, shop_closed_date, shop_closed_status) VALUES (?,?,?)")
                                   ->execute($time, $preorderDate, $preorderStatus);

            // Add a success flash message
            Message::addInfo(
                'Datum wurde erfolgreich gesichert.'
            );
        }

        $this->Template = new BackendTemplate($this->strTemplate);
        $this->Template->specialClosedDays =  $this->specialClosedDays;
        $this->compile();

        return $this->Template->parse();
    }
}
?>  