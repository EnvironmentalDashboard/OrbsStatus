<html>
<body>

  <?php
  $ipuse = $_POST["change"];
  require 'db.php';
  $con = "mysql:host={$host};dbname={$dbname};charset=utf8;port=3306";
  try {
    $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }
  catch (PDOException $e) { die($e->getMessage()); }
  $updatetest='UPDATE orbs SET testing=1 WHERE ip="'.$ipuse.'"';;
  $db->query($updatetest);
  ?>

</body>
</html>
