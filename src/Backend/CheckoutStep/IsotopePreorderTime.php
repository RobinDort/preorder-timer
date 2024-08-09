<?php
namespace RobinDort\PreorderTimer\Backend\CheckoutStep;

use Contao\FrontendTemplate;
use Contao\Input;
use Isotope\Isotope;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\CheckoutStep\CheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Module\Checkout;

class IsotopePreorderTime extends CheckoutStep implements IsotopeCheckoutStep {

    protected $Template;

    public function __construct(Checkout $objModule) {
        parent::__construct($objModule);

        $this->Template = new FrontendTemplate('iso_checkout_preorder_time');
    }


    /**
     * {@inheritdoc}.
     */
    public function isAvailable(): bool {
        return TRUE;
    }

    /**
     * {@inheritdoc}.
     */
    public function isSkippable(): bool {
        return FALSE;
    }


    public function generate() {
        $strClass = $GLOBALS['TL_FFL']['preorder_formular'];

        /** @var \Contao\FormText $objWidget */
        $objWidget = new $strClass([
            'id'            => $this->getStepClass(),
            'name'          => $this->getStepClass(),
            'mandatory'     => FALSE,
            'value'         => [
                'date' => Isotope::getCart()->preorder_time ? date('d.m.Y', Isotope::getCart()->preorder_time) : '',
                'time' => Isotope::getCart()->preorder_time ? date('H:i', Isotope::getCart()->preorder_time) : ''
            ],
            'storeValues'   => TRUE,
            'tableless'     => TRUE,
            'rgxp'          => 'date', // This ensures the date format is validated
        ]);

        $dateValue = "";
        $timeValue = "";

        if (Input::post('FORM_SUBMIT') == $this->objModule->getFormId()) {
            $dateValue = Input::post("date-input");
            $timeValue = Input::post("time-input");

            $objWidget->validate();

            if (!$objWidget->hasErrors()) {

                $combinedValue = $dateValue . ' ' . $timeValue;

                $date = \DateTime::createFromFormat('d.m.Y H:i', $combinedValue);
                Isotope::getCart()->preorder_time = $date ? $date->getTimestamp() : null;
                Isotope::getCart()->save();
                $this->addNoteToOrder();
            } else {
                \System::log("errors " . $objWidget->getErrorsAsString(), __METHOD__, TL_ERROR);
            }
        }

        $this->Template->headline = $GLOBALS['TL_LANG']['MSC']['preorder_time'];
        $this->Template->message = $GLOBALS['TL_LANG']['MSC']['preorder_time_message'];
        $this->Template->form = $objWidget->parse();

        return $this->Template->parse();
    }

    public function review(): array {

        var_dump(Isotope::getCart()->preorder_time);

        return [
            'preorder_time' => [
                'headline' => $GLOBALS['TL_LANG']['MSC']['preorder_time'],
                'info'     => Isotope::getCart()->preorder_time ? date('d.m.Y H:i', Isotope::getCart()->preorder_time) : 'Keine Vorbestellung',
                'note'     => '',
                'edit'     => Checkout::generateUrlForStep('preorder_time'),
            ],
        ];
    }

    public function getNotificationTokens(IsotopeProductCollection $objCollection): array {
        return[];
    }

    private function addNoteToOrder(): void {
        $preorderTime = Isotope::getCart()->preorder_time;
        $draftOrder = Isotope::getCart()->getDraftOrder();
        if (!empty($preorderTime) && $draftOrder instanceof IsotopeProductCollection) {
            $draftOrder->preorder_time = $preorderTime;
        }
    }
}

?>