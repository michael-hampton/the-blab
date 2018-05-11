<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Event
 *
 * @author michael.hampton
 */
class Event
{

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $location;

    /**
     *
     * @var type 
     */
    private $eventName;

    /**
     *
     * @var type 
     */
    private $eventDate;

    /**
     *
     * @var type 
     */
    private $eventTime;

    /**
     *
     * @var type 
     */
    private $imageLocation;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $goingCount;

    /**
     *
     * @var type 
     */
    private $pendingCount;

    /**
     *
     * @var type 
     */
    private $notGoingCount;

    /**
     *
     * @var type 
     */
    private $interestedCount;

    /**
     *
     * @var type 
     */
    private $totalCount;

    /**
     *
     * @var type 
     */
    private $eventType;

    /**
     *
     * @var type 
     */
    private $eventCategory;

    /**
     *
     * @var type 
     */
    private $objDb;

    public function __construct ($id)
    {
        $this->id = $id;

        $this->objDb = new Database();
        $this->objDb->connect ();

        if ( $this->populateObject () === false )
        {
            throw new Exception ('Unable to populate object');
        }
    }

    public function getId ()
    {
        return $this->id;
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
     * @return type
     */
    public function getLocation ()
    {
        return $this->location;
    }

    /**
     * 
     * @return type
     */
    public function getEventName ()
    {
        return $this->eventName;
    }

    /**
     * 
     * @return type
     */
    public function getEventDate ()
    {
        return $this->eventDate;
    }

    /**
     * 
     * @param type $location
     */
    public function setLocation ($location)
    {
        $this->location = $location;
    }

    /**
     * 
     * @param type $eventName
     */
    public function setEventName ($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * 
     * @param type $eventDate
     */
    public function setEventDate ($eventDate)
    {
        $this->eventDate = $eventDate;
    }

    /**
     * 
     * @return type
     */
    public function getEventTime ()
    {
        return $this->eventTime;
    }

    /**
     * 
     * @param type $eventTime
     */
    public function setEventTime ($eventTime)
    {
        $this->eventTime = $eventTime;
    }

    /**
     * 
     * @return type
     */
    public function getImageLocation ()
    {
        return $this->imageLocation;
    }

    /**
     * 
     * @param type $imageLocation
     */
    public function setImageLocation ($imageLocation)
    {
        $this->imageLocation = $imageLocation;
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
    public function getGoingCount ()
    {
        return $this->goingCount;
    }

    /**
     * 
     * @return type
     */
    public function getNotGoingCount ()
    {
        return $this->notGoingCount;
    }

    /**
     * 
     * @return type
     */
    public function getPendingCount ()
    {
        return $this->pendingCount;
    }

    /**
     * 
     * @param type $pendingCount
     */
    public function setPendingCount ($pendingCount)
    {
        $this->pendingCount = $pendingCount;
    }

    /**
     * 
     * @return type
     */
    public function getInterestedCount ()
    {
        return $this->interestedCount;
    }

    /**
     * 
     * @return type
     */
    public function getTotalCount ()
    {
        return $this->totalCount;
    }

    /**
     * 
     * @param type $goingCount
     */
    public function setGoingCount ($goingCount)
    {
        $this->goingCount = $goingCount;
    }

    /**
     * 
     * @param type $notGoingCount
     */
    public function setNotGoingCount ($notGoingCount)
    {
        $this->notGoingCount = $notGoingCount;
    }

    /**
     * 
     * @param type $interestedCount
     */
    public function setInterestedCount ($interestedCount)
    {
        $this->interestedCount = $interestedCount;
    }

    /**
     * 
     * @param type $totalCount
     */
    public function setTotalCount ($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    /**
     * 
     * @return type
     */
    public function getEventType ()
    {
        return $this->eventType;
    }

    /**
     * 
     * @return type
     */
    public function getEventCategory ()
    {
        return $this->eventCategory;
    }

    /**
     * 
     * @param type $eventType
     */
    public function setEventType ($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * 
     * @param type $eventCategory
     */
    public function setEventCategory ($eventCategory)
    {
        $this->eventCategory = $eventCategory;
    }

    /**
     * 
     * @param type $imageLocation
     * @return boolean
     */
    public function updateEventImage ($imageLocation)
    {
        $result = $this->objDb->update ("event", ["image_location" => $imageLocation], "event_id = :eventId", [":eventId" => $this->id]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @var array 
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

    /**
     * 
     * @return boolean
     */
    private function validate ()
    {
        if ( trim ($this->eventName) === "" )
        {
            $this->validationFailures[] = "Event name cannot be empty";
        }

        if ( trim ($this->eventType) === "" )
        {
            $this->validationFailures[] = "Event type cannot be empty";
        }

        if ( trim ($this->eventDate) === "" )
        {
            $this->validationFailures[] = "Event date cannot be empty";
        }

        if ( trim ($this->location) === "" )
        {
            $this->validationFailures[] = "Event location cannot be empty";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function save ()
    {
        if ( $this->validate () === false )
        {
            return false;
        }

        $result = $this->objDb->update ("event", ["location" => $this->location, "event_date" => $this->eventDate, "event_name" => $this->eventName, "event_time" => $this->eventTime, "event_type" => $this->eventType, "event_category" => $this->eventCategory], "event_id = :eventId", [":eventId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("db query failed to update event", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function populateObject ()
    {

        $result = $this->objDb->_query ("select e.*,
                                        count(*) total,
                                        sum(case when m.status = 1 then 1 else 0 end) going,
                                        sum(case when m.status = 2 then 1 else 0 end) notGoing,
                                        sum(case when m.status = 3 then 1 else 0 end) interested,
                                        sum(case when m.status = 4 then 1 else 0 end) pending
                                    from event e
                                    INNER JOIN event_member m ON m.event_id = e.event_id
                                    where e.event_id = :eventId", [":eventId" => $this->id]);


        if ( $result === false || !isset ($result[0]) )
        {
            return false;
        }

        $this->setEventDate ($result[0]['event_date']);
        $this->setEventName ($result[0]['event_name']);
        $this->setEventTime ($result[0]['event_time']);
        $this->setLocation ($result[0]['location']);
        $this->setImageLocation ($result[0]['image_location']);
        $this->setUserId ($result[0]['user_id']);
        $this->notGoingCount = $result[0]['notGoing'];
        $this->goingCount = $result[0]['going'];
        $this->interestedCount = $result[0]['interested'];
        $this->totalCount = $result[0]['total'];
        $this->pendingCount = $result[0]['pending'];
        $this->eventType = $result[0]['event_type'];
        $this->eventCategory = $result[0]['event_category'];

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function deleteEvent ()
    {
        $result = $this->objDb->delete ("event", "event_id = :eventId", [":eventId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
