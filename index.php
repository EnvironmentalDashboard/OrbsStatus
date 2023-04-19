<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/css/style.css">
  <script src="/assets/js/jquery-3.6.4.min.js"></script>
  <title>Test Orbs</title>

</head>

<body>

  <?php
  require 'db.php';
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
      $result = $db->query('SELECT `name`,inet_ntoa(ip),ip, water_uuid, elec_uuid, elec_rvid, water_rvid, testing FROM orbs o WHERE disabled = 0 ORDER BY `name`');

      foreach ($result as $row) {
        $waterrvid = $row['water_rvid'];
        $elecrvid = $row['elec_rvid'];
        $inwater = 'SELECT relative_value FROM relative_values WHERE id=' . $waterrvid;
        $inelec = 'SELECT relative_value FROM relative_values WHERE id=' . $elecrvid;
        $waterrel = $db->query($inwater)->fetchColumn();
        $wgone = false;
        $egone = false;
        if (empty($waterrel) && $waterrel != 0) {
          $waterrel = "N/A";
          $wgone = true;
        }
        $elecrel = $db->query($inelec)->fetchColumn();
        if (empty($elecrel) && $elecrel != 0) {
          $elecrel = "N/A";
          $egone = true;
        }
        $backgroundClass = '';
        if($row['testing'] == SUCCESS){
          $backgroundClass = "connected";
        }else if($row['testing'] == FAILED){
          $backgroundClass = "disconnected";
        }
        
      ?>

        <tr class="<?=$backgroundClass?>">
          <td><?php echo $row['name'] ?></td>
          <td class="ip-address"><?php echo $row['inet_ntoa(ip)'] ?></td>
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
      $(this).text('Loading..')
      $.post('update.php', {
          command: `/E${electricity_rv}W${water_rv}$`,
          ip_address,
        }, (data, textStatus, jqueryXHR) => {
          parentRow.removeClass('connected disconnected');
          if (jqueryXHR.status == 200) {
            if (data.status == <?= SUCCESS ?>) {
              parentRow.addClass('connected')
              $(this).addClass('btn-success').text('Success')
            } else {
              $(this).addClass('btn-danger').text('Failed')
              parentRow.addClass('disconnected')
            }
          } else {
            $(this).text('Test')
          }
        }).done(() => {})
        .fail(() => {
          $(this).addClass('btn-danger').text('Failed')
        })
    })
  </script>
</body>

</html>