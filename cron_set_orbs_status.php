<?php
    require 'db.php';
    require 'ping-ip-address.php';

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
    
    $now = date('Y-m-d H:i:s');
    $oneMinuteLater = strtotime($now) + 60;

    while (strtotime(date('Y-m-d H:i:s')) < $oneMinuteLater) {
        $query = "SELECT inet_ntoa(orb_ip) as ip_address, testing_date_time, command FROM orb_status_log WHERE tested = 0";
        $result = $db->query($query);
        // echo '$result->rowCount()';
        // echo $result->rowCount();
        // die($query);
        $status = FAILED;
        $updateOrbsStatusQuery = '';
        $updatetest = '';

        foreach ($result as $row) {
            $command = $row['command'];
            $ip_address = $row['ip_address'];
            $status = pingIpAddress($ip_address);
            $timestampQuery = '';
            if ($status == SUCCESS) {
                $command =  "bash -c \"exec nohup setsid echo '$command' | timeout 15s netcat $ip_address 9950\""; 
                $result = exec($command);
                $timestampQuery = ", last_connectioned_on = CURRENT_TIMESTAMP";
            }
    
            $updateOrbsStatusQuery ="UPDATE orb_status_log SET tested=1 WHERE `orb_ip` = inet_aton('$ip_address') AND tested=0;";
            $db->query($updateOrbsStatusQuery);

            $updatetest = "UPDATE orbs SET testing=$status $timestampQuery WHERE `ip` = inet_aton('$ip_address')";
            $db->query($updatetest);
        }

        echo "<br/>", strtotime(date('Y-m-d H:i:s')) < $oneMinuteLater;
        echo "<br/>", "c ", date('Y-m-d H:i:s') , " d",  date('Y-m-d H:i:s',$oneMinuteLater);
        echo $updatetest, $updateOrbsStatusQuery;
        sleep(10); // sleep for 10 second to run the process again
    }
  ?>
