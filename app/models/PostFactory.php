<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostFactory
 *
 * @author michael.hampton
 */
class PostFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $objLikes;

    /**
     *
     * @var type 
     */
    private $objUploadFactory;

    /**
     *
     * @var type 
     */
    private $objCommentFactory;

    /**
     *
     * @var type 
     */
    private $objReviewFactory;

    /**
     *
     * @var type 
     */
    private $objTagUserFactory;

    /**
     *
     * @var type 
     */
    private $objCommentReplyFactory;

    /**
     * 
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     */
    public function __construct (
    PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory
    )
    {
        $this->db = new Database();
        $this->db->connect ();

        $this->objLikes = $objPostActionFactory;
        $this->objUploadFactory = $objUploadFactory;
        $this->objCommentFactory = $objCommentFactory;
        $this->objReviewFactory = $objReviewFactory;
        $this->objTagUserFactory = $objTagUserFactory;
        $this->objCommentReplyFactory = $objCommentReplyFactory;
    }
    

    
}
