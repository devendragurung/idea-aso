<!DOCTYPE html>
<html>
<head>
<title>table wine </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-3">

<?php
  header("Content-Type: text/html; charset=UTF-8");
  require("p0.php");
  if(isset($_POST['sellist1'])){
      $sel1 = $_POST['sellist1'];
  }else{
  $sel1= "Lecture";
      }

//table thead
$reqStr1="desc ".$sel1;
//$reqStr1="desc idpw";
$sth = $dbhost->prepare($reqStr1);
$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

print "<table class='table caption-top'><caption class='text-center display-6'>".$sel1."</caption><thead><tr>";
foreach ($result as $i => $value) {
    print "<th>".$result[$i]."</th>";
}
print  "</thead><tbody>";

$reqStr2="select * from ".$sel1;

   if ($stmt = $dbhost->query($reqStr2)) {
    // 1行ずつ結果の出力　配列に
      while ($event = $stmt->fetch()) {
          print "<tr>";
          for($i=0;$i<count($event)/2;$i++){
            print "<td>".$event[$i]."</td>";
          } 
     print "</tr>"; 
  }
print "<tbody></table>" ;    
 }

$dbhost->close();
 ?>
 </div>

</body>
</html>