<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostInterface
 *
 * @author michael.hampton
 */
interface PostInterface
{

    /**
     * 
     * @param type $comment
     * @param User $objUser
     * @param \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter
     * @param array $imageIds
     */
    public function createComment ($comment, User $objUser, \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, array $imageIds = null);
    
    public function getComments(User $objUser);
}
