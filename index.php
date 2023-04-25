<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/jquery-3.6.4.min.js"></script>
  <title>Test Orbs</title>

</head>

<body>

  <?php
  require 'db.php';
  date_default_timezone_set("America/New_York");
  $con = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";
  try {
    $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
  const SUCCESS = 1;
  const FAILED = 0;
  ?>
  <div class="fixed-header">
    <h1 style="color: Green; text-align: center;"> Orb Information</h1>
    <table align="center" class="table">
      <thead class="thead-light">
        <tr>
          <th>Orb Name</th>
          <th>IP Address</th>
          <th>Last Connected On</th>
          <!-- <th>Water UUID</th>
          <th>Electric UUID</th>
          <th>Electric RVID</th>
          <th>Water RVID</th> -->
          <!-- <th>Water Relative Value(0-100)</th> -->
          <!-- <th>Electric Relative Value(0-100)</th> -->
          <th>DB Electric RV(0-4)</th>
          <th>DB Water RV(0-4)</th>
          <th>Select Electric RV(0-4)</th>
          <th>Select Water RV(0-4)</th>
          <th>Test Orb</th>
        </tr>
      </thead>
    </table>
  </div>
  <table align="center" class="table table-content">
    <thead class="thead-light">
      <tr>
        <th>Orb Name</th>
        <th>IP Address</th>
        <th>Last Connected On</th>
        <!-- <th>Water UUID</th>
        <th>Electric UUID</th>
        <th>Electric RVID</th>
        <th>Water RVID</th> -->
        <!-- <th>Water Relative Value(0-100)</th> -->
        <!-- <th>Electric Relative Value(0-100)</th> -->
        <th>DB Electric RV(0-4)</th>
        <th>DB Water RV(0-4)</th>
        <th>Select Electric RV(0-4)</th>
        <th>Select Water RV(0-4)</th>
        <th>Test Orb</th>
      </tr>
    </thead>
    <tbody>

      <?php

      $result = $db->query('SELECT name,inet_ntoa(ip),ip, water_uuid, elec_uuid, elec_rvid, water_rvid, o.last_connectioned_on, r1.relative_value as elec_rv, r2.relative_value as water_rv, testing FROM orbs o LEFT JOIN relative_values r1 ON r1.id = o.elec_rvid LEFT JOIN relative_values r2 ON r2.id = o.water_rvid WHERE o.disabled = 0 ORDER BY `name`');

      foreach ($result as $row) {

        $waterrel = $row['water_rv'];
        $elecrel = $row['elec_rv'];
        $wgone = false;
        $egone = false;
        if (empty($waterrel) && $waterrel != 0) {
          $waterrel = "N/A";
          $wgone = true;
        }
        if (empty($elecrel) && $elecrel != 0) {
          $elecrel = "N/A";
          $egone = true;
        }
        $backgroundClass = '';
        if ($row['testing'] == SUCCESS) {
          $backgroundClass = "connected";
        } else if ($row['testing'] == FAILED) {
          $backgroundClass = "disconnected";
        }
        $date = new DateTimeImmutable($row['last_connectioned_on']);

      ?>

        <tr data-current-status="<?= $row['testing'] ?>" class="<?= $backgroundClass ?>">
          <td><?php echo $row['name'] ?></td>
          <td class="ip-address"><?php echo $row['inet_ntoa(ip)'] ?></td>
          <td class="last-update"><?php echo $row['last_connectioned_on'] ? $date->format('m-d-Y h:i A') : '-' ?></td>
          <!--//CHECK Empty-->
          <!-- <td><?php echo $row['water_uuid'] ?></td>
          <td><?php echo $row['elec_uuid'] ?></td>
          <td><?php echo $row['elec_rvid'] ?></td>
          <td><?php echo $row['water_rvid'] ?></td> -->
          <!-- <td><?php echo $elecrel ?></td> -->
          <td><?php if ($egone) {
                echo "N/A";
              } else {
                echo (int)(($elecrel / 100) * 4);
              }
              ?></td>
          <!-- <td><?php echo $waterrel ?></td> -->
          <td><?php if ($wgone) {
                echo "N/A";
              } else {
                echo (int)(($waterrel / 100) * 4);
              }
              ?></td>
          <td>
            <select name="electricity_rv" class="form-control">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </td>
          <td>
            <select name="water_rv" class="form-control">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </td>
          <td>
            <form method="post" action="update.php">
              <button class="btn btn-primary check-status" type="submit" name="change" value="<?php echo $row['inet_ntoa(ip)'] ?>">
                Test
              </button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <script>
    /* set fixed header width */
    $('.table-content thead th').each((index, item) => {
      $(`table:nth(0) thead th:nth(${index})`)[0].width = item.getBoundingClientRect().width
      $(`.table-content td:nth(${index})`)[0].width = item.getBoundingClientRect().width
    });
    $('.table-content thead').hide();
    /* end */
    $('.check-status').on('click', function(event) {
      event.preventDefault()
      const parentRow = $(this).parents('tr');
      const electricity_rv = parentRow.find("[name=electricity_rv]").val();
      const water_rv = parentRow.find("[name=water_rv]").val();
      const ip_address = parentRow.find('td.ip-address').text();
      
      /* disable all button while the process is executing  */
      $('button.check-status').attr('disabled', true);
      $(this).removeClass('btn-danger btn-success')
      $(this).text('Loading..')

      parentRow.data('current-status', 0);

      const testingDate = Math.floor(new Date().getTime() / 1000)

      $.post('update.php', {
          command: `/E${electricity_rv}W${water_rv}$`,
          ip_address,
          testingDate
        }, (data, textStatus, jqueryXHR) => {
          parentRow.removeClass('connected disconnected');

          if (jqueryXHR.status == 200) {
            let counter = 0;
            /* once we request to check the status, then we've to continues check the backend status for every 2 seconds */
            let intervalProcess;
            intervalProcess = setInterval(() => {
              updateStatus(ip_address, testingDate, parentRow, intervalProcess)
              counter++;
              /* clear interval after 5 attemps */
              if (counter == 5) {
                clearIntervalProcess(intervalProcess, parentRow, data)
              }
            }, 3000);
          } else {
            $(this).text('Test')
          }
        }).done(() => {


        })
        .fail((data) => {
          $(this).addClass('btn-danger').text('Failed')
        })
    });


    function updateStatus(ip_address, testingDate, parentRow, intervalProcess) {
      $.post('get_orbs_status.php', {
        ip_address,
        testingDate
      }, (data, textStatus, jqueryXHR) => {
        if (jqueryXHR.status == 200 && parentRow.data('current-status') != data.current_status) {
          /* clear interval even before 5 attemps if we get the result */
          clearIntervalProcess(intervalProcess, parentRow, data)
        }
      }).fail((data) => {
        button.addClass('btn-danger').text('Failed')
        $('button.check-status').attr('disabled', false);
        console.log('api getting failed')
      })
    }

    function clearIntervalProcess(intervalProcess, parentRow, data) {
      clearInterval(intervalProcess);
      const button = parentRow.find('.check-status');
      if (data.current_status == <?= SUCCESS ?>) {
        parentRow.addClass('connected')
        button.addClass('btn-success').text('Success')
      } else {
        parentRow.addClass('disconnected')
        button.addClass('btn-danger').text('Failed')
      }
      if (data.update_date) {
        parentRow.find('td.last-update').text(data.update_date);
      }
      /* set back the button title after getting result */
      button.addClass('btn-primary').text('Test')
      /* enable all button  */
      $('button.check-status').attr('disabled', false);
    }
  </script>
</body>

</html>