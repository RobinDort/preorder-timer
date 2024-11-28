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

    private $normalShopClosingDays;


    public function __construct()
	{
		parent::__construct();

        // Get the already saved special closed days
        $preorderStatusInteractor = new PreorderStatusInteractor();
        $this->normalShopClosingDays = $preorderStatusInteractor->selectShopNormalClosingDays();
	}


    /**
     * {@inheritDoc}
     */
    public function compile() {}

    public function generate() {
        $this->Template = new BackendTemplate($this->strTemplate);
        $this->Template->normalShopClosingDays = $this->normalShopClosingDays;
        $this->compile();

        return $this->Template->parse();
    }
}
?>  