<?php

namespace RobinDort\PreorderTimer\Model;
use Isotope\Model\OrderStatus;

class PreorderStatus extends OrderStatus {

    /** 
     * Set the color property and name
     *
     * @param string $color
     * @param string $name
     */
    public function setProperties($color, $name)
    {
        // Set the protected color property
        $this->color = $color;

        // Set the name property
        $this->title = $name;

        $this->save();
    }
}

?>