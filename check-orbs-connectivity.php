<?php
  require 'db.php';
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
  $updateOrbsStatusQuery = '';
  function pingAddress($ip_address){

    $pingCommand = "ping $ip_address -c 2";
    $pingresult = exec($pingCommand, $outcome, $status);
    
    /* IN PING WE RECEIVE 0 WHEN IT IS SUCESS AND 0 WHEN IT FAILED */
    $timestampQuery = '';
    if (0 == $status) {
      $status = SUCCESS;
      $timestampQuery = ", last_connectioned_on = CURRENT_TIMESTAMP";
    } else {
      $status = FAILED;
    }
    return "UPDATE orbs SET testing=$status $timestampQuery WHERE `ip` = INET_ATON('$ip_address');";
  }

  $result = $db->query('SELECT name,inet_ntoa(ip) as ip_address ,ip, testing FROM orbs o WHERE o.disabled = 0 ORDER BY `name`');


  foreach ($result as $row) {
    $updateOrbsStatusQuery .= pingAddress($row['ip_address']);
    // $instance = new AsyncOperation($db, $row['ip_address']);
    // $instance->start();
  }

  if(strlen($updateOrbsStatusQuery)){
    try {
      $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    $db->query($updateOrbsStatusQuery);
  }
  ?>
