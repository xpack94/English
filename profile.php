<?php
  include './inc/header.inc.php';
  if (isset($_GET['u'])) {
    $user = mysql_real_escape_string($_GET['u']);
    if (ctype_alnum($user)) {
      // check user exists
      $check = mysql_query("SELECT username, first_name FROM users WHERE username='$user'");
      if (mysql_num_rows($check)===1 && $user != "about") {
        $get = mysql_fetch_assoc($check);
        $theUserName = $get['username'];
        $first_name = $get['first_name'];
      } else {
        // If user doesn't exist then redirect to index
        echo "<meta http-equiv=\"refresh\" content=\"0; url=http://localhost/English/index.php\">";
        exit();
      }
    }
  } else {
    header("location: $username");
  }
  $post = @$_POST['post'];
  if ($post != "") {
  $date_added = date("Y-m-d");
  $added_by = $user;
  $user_posted_to = $user;
  $sqlCommand = "INSERT INTO posts VALUES('', '$post', '$date_added', '$added_by', '$user_posted_to')";
  $query = mysql_query($sqlCommand) or die(mysql_error());
  }
  // Check whether the user has uploaded a profile pic or not
  $check_pic = mysql_query("SELECT profile_pic FROM users WHERE username = '$user'");
  $get_pic_row = mysql_fetch_assoc($check_pic);
  $profile_pic_db = $get_pic_row['profile_pic'];
  if (@$profile_pic_db == NULL) {
    $profile_pic = "img/default-pp.jpg";
  } else {
    $profile_pic = "userdata/profile_pic/".$profile_pic_db;
  }
 ?>
 <br>
 <img id="pp" src="<?php echo $profile_pic; ?>" height="250" width="200" alt="<?php echo $user; ?>'s Profile" title="<?php echo $user; ?>'s Profile" onclick="navigate()" />
 <div class="postForm">
   <form action="<?php echo $user; ?>" method="post">
     <textarea id="post" name="post" rows="4" cols="58"></textarea>
     <input type="submit" class="btn btn-lg" name="send" value="Post" style="background-color: #DCE5EE; border: 1px solid #666; color:#666; height: 73px; width: 65px;">
   </form>
 </div>
 <div class="profilePosts">
   <?php
   $getposts = mysql_query("SELECT * FROM posts WHERE user_posted_to='$user' ORDER BY id DESC LIMIT 10") or die(mysql_errno());
   while ($row = mysql_fetch_assoc($getposts)) {
     $id = $row['id'];
     $body = $row['body'];
     $date_added = $row['added_by'];
     $user_posted_to = $row['user_posted_to'];
     echo '
     <div class="posted_by">
       <a href='.@$added_by.'>'.@$added_by.'</a> - '.$date_added.' -
     </div>
     &nbsp;&nbsp;'.@$body.'<br><hr>';
   }
    ?>
    <?php
      if (isset($_POST['addfriend'])) {
        $friend_request = $_POST['addfriend'];
        $user_to = $user;
        $user_from = $username;
        if (!$user_from == $user) {
          @$errorMsg = "You can't send a friend request to yourself!<br>";
        } else {
          $create_request = mysql_query("INSERT INTO friend_requests VALUES ('', '$user_from', '$user_to')");
          $errorMsg = "Your friend request has been sent";
        }
      } elseif (isset($_POST['unfriend'])) {
        $query = mysql_query("SELECT friend_array FROM users WHERE username='$username'");
        $get_friend_array_row = mysql_fetch_assoc($query);
        $get_record = $get_friend_array_row['friend_array'];
        $get_exploded_records = explode(', ', $get_record);
        foreach ($get_exploded_records as $friend) {
          echo $friend;
        }
      }
     ?>
 </div>
 <?php echo @$errorMsg;
  if (@$user != $username):
    ?>
   <form action="<?php echo $username ?>" method="post">
     <?php
     // Check if the profile owner is in the signed in user friend list or not.
     $query = mysql_query("SELECT friend_array FROM users WHERE username='$username'");
     $get_friend_array_row = mysql_fetch_assoc($query);
     $get_record = $get_friend_array_row['friend_array'];
     $get_exploded_records = explode(', ', $get_record);
     $isFriend = false;
     foreach ($get_exploded_records as $friend) {
       if ($user == $friend) {
         $isFriend = true;
       } else {
         $isFriend = false;
       }
     }
     if ($isFriend) {
       echo '<input type="submit" name="unfriend" value="Unfriend">';
     } else {
       echo '<input type="submit" name="addfriend" value="Add as a friend">';
     }
     echo '<input type="submit" name="sendmsg" value="Send message">';
    echo "</form>";
    endif; ?>
 <div class="textHeader"><?php echo $user; ?>'s Profile</div>
 <div class="profileLeftSideContent">
 <?php
 $about_query = mysql_query("SELECT bio FROM users WHERE username='$user'");
 $get_result = mysql_fetch_assoc($about_query);
 $about_the_user = $get_result['bio'];
 echo $about_the_user;
 ?>
 </div>
 <?php
     $GetListOfFriends = mysql_query("SELECT friend_array FROM users WHERE username = '$user'");
     $getRow = mysql_fetch_assoc($GetListOfFriends);
     $friend_array = explode(', ', $getRow['friend_array']);
     $friendsCount = Count($getRow['friend_array']);
  ?>
 <div class="textHeader"><?php echo $user; ?>'s Friends <?php echo $friendsCount;?></div><br>
 <div class="profileLeftSideContent">
   <?php
    foreach ($friend_array as $friend) {
      $GetImage = mysql_query("SELECT profile_pic FROM users WHERE username = '$friend'");
      $getRow = mysql_fetch_assoc($GetImage);
      echo '<a href="'.$friend.'"><img src="userdata/profile_pic/'.$getRow['profile_pic'].'" alt="'.$friend.'" title="'.$friend.'" name="FriendPhoto" height="50" width="40"/></a>&nbsp;&nbsp';
    }
    ?>
 </div>
 <?php include './inc/footer.inc.php'; ?>
