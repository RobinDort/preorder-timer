<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;

use RobinDort\PreorderTimer\Backend\Validation\PreorderLimiter;
use Haste\DateTime\DateTime;
use Contao\Widget;
use Contao\Date;
use Contao\Input;

class PreorderFormular extends Widget {
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'iso_checkout_preorder_time_formular';
    protected $strPrefix = 'widget widget-preorder-formular';

    private $dayValidator;

    public function __construct() {
		$this->dayValidator = new DayValidator();
	}

    /**
     *  {@inheritdoc}.
     */
    public function generate(): string
    {
        // Not actually used
        return '';
    }
}

?>