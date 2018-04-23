<?php
/**
 * Class Album
 */
class Album
{
    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $description;

    /**
     * @var
     */
    private $name;
    
    /**
     *
     * @var type 
     */
    private $userId;


    /**
     *
     * @var type 
     */
    private $db;

    /**
     * Album constructor.
     */
    public function __construct($id)
    {
        $this->id = $id;
        
        $this->db = new Database();
        $this->db->connect();
                    
        if($this->populateObject() === false) {
            throw new Exception("Failed to populate object");
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    private function populateObject()
    {
        $result = $this->db->_select("album", "id = :id", [":id" => $this->id]);

        if($result === false) {
            trigger_error("Db query failed", E_USER_WARNING);
            return false;
        }
        
        $this->name = $result[0]['name'];
        $this->description = $result[0]['description'];
        $this->userId = $result[0]['user_id'];
    }
}