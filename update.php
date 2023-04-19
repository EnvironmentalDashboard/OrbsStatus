<?php
if(count($_POST) == 0){
  header('Location:index.php');
}
?>
<?php
  const SUCCESS = 1;
  const FAILED = 0;
  
  function pingAddress($ip){
    $pingCommand = "ping $ip -c 3";
    $pingresult = exec($pingCommand, $outcome, $status);
    
    /* IN PING WE RECEIVE 0 WHEN IT IS SUCESS AND 0 WHEN IT FAILED */
    if (0 == $status) {
      $status = SUCCESS;
    } else {
      $status = FAILED;
    }
    // echo "pingCommand - $pingCommand<br>";
    // echo "status - $status<br>";
    // echo "pingresult - $pingresult<br>";
    // echo "outcome - $outcome<br>";
    // print_r($outcome);
    // echo "The IP address, $ip, is  $status<br>";
    // echo "------------------";
    $command = $_POST['command'];
    $ip_address  = $_POST['ip_address'];

    $command =  "bash -c \"exec nohup setsid echo '$command' | timeout 15s netcat $ip_address 9950 > '$ip_address' 2&>1&\""; 

    /* update database status */
    require 'db.php';
    $con = "mysql:host={$host};dbname={$dbname};charset=utf8;port=3306";
    try {
      $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    $updatetest = "UPDATE orbs SET testing=$status WHERE `ip` = INET_ATON('$ip_address')";
    $db->query($updatetest);
    /* end status updated */

    /* send response back to index page */
    header('Content-Type: application/json; charset=utf-8');
    if($status == SUCCESS){
      $result = exec($command);
      echo json_encode(["status" => $status, "message" => "Orb is connected", $command]);
    }else{
      echo json_encode(["status" => $status, "message" => "Orb is disconnected", $command]);
    }
    
  }

  pingAddress($ip_address);  
  
  ?>
