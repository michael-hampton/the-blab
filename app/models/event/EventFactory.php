<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventFactory
 *
 * @author michael.hampton
 */
class EventFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     * 
     */
    public function __construct ()
    {

        $this->db = new Database();

        $this->db->connect ();
    }

    /**
     * 
     * @param type $searchText
     * @param type $pageNo
     * @param type $pageLimit
     */
    public function getAllEvents ($searchText = null, $pageNo = null, $pageLimit = null)
    {
        $sqlWhere = "";
        $arrWhere = [];

        if ( $searchText !== null )
        {
            $sqlWhere .= " AND event_name LIKE :searchText";
            $arrWhere[':searchText'] = '%' . $searchText . '%';
        }


        $sql = "SELECT * FROM event WHERE 1=1";

        if ( $sqlWhere !== "" )
        {
            $sql .= $sqlWhere;
        }

        $sql .= " ORDER BY event_name ASC";


        if ( $pageNo !== null && $pageLimit !== null )
        {
            $sql .= " LIMIT {$pageNo}, {$pageLimit}";
        }

        $results = $this->db->_query ($sql, $arrWhere);

        return $this->loadObject ($results);
    }

    /**
     * 
     * @param User $objUser
     * @return \Event|boolean
     */
    public function getEventsForUser (User $objUser)
    {
        $results = $this->db->_select ("event", "user_id = :userId", [":userId" => $objUser->getId ()]);

        return $this->loadObject ($results);
    }

    /**
     * 
     * @param array $results
     * @return \Event|boolean
     */
    private function loadObject (array $results)
    {
        if ( $results === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrEvents = [];

        foreach ($results as $result) {
            $objEvent = new Event ($result['event_id']);
            $objEvent->setEventDate ($result['event_date']);
            $objEvent->setEventName ($result['event_name']);
            $objEvent->setLocation ($result['location']);
            $objEvent->setEventTime ($result['event_time']);

            $arrEvents[] = $objEvent;
        }

        return $arrEvents;
    }

    /**
     * 
     * @param User $objUser
     * @return \Event|boolean
     */
    public function getEventsForProfile (User $objUser)
    {
        $results = $this->db->_query ("SELECT e.* FROM event e
                                    INNER JOIN`event_member` em ON em.`event_id` = e.event_id
                                    WHERE em.`user_id` = :userId", [':userId' => $objUser->getId ()]);

        if ( $results === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrEvents = [];

        foreach ($results as $result) {
            $objEvent = new Event ($result['event_id']);
            $objEvent->setEventDate ($result['event_date']);
            $objEvent->setEventName ($result['event_name']);
            $objEvent->setLocation ($result['location']);
            $objEvent->setEventTime ($result['event_time']);

            $arrEvents[] = $objEvent;
        }

        return $arrEvents;
    }

    /**
     * 
     * @param User $objUser
     * @param type $location
     * @param type $eventDate
     * @param type $eventName
     * @param type $eventTime
     * @return \Event|boolean
     */
    public function createEvent (User $objUser, $location, $eventDate, $eventName, $eventTime)
    {
        if ( trim ($location) === "" )
        {
            $this->validationFailures[] = "Location is a mandatory field";
        }

        if ( trim ($eventDate) === "" )
        {
            $this->validationFailures[] = "Event Date is a mandatory field";
        }

        if ( trim ($eventName) === "" )
        {
            $this->validationFailures[] = "Event Name is a mandatory field";
        }

        if ( trim ($eventTime) === "" )
        {
            $this->validationFailures[] = "Event Time is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $result = $this->db->create ("event", [
            "user_id" => $objUser->getId (),
            "location" => $location,
            "event_date" => date ("Y-m-d H:i:s", strtotime ($eventDate)),
            "event_name" => $eventName,
            "event_time" => $eventTime
                ]
        );

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED - unable to create event", E_USER_WARNING);
            return false;
        }

        $objEvent = new Event ($result);

        return $objEvent;
    }

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     * 
     * @return type
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

}
