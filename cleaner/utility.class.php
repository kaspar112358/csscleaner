<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utility
 *
 * @author Kaspar
 */
class utility {
    
    
    public static function inMultiArray($needle, $haystack, $strict = false){
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && utility::inMultiArray($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }
}
