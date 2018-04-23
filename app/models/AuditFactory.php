<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuditFactory
 *
 * @author michael.hampton
 */
class AuditFactory
{

    /**
     *
     * @var type 
     */
    private $objDb;

    /**
     * 
     */
    public function __construct ()
    {
        $this->objDb = new Database();
        $this->objDb->connect ();
    }

    /**
     * 
     * @param type $type
     * @param type $id
     * @return \Audit|boolean
     */
    public function getAudits ($type, $id)
    {
        $arrResults = $this->objDb->_select ("audit_history", "source_id = :id AND audit_type = :type", [":id" => $id, ":type" => $type]);

        if ( $arrResults === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrAudits = [];

        foreach ($arrResults as $arrResult) {
            $objAudit = new Audit();
            $objAudit->setAfter ($arrResult['after_value']);
            $objAudit->setBefore ($arrResult['before_value']);
            $objAudit->setDateCreated ($arrResult['date_created']);
            $objAudit->setId ($arrResult['id']);
            $objAudit->setSourceId ($arrResult['source_id']);
            $objAudit->setUserId ($arrResult['user_id']);
            $objAudit->setAuditType ($arrResult['audit_type']);

            $arrAudits[] = $objAudit;
        }

        return $arrAudits;
    }

    /**
     * 
     * @param User $objUser
     * @param type $before
     * @param type $after
     * @param type $type
     * @param type $id
     * @return boolean
     */
    public function createAudit (User $objUser, $before, $after, $type, $id)
    {
        $result = $this->objDb->create ("audit_history", [
            "user_id" => $objUser->getId (),
            "audit_type" => $type,
            "source_id" => $id,
            "before_value" => $before,
            "after_value" => $after,
            "date_created" => date ("Y-m-d H:i:s")
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
