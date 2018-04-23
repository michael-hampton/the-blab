<?php

class VideoFactory
{

    private $db;

    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

    /**
     * 
     * @param User $objUser
     * @param type $url
     * @return \Video|boolean
     */
    public function createVideoForUser (User $objUser, $url)
    {
        $result = $this->db->create ("video", ["video_url" => $url, "user_id" => $objUser->getId ()]);

        if ( $result === false )
        {
            return false;
        }

        return new Video ($result);
    }

}
