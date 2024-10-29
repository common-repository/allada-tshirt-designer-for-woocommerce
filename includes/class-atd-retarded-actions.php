<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ATD_retarded_actions
 *
 * @author Iso-Doss
 */
class ATD_Retarded_Actions {

    public static $code = array();

    public static function display_code() {
        foreach (self::$code as $i => $current_code) {
            echo wp_kses_post( $current_code );
            unset(self::$code[$i]);
        }
    }

}
