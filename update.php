<?php
if(count($_POST) == 0){
  header('Location:index.php');
}
?>
<?php
  include "./ping-ip-address.php";

  const SUCCESS = 1;
  const FAILED = 0;
  
  function checkIp($ip){
    $command = $_POST['command'];
    $ip_address  = $ip ? $ip : $_POST['ip_address'];    
    $status = pingIpAddress($ip_address);
    
    
    // $command =  "bash -c \"exec nohup setsid echo '$command' | timeout 15s netcat $ip_address 9950 > '$ip_address' 2&>1&\""; 

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
    $response = [
      "status" => $status,
      "command" => $command,
    ];

    if($status == SUCCESS){
      $result = exec($command);
      echo json_encode($response + [
        "message" => "Orb is connected",
        'update_date' => date('m-d-Y h:i A')
      ]);
    }else{
      echo json_encode($response + [
        "message" => "Orb is disconnected",
      ]);
    }
    
  }

  checkIp(null);
  
  ?>
