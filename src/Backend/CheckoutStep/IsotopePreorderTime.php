<?php
namespace RobinDort\PreorderTimer\Backend\CheckoutStep;

use Contao\FrontendTemplate;
use Contao\Input;
use Isotope\Isotope;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\CheckoutStep\CheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Module\Checkout;

use RobinDort\PreorderTimer\Backend\Validation\PreorderStatusInteractor;

class IsotopePreorderTime extends CheckoutStep implements IsotopeCheckoutStep {

    protected $Template;
    protected $blnError = false;

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
        $shopClosedSpecialDays = $this->extractSpecialClosingDays();

        /** @var \Contao\FormText $objWidget */
        $objWidget = new $strClass([
            'id'            => $this->getStepClass(),
            'name'          => $this->getStepClass(),
            'mandatory'     => true,
            'value'         => [
                'date' => Isotope::getCart()->preorder_time ? 
                (new \DateTime('@' . Isotope::getCart()->preorder_time, new \DateTimeZone('Europe/Berlin')))->format('d.m.Y') : '',
                'time' => Isotope::getCart()->preorder_time ?
                (new \DateTime('@' . Isotope::getCart()->preorder_time, new \DateTimeZone('Europe/Berlin')))->format('H:i') : ''
            ],
            'storeValues'       => TRUE,
            'tableless'         => TRUE,
            'rgxp'              => 'date', // This ensures the date format is validated
            'shippingId'        => Isotope::getCart()->shipping_id, // Pass the shipping ID
            'specialClosedDays' => $shopClosedSpecialDays, // Pass the special days on which the shop is also closed
        ]);

        $dateValue = "";
        $timeValue = "";

        if (Input::post('FORM_SUBMIT') == $this->objModule->getFormId()) {
            $dateValue = Input::post("date-input");
            $timeValue = Input::post("time-input");

            $objWidget->validate();

            if (!$objWidget->hasErrors() && $objWidget->getValidatedValue() !== null) {

                $validatedTime = $objWidget->getValidatedValue();

                Isotope::getCart()->preorder_time = $validatedTime;
                Isotope::getCart()->save();
                $this->addNoteToOrder();

            } else {
                try {
                    $this->blnError = true;
                } catch (\Exception $e) {
                    \System::log("Exception: " . $e->getErrorMessage(), __METHOD__, "TL_ERROR");
                    $objWidget->addError($e->getErrorMessage());
                }
            }
        }

        $this->Template->headline = $GLOBALS['TL_LANG']['MSC']['preorder_time'];
        $this->Template->message = $GLOBALS['TL_LANG']['MSC']['preorder_time_message'];
        $this->Template->order_deviation = $GLOBALS['TL_LANG']['MSC']['preorder_time_order_deviation'];
        $this->Template->form = $objWidget->parse();

        return $this->Template->parse();

    }

    public function review(): array {
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
            $draftOrder->save();
        }
    }

    private function extractSpecialClosingDays() {

        $preorderStatusInteractor = new PreorderStatusInteractor();
        $extractedSpecialDays = $preorderStatusInteractor->selectShopNormalClosingDays();
        
        return $extractedSpecialDays;
    }
}

?>