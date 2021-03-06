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

require_once 'models/FriendFactory.php';
require_once 'models/User.php';
require_once 'models/UserFactory.php';
require_once 'models/EventFactory.php';
require_once 'models/GroupFactory.php';

$objUserFactory = new UserFactory();
$arrUsers = $objUserFactory->getUsers ();

$objFriend = new FriendFactory();

$userId = $_SESSION['user']['user_id'];

$arrFriendList = $objFriend->getFriendList ($arrUsers[$userId]);

$objEventFactory = new EventFactory();

$arrEvents = $objEventFactory->getEventsForUser ($arrUsers[$userId]);

$objGroupFactory = new GroupFactory();

$arrGroups = $objGroupFactory->getGroupsForUser ($arrUsers[$userId]);

require_once 'models/PostFactory.php';
$objPostFactory = new PostFactory();
$arrPosts = $objPostFactory->getPostsForUser ($arrUsers[$_SESSION['user']['user_id']]);

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

<style>
    .pac-container {
        z-index: 9999 !important;
    }
</style>

<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="css/animate.css" rel="stylesheet">
<link href="css/newsFeed.css" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.mentions.css">
<link href="css/sweetalert.css" rel="stylesheet">
<link href="css/datetime.css" rel="stylesheet">
<link href="css/fonts.css" rel="stylesheet">
<link href="css/clockpicker.css" rel="stylesheet">
<link href="css/icheck.css" rel="stylesheet">
<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1COtE7cJxfGxNWiTjEgtSNhbvDBCiiqQ&callback=initMap"
type="text/javascript"></script>-->


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
                    <a href="#">News Feed</a>

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

                <div class="col-lg-3">


                    <ul>
                        <li><a href="#" id="createEvent">Create Event</a></li>
                        <li><a href="#" id="createGroup">Create Group</a></li>
                    </ul>


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
                            <h3>Michael Hampton</h3>
                            <div class="bgColor">
                                <form id="uploadForm2" action="uploadProfile.php" method="post">
                                    <div id="targetOuter">
                                        <div id="targetLayer"><img src="images/profile.jpg" width="400px" height="300px" class="upload-preview" /></div>
                                        <img src="uploads/profile/<?= $_SESSION['user']['username'] ?>.jpg"  class="icon-choose-image"/>
                                        <div class="icon-choose-image" onClick="showUploadOption ()"></div>
                                        <div id="profile-upload-option">
                                            <div class="profile-upload-option-list"><input name="userImage" id="userImage" type="file" class="inputFile" onChange="showPreview (this);"></input><span>Upload</span></div>
                                            <div class="profile-upload-option-list" onClick="removeProfilePhoto ();">Remove</div>
                                            <div class="profile-upload-option-list" onClick="hideUploadOption ();">Cancel</div>
                                        </div>
                                    </div>	
                                    <div>
                                        <input type="submit" value="Upload Photo" class="btnSubmit" onClick="hideUploadOption ();"/>
                                    </div>
                                </form>
                            </div>	
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

                <div id="posts-list" class="col-lg-9">

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
<script src="js/icheck.js."></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyA1COtE7cJxfGxNWiTjEgtSNhbvDBCiiqQ&libraries=places"></script>
<script src="js/geocomplete.js"></script>
<script src="js/newsFeed.js"></script>