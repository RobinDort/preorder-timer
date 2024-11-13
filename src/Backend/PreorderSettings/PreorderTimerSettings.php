<?php

namespace RobinDort\PreorderTimer\Backend\PreorderSettings;

use RobinDort\PreorderTimer\Backend\Validation\PreorderStatusInteractor;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\Input;
use Contao\Database;
use Contao\Message;
use Contao\Controller;
use Contao\Environment;


class PreorderTimerSettings extends BackendModule {

    protected $strTemplate = "be_preorder_settings";

    private $specialClosedDays;
    private $preorderStatusInteractor;


    public function __construct()
	{
		parent::__construct();

        // Get the already saved special closed days
        $this->preorderStatusInteractor = new PreorderStatusInteractor();
        $this->specialClosedDays = $this->preorderStatusInteractor->extractSpecialClosedDays();
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

            try {

                $response = $this->preorderStatusInteractor->insertSpecialClosedDay($time,$preorderDate,$preorderStatus);
                if ($response["success"] === true) {

                    // Add a success flash message
                    Message::addInfo(
                        $response["message"]
                    );

                    // Redirect to the same page to refresh the form and prevent resubmission
                    Controller::reload();
                } else {
                    Message::addError($response["message"]);
                }

                
                
           } catch (\Exception $e) {
               \System::log($e->getMessage(),__METHOD__,"TL_ERROR");
           }
        }

        $this->Template = new BackendTemplate($this->strTemplate);
        $this->Template->specialClosedDays =  $this->specialClosedDays;
        $this->compile();

        return $this->Template->parse();
    }
}
?>  