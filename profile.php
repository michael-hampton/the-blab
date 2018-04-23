<?php
if ( session_id () === "" )
{
    session_start ();
}

if ( empty ($_SESSION['user']['username']) )
{
    header ("Location: login.php");
    die;
}

if ( empty ($_GET['user']) )
{
    die ("Invalid user");
}

require_once 'models/FriendFactory.php';
require_once 'models/User.php';
require_once 'models/UserFactory.php';
require_once 'models/EventFactory.php';
require_once 'models/GroupFactory.php';

$objUserFactory = new UserFactory();
$arrUsers = $objUserFactory->getUsers ();

$objFriend = new FriendFactory();

$userId = $_SESSION['user']['user_id'];

$objUser = $arrUsers[$userId];

$arrFriendList = $objFriend->getFriendList ($objUser);

$objEventFactory = new EventFactory();

$arrEvents = $objEventFactory->getEventsForUser ($objUser);

$objGroupFactory = new GroupFactory();

$arrGroups = $objGroupFactory->getGroupsForUser ($objUser);

require_once 'models/PostFactory.php';
$objPostFactory = new PostFactory();
$arrPosts = $objPostFactory->getPostsForUser ($objUser);

$arrPosts = array_slice ($arrPosts, 0, 2);

date_default_timezone_set ("Europe/London");

function blab_time_ago ($timestamp)
{
    $time_ago = strtotime ($timestamp);
    $current_time = time ();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round ($seconds / 60);           // value 60 is seconds  
    $hours = round ($seconds / 3600);           //value 3600 is 60 minutes * 60 sec  
    $days = round ($seconds / 86400);          //86400 = 24 * 60 * 60;  
    $weeks = round ($seconds / 604800);          // 7*24*60*60;  
    $months = round ($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60  
    $years = round ($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60  
    if ( $seconds <= 60 )
    {
        return "Just Now";
    }
    else if ( $minutes <= 60 )
    {
        if ( $minutes == 1 )
        {
            return "one minute ago";
        }
        else
        {
            return "$minutes minutes ago";
        }
    }
    else if ( $hours <= 24 )
    {
        if ( $hours == 1 )
        {
            return "an hour ago";
        }
        else
        {
            return "$hours hrs ago";
        }
    }
    else if ( $days <= 7 )
    {
        if ( $days == 1 )
        {
            return "yesterday";
        }
        else
        {
            return "$days days ago";
        }
    }
    else if ( $weeks <= 4.3 ) //4.3 == 52/12  
    {
        if ( $weeks == 1 )
        {
            return "a week ago";
        }
        else
        {
            return "$weeks weeks ago";
        }
    }
    else if ( $months <= 12 )
    {
        if ( $months == 1 )
        {
            return "a month ago";
        }
        else
        {
            return "$months months ago";
        }
    }
    else
    {
        if ( $years == 1 )
        {
            return "one year ago";
        }
        else
        {
            return "$years years ago";
        }
    }
}
?>

<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="css/animate.css" rel="stylesheet">
<link href="css/newsFeed.css" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.mentions.css">
<link href="css/sweetalert.css" rel="stylesheet">
<link href="css/datetime.css" rel="stylesheet">
<link href="css/fonts.css" rel="stylesheet">
<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1COtE7cJxfGxNWiTjEgtSNhbvDBCiiqQ&callback=initMap"-->
<!--type="text/javascript"></script>-->
<link href="css/clockpicker.css" rel="stylesheet">
<link rel="stylesheet" href="css/profile.css">

<style>

    .pac-container {
        z-index: 9999 !important;
    }
    input[type="file"] {
        display: inline-block;
        position: absolute;
        margin-left: 20px;
        margin-right: 20px;
        padding-top: 30px;
        padding-bottom: 67px;
        z-index: 99;
        cursor:pointer;
    }
    .custom-file-upload {
        position:relative;
        display: inline-block;
        cursor: pointer;
        padding-top:40px;
        padding-bottom:40px;
        width:20%;
        border:1px dashed #ff5b57 !important;
        margin-left:20px;
        margin-right:20px;
        margin-top:10px;
        text-align:center;
    }

    .dropdown-a {
        display: block;
        display: inline-block;
        margin: 0px 3px;
        position: relative;
        z-index: 99;
    }


    /* ===[ End demonstration ]=== */

    .dropdown-a .dropdown_button {
        cursor: pointer;
        width: auto;
        display: inline-block;
        padding: 4px 5px;
        /*        border: 1px solid #AAA;*/
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        font-weight: bold;
        color: #717780;
        line-height: 16px;
        text-decoration: none !important;
        background: white;
    }

    .dropdown-a input[type="checkbox"]:checked +  .dropdown_button {
        border: 1px solid #3B5998;
        color: white;
        background: #6D84B4;
        -moz-border-radius-topleft: 2px;
        -moz-border-radius-topright: 2px;
        -moz-border-radius-bottomright: 0px;
        -moz-border-radius-bottomleft: 0px;
        -webkit-border-radius: 2px 2px 0px 0px;
        border-radius: 2px 2px 0px 0px;
        border-bottom-color: #6D84B4;
    }

    .dropdown-a input[type="checkbox"] + .dropdown_button .arrow {
        display: inline-block;
        width: 0px;
        height: 0px;
        border-top: 5px solid #6B7FA7;
        border-right: 5px solid transparent;
        border-left: 5px solid transparent;
    }

    .dropdown-a input[type="checkbox"]:checked + .dropdown_button .arrow { border-color: white transparent transparent transparent }

    .dropdown-a .dropdown_content {
        position: absolute;
        border: 1px solid #777;
        padding: 0px;
        background: white;
        margin: 0;
        display: none;
    }

    .dropdown-a .dropdown_content li {
        list-style: none;
        margin-left: 0px;
        line-height: 16px;
        border-top: 1px solid #FFF;
        border-bottom: 1px solid #FFF;
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .dropdown-a .dropdown_content li:hover {
        border-top-color: #3B5998;
        border-bottom-color: #3B5998;
        background: #6D84B4;
    }

    .dropdown-a .dropdown_content li a {
        display: block;
        padding: 2px 7px;
        padding-right: 15px;
        color: black;
        text-decoration: none !important;
        white-space: nowrap;
    }

    .dropdown-a .dropdown_content li:hover a {
        color: white;
        text-decoration: none !important;
    }

    .dropdown-a input[type="checkbox"]:checked ~ .dropdown_content { display: block }

    .dropdown-a input[type="checkbox"] { display: none }

    .scroll {
        white-space: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: -ms-autohiding-scrollbar; 
    }

    .cd-panel {
        /*...*/
        visibility: hidden;
        transition: visibility 0s 0.6s;
    }

    .cd-panel.is-visible {
        visibility: visible;
        transition: visibility 0s 0s;
        z-index: 9999999;
    }

    .cd-panel-header {
        /*...*/
        position: fixed;
        top: -50px;
        width: 100%;
        height: 78px;
        transition: top 0.3s 0s;
    }

    .is-visible .cd-panel-header {
        top: 0;
        transition: top 0.3s 0.3s;
    }

    .cd-panel-container {
        /*...*/
        position: fixed;
        width: 90%;
        height: 100%;
        top: 0;
        right: 0;

        transition-property: transform;
        transition-duration: 0.3s;
        transition-delay: 0.3s;

        transform: translate3d(100%, 0, 0);
    }

    .is-visible .cd-panel-container {
        transform: translate3d(0, 0, 0);
        transition-delay: 0s;
    }

    .cd-panel-content{
        margin-top: 82px;
        max-height: 600px;
        overflow-y: auto;
    }

    @media only screen 
    and (min-width: 480px) 
    and (max-width: 600px) {
        .bgImage {
            margin-top: 0px !important;
        }

        #timelineProfilePic {
            width: 34% !important;
        }

        .menuItem-parent {
            width: 100% !important;
        }

        .menu-parent {
            margin-top: 30px !important;
        }

        li.dropdown {
            width: 22%;
            float:left;
        }
    }

    @media only screen 
    and (min-width: 600px) 
    and (max-width: 1300px) {
        .bgImage {
            margin-top: -84px !important;
        }

        #timelineProfilePic {
            width: 24% !important;
            height: 152px;
        }

        .menuItem-parent {
            width: 100% !important;
        }

        .menu-parent {
            margin-top: 30px !important;
        }

        li.dropdown {
            width: 22%;
            float:left;
        }
    }

    .ui-autocomplete {
        z-index: 9999;
    }

    @media only screen 
    and (min-width: 320px) 
    and (max-width: 480px) {
        .bgImage {
            margin-top: 26px !important;
        }

        #timelineProfilePic {
            width: 44% !important;
            height: 124px;
            margin-top: -105px;
        }

        .menuItem-parent {
            width: 100% !important;
        }

        .menu-parent {
            margin-top: 30px !important;
        }

        li.dropdown {
            width: 22%;
            float:left;
        }
    }
</style>


<?php
$totalCommentsToDisplay = 1;
?>

<div id="wrapper">


    <div id="page-wrapper" class="gray-bg" style="min-height: 365px;">

        <div class="row wrapper border-bottom white-bg page-heading">

            <div class="col-lg-8" >
                <div class="col-lg-8">
                    <div class="vasplus_pb_Search pull-left col-lg-12" style="margin-top:20px;">
                        <form>
                            <input style="border-radius: 8px; border: 1px solid #CCC" type="text" name="vpv_search_box" class="form-control" id="vpv_search_box" placeholder="Search for people or pages">
                            <a href="javascript:void(0);" id="vpv_search_button" onclick="vpb_auto_search_people_and_pages ();"></a>
                        </form>
                        <div class="vpb_search_results" style="display: block;">
                            <ul>
                                <div id="response_brought">  </div>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" style="margin-top:32px;">
                    <a href="#">Home</a>
                    <a href="#"><?= $objUser->getFirstName () ?></a>

                    <ul class="nav navbar-nav navbar-right" style="margin-top: -14px;">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count" style="border-radius:10px;"></span> <span class="glyphicon glyphicon-envelope" style="font-size:18px;"></span></a>
                            <ul class="dropdown-menu notificationsWidow">



                            </ul>
                        </li>

                        <li class="dropdown" id="friendRequests">
                            <span id="counter">0</span>
                            <div id="friendRequests_bar">
                                <div id="arrowUp"></div>
                                <div id="notificationTitle">Friend Requests</div>
                                <div id="friends-body" style="max-height:250px; overflow: auto;">

                                </div>
                                <div id="notificationFooter"><a href="#">See All</a></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>


        </div>
        <div class="wrapper wrapper-content animated fadeInRight">


            <div class="col-lg-10 col-md-offset-1" id="container">

                <div id="timelineContainer">
                    <!-- timeline background -->
                    <div id="timelineBackground">
                        <img src="<?= $objUser->getBackgroundImage () ?>" class="bgImage" style="margin-top: <?= $objUser->getBackgroundPosition () ?>px;">
                    </div>

                    <!-- timeline background -->
                    <div id="timelineShade">
                        <form id="bgimageform" method="post" enctype="multipart/form-data" action="controllers/saveBackgroundImage.php">
                            <input type="hidden" id="position" name="position">
                            <div class="uploadFile timelineUploadBG">
                                <input type="file" name="photoimg" id="bgphotoimg" class="custom-file-input">
                            </div>
                        </form>
                    </div>

                    <!-- timeline profile picture -->
                    <div id="timelineProfilePic" style="padding:0px; width: 20%;">
                        <img style="width:100%;" class="userProfileImg" src="<?= $objUser->getProfileImage () ?>">

                        <div>
                            <form id="userImage" method="post" enctype="multipart/form-data">
                                <div class="uploadFile2">
                                    <input type="file" class="custom-file-upload" name="userImage" id="profileimgaa">
                                </div>
                            </form>
                        </div>


                    </div>

                    <!-- timeline title -->
                    <div id="timelineTitle"><?= $objUser->getFirstName () . ' ' . $objUser->getLastName () ?></div>

                    <!-- timeline nav -->
                    <div id="timelineNav">
                        <div class="col-lg-10 col-md-offset-4 menu-parent" style="margin-top: 12px;">
                            <div class="col-lg-2 menuItem-parent">
                                <a href="index.php">Timeline</a>

                            </div>

                            <div class="col-lg-2 menuItem-parent">
                                <a class="menuItem" type="about" href="#">About</a>

                            </div>

                            <div class="col-lg-2 menuItem-parent">
                                <a class="menuItem" type="friends" href="#">Friends</a>

                            </div>

                            <div class="col-lg-2 menuItem-parent">
                                <a class="menuItem" type="photos" href="#">Photos</a>

                            </div>

                            <div class="col-lg-2 menuItem-parent">
                                <div class="dropdown-a" id="dropdown">
                                    <input type="checkbox" id="drop1" />
                                    <label for="drop1" class="dropdown_button">More <span class="arrow"></span></label>
                                    <ul class="dropdown_content">
                                        <li><a class="menuItem" type="groups" href="#">Groups</a></li>
                                        <li><a class="menuItem" type="events" href="#">Events</a></li>
                                        <li><a class="menuItem" type="pages" href="#">Pages</a></li>                                
                                        <li><a class="menuItem" type="likes" href="#">Likes</a></li>                                                                
                                    </ul>

                                </div>

                            </div>
                        </div>

                    </div>



                </div>
            </div>

            <div class="row m-b-lg m-t-lg">

                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">

                            <div class="col-md-11">
                                <form id="commentform" name="commentform">
                                    <textarea class="form-control mentions autoExpand" id="comment" rows="3" placeholder="whats on your mind"></textarea><br>

                                    <div class="col-lg-12 pull-left" id="output" style="
                                         margin-left: 21px;
                                         ">

                                    </div>

                                    <button type="submit" id="button" class="btn btn-primary btn-sm">post</button>
                                    <button type="button" class="btn btn-success StartUpload btn-sm">Upload Pictures</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">

                </div>

            </div>
            <div class="row">


                <div class="col-lg-4 pull-left">
                    <div class="ibox">
                        <div class="ibox-content" style="padding:0px;">
                            <div style="padding-left: 26px;">
                                <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                                <a href="img/gallery/2.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/2s.jpg"></a>
                                <a href="img/gallery/3.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/3s.jpg"></a>
                                <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                                <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                                <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                                <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                                <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                                <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>

                            </div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-content">
                            <i class="fa fa-calendar"></i>
                            <h3>Events</h3>

                            <ul class="list-group clear-list">
                                <?php
                                if ( !empty ($arrEvents) ):
                                    foreach ($arrEvents as $arrEvent):
                                        ?>
                                        <li style="cursor: pointer;" event-id="<?= $arrEvent->getId () ?>" class="list-group-item fist-item showEvent">
                                            <span class="pull-right"> <?= $arrEvent->getEventDate () ?> </span>
                                            <?= $arrEvent->getEventName () ?>
                                        </li>
                                        <?php
                                    endforeach;
                                endif;
                                ?>

                            </ul>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-content">
                            <h3>Groups</h3>

                            <ul class="list-group clear-list">
                                <?php
                                if ( !empty ($arrGroups) ):
                                    foreach ($arrGroups as $arrGroup):
                                        ?>
                                        <li style="cursor: pointer;" class="list-group-item fist-item">

                                            <a href="group.php?groupId=<?= $arrGroup->getId () ?>">
                                                <?= $arrGroup->getGroupName () ?>
                                            </a>

                                            <a group-id="<?= $arrGroup->getId () ?>" href="#" class="showGroup">Members</a>
                                        </li>
                                        <?php
                                    endforeach;
                                endif;
                                ?>

                            </ul>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-content">
                            <h3>Friends</h3>

                            <div class="user-friends">
                                <?php foreach ($arrFriendList as $arrFriend): ?>


                                    <a href=""><img title="<?= $arrFriend->getFirstName () . ' ' . $arrFriend->getLastName () ?>" alt="image" class="img-circle" src="uploads/profile/<?= $arrFriend->getUsername () ?>.jpg"></a>
                                <?php endforeach; ?>
                            </div>

                            <br><a href="#" class="seeAllFriends">See all friends</a>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="chat-users">


                                <div class="users-list">
                                    <?php
                                    foreach ($arrUsers as $arrUser):
                                        if ( $arrUser->getId () !== $_SESSION['user']['user_id'] ):
                                            ?>
                                            <div class="chat-user">
                                                <span class="pull-right label label-primary">Online</span> 
                                                <img class="chat-avatar" src="img/a4.jpg" alt="">
                                                <div class="chat-user-name">
                                                    <a class="show-chat" user-id="<?= $arrUser->getId () ?>" href="#"><?= $arrUser->getFirstName () . ' ' . $arrUser->getLastName () ?></a>
                                                </div>
                                            </div>

                                            <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="posts-list" class="col-lg-7 pull-left">

                    <?php
                    if ( empty ($arrPosts) )
                    {
                        echo 'Be the first person to post';
                    }
                    else
                    {
                        require_once 'models/UploadFactory.php';
                        $objUpload = new UploadFactory();

                        foreach ($arrPosts as $arrComment) {

                            $usersLikes = [];
                            $likeCount = 0;
                            $likeClass = 'like';
                            $likes = '';

                            $arrLikes = $arrComment->getArrLikes ();

                            if ( !empty ($arrLikes) )
                            {
                                $usersLikes = array_reduce ($arrLikes, function ($reduced, $current) {

                                    $fullName = $current->getFirstName () . ' ' . $current->getLastName ();

                                    $reduced[$current->getUsername ()] = $fullName;
                                    return $reduced;
                                });

                                $likes = implode (',', array_slice ($usersLikes, 0, 2));
                                $likeCount = count ($usersLikes) - 2;
                                $likeClass = !empty ($usersLikes) && array_key_exists ($_SESSION['user']['username'], $usersLikes) ? 'unlike' : $likeClass;
                            }

                            echo '<div class="social-feed-box">

                                <div class="pull-right social-action dropdown">
                                    <button data-toggle="dropdown" class="dropdown-toggle btn-white">
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu m-t-xs">
                                        <li><a href="#">Config</a></li>
                                    </ul>
                                </div>
                        
                                <div class="social-avatar">
                                    <a href="" class="pull-left">
                                        <img alt="image" src="uploads/profile/' . $arrComment->getUsername () . '.jpg">
                                    </a>
                                    <div class="media-body">
                                        <a href="profile.php?user=' . $arrComment->getUserId () . '">
                                            ' . $arrComment->getAuthor () . '
                                        </a>
                                        <small date="' . $arrComment->getCreated () . '" class="text-muted timeAgo">' . blab_time_ago ($arrComment->getCreated ());
                            echo trim ($arrComment->getLocation ()) !== "" ? ' ' . $arrComment->getLocation () : '';
                            echo '</small>
                                    </div>
                                </div>
                            
                                <div class="social-body">
                                  <p>
                                     ' . $arrComment->getMessage () . '
                                  </p>';

                            $arrImages = $arrComment->getArrImages ();

                            if ( !empty ($arrImages) )
                            {
                                echo '<section class="photos-frame">';

                                foreach ($arrImages as $arrImage) {


                                    echo '<img style="width:100%;" src="' . $arrImage->getFileLocation () . '" class="img-responsive">';
                                }

                                echo '</section>';
                            }

//                            if ( !empty ($arrComment['img']) )
//                            {
//                                echo '<img src="' . $arrComment['img'] . '" class="img-responsive">';
//                            }

                            echo '<div class="btn-group">
                                      <button id="' . $arrComment->getId () . '" class="btn btn-white btn-xs ' . $likeClass . ' post"><i class="fa fa-thumbs-up"></i> Like this! (' . $arrComment->getLikes () . ')</button>
                                          <a href="" id="' . $arrComment->getId () . '" class="showLikes" type="post">' . $likes . ' ' . ($likeCount > 0 ? ' and ' . $likeCount . ' others' : ' liked this ') . ' </a>
                                      <button class="btn btn-white btn-xs"><i class="fa fa-comments"></i> Comment</button>
                                      <button class="btn btn-white btn-xs Share" messageId="' . $arrComment->getId () . '"><i class="fa fa-share"></i> Share</button>
                                  </div>
                              </div>';

                            echo '<div class="social-footer" id="' . $arrComment->getId () . '">';

                            $comments = $arrComment->getArrComments ();

                            if ( count ($comments) > 0 ):


                                foreach ($comments as $count => $comment):

                                    $class = $count >= $totalCommentsToDisplay ? 'd-none' : '';

                                    $userCommentLikes = [];
                                    $likeCommentCount = 0;
                                    $commentLikes = '';
                                    $likeCommentClass = 'like';

                                    $arrCommentLikes = $comment->getArrLikes ();

                                    if ( !empty ($arrCommentLikes) )
                                    {

                                        $userCommentLikes = array_reduce ($arrCommentLikes, function ($reduced, $current) {

                                            $fullName = $current->getFirstName () . ' ' . $current->getLastName ();

                                            $reduced[$current->getUsername ()] = $fullName;
                                            return $reduced;
                                        });

                                        $likeCommentClass = !empty ($userCommentLikes) && array_key_exists ($_SESSION['user']['username'], $userCommentLikes) ? 'unlike' : $likeClass;
                                        $commentLikes = implode (',', array_slice ($userCommentLikes, 0, 2));
                                        $likeCommentCount = count ($usersLikes) - 2;
                                    }

                                    if ( $count === $totalCommentsToDisplay )
                                    {
                                        echo '<a id="' . $arrComment->getId () . '" href="#" class="viewMore">View More Comments</a>';
                                    }

                                    echo '<div class="social-comment comment-a1 ' . $class . '">
                                                <a href="profile.php?user=' . $comment->getUserId () . '" class="pull-left">
                                                    <img alt="image" src="uploads/profile/' . $comment->getUsername () . '.jpg">
                                                </a>
                                                <div class="media-body">
                                                    <a href="profile.php?user=' . $comment->getUserId () . '">
                                                        ' . $comment->getAuthor () . ' <br>
                                                    </a>
                                                    <small date="' . $comment->getCreated () . '" class="text-muted timeAgo">' . blab_time_ago ($comment->getCreated ()) . '</small> <br>
                                                    <span style="font-size:14px;">
                                                   ' . $comment->getComment () . '
                                                       </span>
                                                    
                                                </div>
                                                
<div class="btn-group">
                                      <button comment-id="' . $comment->getId () . '" id="' . $arrComment->getId () . '" class="btn btn-white btn-xs ' . $likeCommentClass . ' comment"><i class="fa fa-thumbs-up"></i> Like this! (' . $comment->getLikes () . ')</button>
                                          <a href="#" class="showLikes" id="' . $comment->getId () . '" type="comment">' . $commentLikes . ' ' . ($likeCommentCount > 0 ? ' and ' . $likeCommentCount . ' others' : ' liked this ') . '</a>
                                      <button class="btn btn-white btn-xs"><i class="fa fa-comments"></i> Comment</button>
                                      <button class="btn btn-white btn-xs Share" messageId="' . $comment->getId () . '"><i class="fa fa-share"></i> Share</button>
                                  </div>
                                            </div>';


                                endforeach;

                            endif;
                            echo '<div class="social-comment a1">
                                <a href="href="profile.php?user=' . $_SESSION['user']['user_id'] . '"" class="pull-left">
                                    <img alt="image" src="img/a3.jpg">
                                </a>
                                <div class="media-body">
                                    <textarea comment-id="' . $arrComment->getId () . '" class="form-control reply-comment" placeholder="Write comment..."></textarea>
                                </div>
                            </div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>

                    <div class="loadmore" data-page='2'>
                        <div class="content"></div><div class="loading-bar">Scroll for more or click here</div></div>

                </div>



            </div>

            <div class="chatWindow" style="display:none;">

            </div>

        </div>
        <div class="footer">
            <div class="pull-right">
                10GB of <strong>250GB</strong> Free.
            </div>
            <div>
                <strong>Copyright</strong> Example Company © 2014-2015
            </div>
        </div>

    </div>
</div>

<div class="cd-panel from-right" style="background-color: #FFF;">


    <div class="cd-panel-container" style="background-color: #FFF;">

        <div class="about" style="display:none;">
            <header class="cd-panel-header">
                <h1>About</h1>
                <a href="#0" class="cd-panel-close">Close</a>
            </header>

            <div class="cd-panel-content" style="margin-top:82px;">
                <section id="core" class="col-lg-8">
                    <div class="profileinfo">

                        <div class="gear">
                            <label>Primary E-Mail:</label>
                            <span id="email" class="datainfo"><?= $objUser->getEmail () ?></span>

                            <?php if ( $_SESSION['user']['user_id'] == $_GET['user'] ): ?>
                                <a type="email" href="#" class="editlink">Edit Info</a>
                                <a class="savebtn">Save</a>
                                <a class="cancel">Cancel</a>
                            <?php endif; ?>
                        </div>

                        <div class="gear">
                            <label>Phone:</label>
                            <span id="phone1" class="datainfo"><?= $objUser->getTelephone1 () ?> | <?= $objUser->getTelephone2 () ?>

                            </span>

                            <?php if ( $_SESSION['user']['user_id'] == $_GET['user'] ): ?>
                                <a type="phone" href="#" class="editlink">Edit Info</a>
                                <a class="savebtn">Save</a>
                                <a class="cancel">Cancel</a>
                            <?php endif; ?>
                        </div>

                        <div class="gear">
                            <label>Full Name:</label>
                            <span id="fullname" class="datainfo"><?= $objUser->getFirstName () . ' | ' . $objUser->getLastName () ?></span>
                            <?php if ( $_SESSION['user']['user_id'] == $_GET['user'] ): ?>
                                <a  type="name" href="#" class="editlink">Edit Info</a>
                                <a class="savebtn">Save</a>
                                <a class="cancel">Cancel</a>
                            <?php endif; ?>
                        </div>

                        <div class="gear">
                            <label>Birthday:</label>
                            <span id="birthday" class="datainfo"><?= $objUser->getDob () ?></span>
                            <?php if ( $_SESSION['user']['user_id'] == $_GET['user'] ): ?>
                                <a type="dob" href="#" class="editlink">Edit Info</a>
                                <a class="savebtn">Save</a>
                                <a class="cancel">Cancel</a>
                            <?php endif; ?>
                        </div>

                        <div class="gear">
                            <label>Address:</label>
                            <span id="address" class="datainfo"><?= $objUser->getAddress () ?> | <?= $objUser->getTown () ?> | <?= $objUser->getPostCode () ?>


                            </span>
                            <?php if ( $_SESSION['user']['user_id'] == $_GET['user'] ): ?>
                                <a type="address" href="#" class="editlink">Edit Info</a>
                                <a class="savebtn">Save</a>
                                <a class="cancel">Cancel</a>
                            <?php endif; ?>
                        </div>

                        <div class="gear">
                            <label>Occupation:</label>
                            <span id="occupation" class="datainfo"><?= $objUser->getJobTitle () ?></span>
                            <?php if ( $_SESSION['user']['user_id'] == $_GET['user'] ): ?>
                                <a type="job" href="#" class="editlink">Edit Info</a>
                                <a class="savebtn">Save</a>
                                <a class="cancel">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="friends" style="display:none;">
            <header class="cd-panel-header">
                <h1>Friends</h1>
                <a href="#0" class="cd-panel-close pull-right">Close</a>
            </header>

            <div class="cd-panel-content">

            </div>
        </div>

        <div class="photos" style="display:none;">
            <header class="cd-panel-header">
                <h1>Photos</h1>
                <a href="#0" class="cd-panel-close pull-right">Close</a>
            </header>

            <div class="cd-panel-content">

            </div>
        </div>

        <div class="pages" style="display:none;">
            <header class="cd-panel-header">
                <h1>Pages</h1>
                <a href="#0" class="cd-panel-close">Close</a>
            </header>

            <div class="cd-panel-content">

            </div>
        </div>

        <div class="events" style="display:none;">
            <header class="cd-panel-header">
                <h1>Events</h1>
                <a href="#0" class="cd-panel-close pull-right">Close</a>
            </header>

            <div class="cd-panel-content">

            </div>
        </div>

        <div class="groups" style="display:none;">
            <header class="cd-panel-header">
                <h1>Groups</h1>
                <a href="#0" class="cd-panel-close pull-right">Close</a>
            </header>

            <div class="cd-panel-content">

            </div>
        </div>

        <div class="likes" style="display:none;">
            <header class="cd-panel-header">
                <h1>Likes</h1>
                <a href="#0" class="cd-panel-close">Close</a>
            </header>

            <div class="cd-panel-content">

            </div>
        </div>


    </div>

</div> <!-- cd-panel -->

<div class="modal inmodal" id="allLikes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated flipInY">
            Likes
        </div>
    </div>
</div>

<div class="modal inmodal" id="showAllFriends" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated flipInY">
            Friends
        </div>
    </div>
</div>

<div class="modal inmodal" id="eventUsers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated flipInY">
            Likes
        </div>
    </div>
</div>

<div class="modal inmodal" id="groupUsers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated flipInY">
            Likes
        </div>
    </div>
</div>

<div class="modal inmodal" id="eventPopup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Create Event</h5>

                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" name="eventForm" id="eventForm">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Event Name</label>

                            <div class="col-lg-10">
                                <input type="text" placeholder="Event Name" id="eventName" name="eventName" class="form-control"> 
                            </div>

                            <div class="geo-details">

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Location</label>

                            <div class="col-lg-10">
                                <input id="geocomplete" type="text" placeholder="Type in an address" />
                                <input style="display:none;" id="find" type="button" value="find" />
        <!--                                <input type="text" placeholder="Location" id="geocomplete" name="location" class="form-control"></div>-->
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-lg-2 control-label">Event Date</label>

                            <div class="col-lg-10">
                                <input type="text" placeholder="Event Date" id="eventDate" name="eventDate" class="form-control"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Event Time</label>

                            <div class="input-group clockpicker col-lg-10" data-autoclose="true">
                                <input type="text" class="form-control" value="09:30" >
                                <span class="input-group-addon">
                                    <span class="fa fa-clock-o"></span>
                                </span>
                            </div>
                        </div>



                        <div style="height: 250px; overflow-y: auto;">
                            <ul class="list-group clear-list">

                                <?php foreach ($arrFriendList as $friend): ?>

                                    <li class="list-group-item fist-item">
                                        <?= $friend->getFirstName () . ' ' . $friend->getLastName () ?>
                                        <input class="checkbox-primary event-checkbox" type="checkbox" value="<?= $friend->getId () ?>">
                                    </li>

                                <?php endforeach; ?>
                            </ul>

                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary saveEvent" type="submit">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="groupPopup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated flipInY">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Create Group</h5>

                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" name="groupForm" id="eventForm">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Group Name</label>

                            <div class="col-lg-10">
                                <input type="text" placeholder="Group Name" id="groupName" name="groupName" class="form-control"> 
                            </div>
                        </div>

                        <div style="height: 250px; overflow-y: auto;">
                            <ul class="list-group clear-list">

                                <?php foreach ($arrFriendList as $friend): ?>

                                    <li class="list-group-item fist-item">
                                        <?= $friend->getFirstName () . ' ' . $friend->getLastName () ?>
                                        <input class="checkbox-primary group-checkbox" type="checkbox" value="<?= $friend->getId () ?>">
                                    </li>

                                <?php endforeach; ?>
                            </ul>

                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary saveGroup" type="submit">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="photoUpload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY pull-left">
            <form name="uploadForm" id="uploadForm" enctype="multipart/form-data" style="min-width: 450px;">

                <input style="display:none;" type="file" name="pictures[]" multiple="multiple" id="fileupload" />

                <textarea placeholder="Say something about the photo(s)" name="uploadComment" id="uploadComment" style="height:100px; width:90%; margin-left:30px; margin-top:30px;" class="form-control"></textarea>

                <button style="display:none;" type="submit" class="btn btn-primary btn-sm Upload">Upload</button>
            </form>


            <div id="dvPreview"></div>

        </div>
    </div>
</div>




<script src="js/jquery-2.1.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="js/jquery.mentions.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="js/menu.js"></script>
<script src="js/inspinia.js"></script>

<script src="js/bootstrap.js"></script>
<script src="js/slimscroll.js"></script>
<script src="js/pagination.js"></script>
<script src="js/search.js"></script>
<script src="js/sweetalert.js"></script>
<script src="js/datetime.js"></script>
<script src="js/convertVideo.js"></script>
<script src="js/clockpicker.js"></script>
<script src="js/icheck.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyA1COtE7cJxfGxNWiTjEgtSNhbvDBCiiqQ&libraries=places"></script>
<script src="js/geocomplete.js"></script>
<script src="js/newsFeed.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="js/ajaxForm.js"></script>

<script>

                                function readURL2 (input)
                                {

                                    if (input.files && input.files[0])
                                    {
                                        var reader = new FileReader ();

                                        reader.onload = function (e)
                                        {
                                            $ ('#timelineProfilePic > img').attr ('src', e.target.result).width ('100%');

                                        };

                                        reader.readAsDataURL (input.files[0]);
                                    }
                                }

                                function readURL (input)
                                {

                                    if (input.files && input.files[0])
                                    {
                                        var reader = new FileReader ();

                                        reader.onload = function (e)
                                        {
                                            $ ('#timelineBackground > img').attr ('src', e.target.result);
                                            $ ('#timelineBackground > img').attr ('id', 'timelineBGload');
                                            $ ('#timelineBackground > img').addClass ("headerimage");
                                            $ ('#timelineBackground').prepend ('<div id="uX1" class="bgSave wallbutton blackButton">Save Cover</div>');
                                            $ ("#timelineShade").hide ();
                                            $ ("#bgimageform").hide ();
                                        };

                                        reader.readAsDataURL (input.files[0]);
                                    }
                                }

                                $ (document).ready (function ()
                                {
                                    $ ('.cd-panel-close').on ('click', function (event)
                                    {

                                        $ ('.cd-panel').removeClass ('is-visible');
                                        $ ("#page-wrapper").fadeIn ();
                                        event.preventDefault ();

                                    });

                                    $ (".menuItem").on ("click", function ()
                                    {
                                        var type = $ (this).attr ("type");

                                        $ ("#page-wrapper").fadeOut ();
                                        $ ('.cd-panel').addClass ('is-visible');

                                        switch (type) {
                                            case "about":
                                                $ (".about").show ();
                                                return false;


                                                break;

                                            case "friends":
                                                var strUrl = "controllers/getAllFriends.php";
                                                var el = $ (".friends");
                                                break;

                                            case "photos":
                                                var strUrl = "album.php";
                                                var el = $ (".photos");
                                                break;

                                            case "groups":
                                                var strUrl = "controllers/getAllGroups.php";
                                                var el = $ (".groups");
                                                break;

                                            case "events":
                                                var strUrl = "controllers/getAllEvents.php";
                                                var el = $ (".events");
                                                break;

                                            case "pages":
                                                var strUrl = "controllers/getAllPages.php";
                                                var el = $ (".pages");
                                                break;

                                            case "likes":
                                                var strUrl = "controllers/getAllLikes.php";
                                                var el = $ (".likes");
                                                break;
                                        }

                                        $.ajax ({
                                            url: strUrl + '?userId=1',
                                            type: 'GET',
                                            success: function (response)
                                            {
                                                console.log (el);
                                                alert (response);
                                                el.find (".cd-panel-content").html (response);
                                                el.show ();
                                            }
                                        });

                                        event.preventDefault ();

                                    });

                                    $ (".editlink").on ("click", function (e)
                                    {
                                        e.preventDefault ();

                                        var type = $ (this).attr ("type");

                                        var dataset = $ (this).prev (".datainfo");
                                        var savebtn = $ (this).next (".savebtn");
                                        var cancelbtn = $ (this).next ().next (".cancel");
                                        var theid = dataset.attr ("id");
                                        var newid = theid + "-form";
                                        var currval = dataset.text ();

                                        dataset.empty ();

                                        switch (type) {
                                            case "phone":

                                                currval = currval.split ('|');

                                                $ ('<input style="padding:10px;" type="text" name="telephone1-form" id="telephone1-form" value="' + currval[0] + '" class="hlite">').appendTo (dataset);
                                                $ ('<input style="padding:10px;" type="text" name="telephone2-form" id="telephone2-form" value="' + currval[1] + '" class="hlite">').appendTo (dataset);
                                                break;

                                            case "name":

                                                currval = currval.split ('|');

                                                $ ('<input style="padding:10px;" type="text" name="firstname-form" id="firstname-form" value="' + currval[0] + '" class="hlite">').appendTo (dataset);
                                                $ ('<input style="padding:10px;" type="text" name="lastname-form" id="lastname-form" value="' + currval[1] + '" class="hlite">').appendTo (dataset);
                                                break;

                                            case "address":
                                                currval = currval.split ('|');

                                                $ ('<input style="padding:10px;" type="text" name="town-form" id="town-form" value="' + currval[0] + '" class="hlite">').appendTo (dataset);
                                                $ ('<input style="padding:10px;" type="text" name="address-form" id="address-form" value="' + currval[1] + '" class="hlite">').appendTo (dataset);
                                                $ ('<input style="padding:10px;" type="text" name="postcode-form" id="postcode-form" value="' + currval[2] + '" class="hlite">').appendTo (dataset);
                                                break;

                                            default:
                                                $ ('<input type="text" name="' + newid + '" id="' + newid + '" value="' + currval + '" class="hlite">').appendTo (dataset);
                                                break;
                                        }

                                        $ (this).css ("display", "none");
                                        savebtn.css ("display", "block");
                                        cancelbtn.css ("display", "block");
                                    });

                                    $ (".cancel").on ("click", function ()
                                    {

                                        var elink = $ (this).prev ().prev (".editlink");
                                        $ (this).prev ().css ("display", "none");
                                        var dataset = elink.prev (".datainfo");
                                        var newid = dataset.attr ("id");

                                        var cinput = "#" + newid + "-form";
                                        var einput = $ (cinput);

                                        var txtData = [];

                                        elink.parent ().find ("input").each (function ()
                                        {

                                            txtData.push ($ (this).val ());

                                        });

                                        dataset.html (txtData.join (" | "));

                                        $ (this).css ("display", "none");
                                        elink.parent ().find ("input").remove ();
                                        elink.css ("display", "block");

                                    });

                                    $ (".savebtn").on ("click", function (e)
                                    {
                                        e.preventDefault ();
                                        var elink = $ (this).prev (".editlink");
                                        var dataset = elink.prev (".datainfo");
                                        $ (this).prev ().css ("display", "block");
                                        var newid = dataset.attr ("id");
                                        $ (this).next ().css ("display", "none");

                                        var txtData = [];
                                        var numeric = {};

                                        elink.parent ().find ("input").each (function ()
                                        {
                                            numeric[this.name] = this.value;
                                            txtData.push ($ (this).val ());
                                            $ (this).remove ();

                                        });

                                        $.ajax ({
                                            url: 'controllers/updateProfileData.php',
                                            type: 'POST',
                                            data: numeric,
                                            success: function (response)
                                            {
                                                alert (response);
                                            }
                                        });

                                        dataset.html (txtData.join (" | "));

                                        $ (this).css ("display", "none");
                                    });

                                    $ ('body').on ('click', '.userProfileImg', function ()
                                    {
                                        $ ("#profileimgaa").click ();
                                    });

                                    $ ("#profileimgaa").off ();
                                    $ ("#profileimgaa").on ("change", function ()
                                    {
                                        $ ("#profileimgaa").hide ();
                                        readURL2 (this);
                                        $ ("#userImage").submit ();
                                    });

                                    $ ("#userImage").on ("submit", function ()
                                    {
                                        //stop submit the form, we will post it manually.
                                        event.preventDefault ();
                                        // Get form
                                        var form = $ ('#userImage')[0];
                                        // Create an FormData object
                                        var data = new FormData (form);

                                        console.log (data);

                                        // If you want to add an extra field for the FormData
                                        // disabled the submit button
                                        $.ajax ({
                                            type: "POST",
                                            enctype: 'multipart/form-data',
                                            url: "controllers/uploadProfile.php",
                                            data: data,
                                            processData: false,
                                            contentType: false,
                                            cache: false,
                                            success: function (response)
                                            {

                                                alert (response);
                                                return false;
                                            },
                                            error: function (e)
                                            {


                                            }
                                        });

                                        return false;
                                    });

                                    /* Uploading Profile BackGround Image */
                                    $ ('body').on ('change', '#bgphotoimg', function ()
                                    {
                                        var input = $ ("#bgphotoimg");

                                        readURL (this);
                                    });

                                    /* Banner position drag */
                                    $ ("body").on ('mouseover', '.headerimage', function ()
                                    {
                                        var y1 = $ ('#timelineBackground').height ();
                                        var y2 = $ ('.headerimage').height ();
                                        $ (this).draggable ({
                                            scroll: false,
                                            axis: "y",
                                            drag: function (event, ui)
                                            {
                                                if (ui.position.top >= 0)
                                                {
                                                    ui.position.top = 0;
                                                }
                                                else if (ui.position.top <= y1 - y2)
                                                {
                                                    ui.position.top = y1 - y2;
                                                }
                                            },
                                            stop: function (event, ui)
                                            {
                                            }
                                        });
                                    });

                                    $ ("body").on ('click', '.bgSave', function ()
                                    {
                                        $ ("#bgimageform").submit ();
                                    });

                                    $ ("#bgimageform").on ("submit", function ()
                                    {
                                        var p = $ ("#timelineBGload").attr ("style");
                                        var Y = p.split ("top:");
                                        var Z = Y[1].split (";");
                                        var dataString = '&position=' + Z[0];

                                        $ ("#position").val ($ (".bgImage").offset ().top);

                                        //stop submit the form, we will post it manually.
                                        event.preventDefault ();
                                        // Get form
                                        var form = $ ('#bgimageform')[0];
                                        // Create an FormData object
                                        var data = new FormData (form);

                                        console.log (data);

                                        // If you want to add an extra field for the FormData
                                        // disabled the submit button
                                        $.ajax ({
                                            type: "POST",
                                            enctype: 'multipart/form-data',
                                            url: "controllers/saveBackgroundImage.php",
                                            data: data,
                                            processData: false,
                                            contentType: false,
                                            cache: false,
                                            success: function (data)
                                            {

                                                $ (".bgImage").fadeOut ('slow');
                                                $ (".bgSave").fadeOut ('slow');
                                                $ ("#timelineShade").fadeIn ("slow");
                                                // $ ("#timelineBGload").removeClass ("headerimage").css ({'margin-top': data});
                                                $ ("#timelineBackground").html (data);
                                                return false;
                                            },
                                            error: function (e)
                                            {


                                            }
                                        });

                                        return false;
                                    });



                                });
</script>