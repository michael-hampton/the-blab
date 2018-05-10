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
    public function getAllEvents (User $objUser, $searchText = null, $pageNo = null, $pageLimit = null)
    {

        $sql = "SELECT e.*,
               SUM(IF(status = 1, 1, 0)) AS going,
                SUM(IF(status = 2, 1, 0)) AS not_going,
                SUM(IF(status = 3, 1, 0)) AS interested,
                SUM(IF(status = 4, 1, 0)) AS pending
                FROM event e
                LEFT JOIN event_member em ON em.event_id = e.event_id
                 WHERE e.event_id NOT IN (SELECT event_id FROM event_member WHERE user_id = :userId) AND LOWER(event_type) = 'public' ";
        $arrWhere[':userId'] = $objUser->getId ();


        if ( $searchText !== null )
        {
            $sql .=" AND event_name LIKE :searchText";
            $arrWhere[':searchText'] = '%' . $searchText . '%';
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

            if ( isset ($result['going']) )
            {
                $objEvent->setGoingCount ($result['going']);
            }

            if ( isset ($result['not_going']) )
            {
                $objEvent->setNotGoingCount ($result['not_going']);
            }

            if ( isset ($result['interested']) )
            {
                $objEvent->setInterestedCount ($result['interested']);
            }

            if ( isset ($result['pending']) )
            {
                $objEvent->setPendingCount ($result['pending']);
            }

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
        $results = $this->db->_query ("SELECT e.*,
                                         SUM(IF(status = 1, 1, 0)) AS going,
                                        SUM(IF(status = 2, 1, 0)) AS not_going,
                                        SUM(IF(status = 3, 1, 0)) AS interested,
                                        SUM(IF(status = 4, 1, 0)) AS pending
                                        FROM event e
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
