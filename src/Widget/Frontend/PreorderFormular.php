<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;
use Contao\Widget;
use Haste\DateTime\DateTime;
use Contao\Date;

class PreorderFormular extends Widget {
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'iso_checkout_preorder_time_formular';
    protected $strPrefix = 'widget widget-preorder-formular';

    public function generate(): string
    {
        // Not actually used
        return '';
    }


    protected function validator($varInput) {
        
        if ($this->rgxp === "date") {
    
            // Define the formats
            $germanDateTimeFormat = 'd.m.Y H:i';
            $html5DateTimeFormat = 'Y-m-d\TH:i';
        
            // Check and handle the German datetime format
            $date = \DateTime::createFromFormat($germanDateTimeFormat, $varInput);
        
            if ($date && $date->format($germanDateTimeFormat) === $varInput) {
                // Convert to HTML5 datetime-local format
                $varInput = $date->format($html5DateTimeFormat);
            } else {
                // Validate the input as HTML5 datetime-local format
                $date = \DateTime::createFromFormat($html5DateTimeFormat, $varInput);
                if (!$date || $date->format($html5DateTimeFormat) !== $varInput) {
                    $this->addError('Please enter a valid date and time in the correct format.');
                }
            }
        
            // Call parent validator
            return parent::validator($varInput);
            
        }
    }

    /**
	 * Convert date values into the HTML5 date format
	 */
    protected function convertDate($varValue)
    {
        if (!$varValue || $this->rgxp != 'date')
        {
            return $varValue;
        }
    
        // Define the German date format
        $germanDateFormat = 'd.m.Y H:i';
        $html5DateFormat = 'Y-m-d\TH:i';

        $date = \DateTimeImmutable::createFromFormat($germanDateFormat, $varValue);
    
        if ($date && $date->format($germanDateFormat) === $varValue) {
            // Transform from German date format to HTML5 datetime-local format
            return $date->format($html5DateFormat);
        }
    
        // Check if the value matches the HTML5 datetime-local format (manual regex)
        $html5DateRegex = '~^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$~';
        if (preg_match($html5DateRegex, $varValue)) {
            return $varValue;
        }
    
        // Return the original value if no format matches
        return $varValue;
    }
}

?>