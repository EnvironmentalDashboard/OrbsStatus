<?php
    if(count($_POST) == 0){
    header('Location:index.php');
    }
?>
<?php
    require 'db.php';
    require './ping-ip-address.php';
    $testingDate = date('Y-m-d H:i', $_POST['testingDate']);
    $ip_address  = $ip ? $ip : $_POST['ip_address'];

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
    $query = "SELECT tested FROM orb_status_log WHERE orb_ip = inet_aton('$ip_address') and testing_date_time = '$testingDate'";
    $result = $db->query($query);
    
    $status = FAILED;
    foreach ($result as $row) {
      $status = $row['tested'];
    }
     /* send response back to index page */
    header('Content-Type: application/json; charset=utf-8');
    $response = [
      "current_status" => $status,
    ];

    if($status == SUCCESS){
      echo json_encode($response + [
        "message" => "Orb is connected",
        'update_date' => date('m-d-Y h:i A')
      ]);
    }else{
      echo json_encode($response + [
        "message" => "Orb is disconnected",
      ]);
    }
    
  ?>
