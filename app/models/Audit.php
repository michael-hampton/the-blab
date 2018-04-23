<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Audit
 *
 * @author michael.hampton
 */
class Audit
{

    /**
     *
     * @var type 
     */
    private $dateCreated;

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $sourceId;

    /**
     *
     * @var type 
     */
    private $before;

    /**
     *
     * @var type 
     */
    private $after;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $auditType;

    /**
     * 
     * @return type
     */
    public function getDateCreated ()
    {
        return $this->dateCreated;
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * 
     * @return type
     */
    public function getSourceId ()
    {
        return $this->sourceId;
    }

    /**
     * 
     * @return type
     */
    public function getBefore ()
    {
        return $this->before;
    }

    /**
     * 
     * @return type
     */
    public function getAfter ()
    {
        return $this->after;
    }

    /**
     * 
     * @return type
     */
    public function getUserId ()
    {
        return $this->userId;
    }

    /**
     * 
     * @param type $dateCreated
     */
    public function setDateCreated ($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param type $sourceId
     */
    public function setSourceId ($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * 
     * @param type $before
     */
    public function setBefore ($before)
    {
        $this->before = $before;
    }

    /**
     * 
     * @param type $after
     */
    public function setAfter ($after)
    {
        $this->after = $after;
    }

    /**
     * 
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @return type
     */
    public function getAuditType ()
    {
        return $this->auditType;
    }

    /**
     * 
     * @param type $auditType
     */
    public function setAuditType ($auditType)
    {
        $this->auditType = $auditType;
    }

}
