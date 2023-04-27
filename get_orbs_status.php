<?php
    if(count($_POST) == 0){
    header('Location:index.php');
    }
?>
<?php
    require 'db.php';
    require 'ping-ip-address.php';
    $testingDate = date('Y-m-d H:i', $_POST['testingDate']);
    $ip_address  = $ip ? $ip : $_POST['ip_address'];
    date_default_timezone_set("America/New_York");

    set_time_limit(-1);

    const SUCCESS = 1;
    const FAILED = 0;

    $con = "mysql:host={$host};dbname={$dbname};charset=utf8;port={$port}";
    try {
        $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    
    $query = "SELECT tested FROM orb_status_log WHERE orb_ip = inet_aton('$ip_address') and testing_date_time = '$testingDate' AND tested=1";
    $result = $db->query($query);
    
    $status = FAILED;
    $date = '';

    echo $result->rowCount();
    exit;

    if($result->rowCount() != 0){
      $query = "SELECT last_connectioned_on, testing FROM orbs o WHERE ip = inet_aton('$ip_address')";
      $result = $db->query($query);
      
      foreach ($result as $row) {
        $status = $row['testing'];
        $date = new DateTimeImmutable($row['last_connectioned_on']);
        $date = $date->format('m-d-Y h:i A');
      }
    }
     /* send response back to index page */
    header('Content-Type: application/json; charset=utf-8');
    $response = [
      "current_status" => $status,
    ];
    if($date){
      $response['update_date'] = $date;
    }
    if($status == SUCCESS){
      echo json_encode($response + [
        "message" => "Orb is connected",
      ]);
    }else{
      echo json_encode($response + [
        "message" => "Orb is disconnected",
      ]);
    }
    
  ?>
