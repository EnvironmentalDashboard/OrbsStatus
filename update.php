<html>
<body>

  <?php
  $newvalue=substr($_POST["relval"],0,1);
  $newvalueuse=(floatval($newvalue)/4)*100;
  $iduse=substr($_POST["relval"],1,5);
  require 'db.php';
  $con = "mysql:host={$host};dbname={$dbname};charset=utf8;port=3306";
  try {
    $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }
  catch (PDOException $e) { die($e->getMessage()); }
  $updateinput='UPDATE relative_values SET relative_value='.$newvalue.' WHERE id='.$iduse;
  echo $updateinput;
  //$db->query($updateinput);
  ?>

</body>
</html>
