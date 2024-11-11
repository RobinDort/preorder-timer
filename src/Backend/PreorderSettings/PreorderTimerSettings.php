<?php

namespace RobinDort\PreorderTimer\Backend\PreorderSettings;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Input;
use Contao\Database;


class PreorderTimerSettings extends Backend {

    public function generate() {
        if (Input::post('FORM_SUBMIT') === 'tl_preorder_settings_form') {
            $preorderDate = Input::post('preorder_date_shop_closed');

            // Save the entered date into the new table
            Database::getInstance()->prepare("INSERT INTO tl_preorder_settings (preorder_closed_shop_date) VALUES (?)")
                                   ->execute($preorderDate);
        }

        $this->Template = new BackendTemplate('be_preorder_settings');
        return $this->Template->parse();
    }
}
?>