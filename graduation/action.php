<?php
   session_start();
   $myid = $_POST['email'];
   $mypw = $_POST['pswd'];

  //get from DB
   if(isset($_POST['submit']) && $myid && $mypw){
    require("p0.php");

      $reqStr="select checkID('$myid' ,'$mypw')";

      if ($stmt = $dbhost->query($reqStr)){
        while ($event = $stmt->fetch()) {
            if($event[0]==1){
              require_once("login.php");
            }
            elseif($event[0]== -1){
                print "<h3>Certification Failed. 認証失敗.</h3>";
                $dbhost = null;
            }
    }
}
   }else{
    print "<h3 class='text-danger'>警告：認証入力していない。</h3>";
}
?>