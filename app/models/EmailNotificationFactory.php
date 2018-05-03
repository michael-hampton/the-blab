<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailNotificationFactory
 *
 * @author michael.hampton
 */
class EmailNotificationFactory
{
    
    /**
     * 
     * @param User $objUser
     * @param type $header
     * @param type $body
     * @return \EmailNotification
     */
    public function createNotification (User $objUser, $header, $body)
    {
        $objEmail = new EmailNotification ($objUser, $header, $body);

        return $objEmail;
    }

}
