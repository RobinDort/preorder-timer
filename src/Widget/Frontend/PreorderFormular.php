<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;
use Contao\Widget;
use Haste\DateTime\DateTime;

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
        $germanDateFormat = 'd.m.Y';
        $html5DateFormat = 'Y-m-d\TH:i';
    
        // Check if the value matches the German date format
        if (preg_match('~^' . Date::getRegexp($germanDateFormat) . '$~i', $varValue))
        {
            // Transform from German date format to HTML5 datetime-local format
            $date = \DateTimeImmutable::createFromFormat($germanDateFormat, $varValue);
            
            if ($date)
            {
                return $date->format($html5DateFormat);
            }
    
            // Return the original value if conversion fails
            return $varValue;
        }
    
        // If the value is already in HTML5 datetime-local format, return it as is
        if (preg_match('~^' . Date::getRegexp($html5DateFormat) . '$~i', $varValue))
        {
            return $varValue;
        }
    
        // Return the original value if no format matches
        return $varValue;
    }
}

?>