<?php
require 'db.php';
$con = "mysql:host={$host};dbname={$dbname};charset=utf8;port=3306";
try {
  $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (PDOException $e) { die($e->getMessage()); }

?>
<h1 style="color: Blue; text-align: center;"> Orb Information  <button type="submit" form="change" value="Submit">Submit Changes</button> </h1>
<p><table align="center" style="border: 1px solid black; ">
  <tr style="text-align:left;">
    <th>Orb Name</th>
    <th>IP Number</th>
    <th>Water UUID</th>
    <th>Electric UUID</th>
    <th>Electric RVID</th>
    <th>Water RVID</th>
    <th>Relative Value(0-100)</th>
    <th>Relative Value(0-4)</th>
    <th>Value Change Form </th>

  </tr>
  <?php foreach ($db->query('SELECT name,inet_ntoa(ip),water_uuid, elec_uuid, elec_rvid, water_rvid, relative_value FROM orbs o inner join relative_values rv on o.water_rvid=rv.id') as $row) {?>

  <tr>
    <td><?php echo $row['name'] ?></td>
    <td><?php echo $row['inet_ntoa(ip)'] ?></td>
    <td><?php echo $row['water_uuid'] ?></td>
    <td><?php echo $row['elec_uuid'] ?></td>
    <td><?php echo $row['elec_rvid'] ?></td>
    <td><?php echo $row['water_rvid'] ?></td>
    <td><?php echo $row['relative_value'] ?></td>
    <td><?php if($row['relative_value']==0){
      echo "0";
    }
    if($row['relative_value']>0 and $row['relative_value']<=25){
      echo "1";
    }
    if($row['relative_value']>25 and $row['relative_value']<=50){
      echo "2";
    }
    if($row['relative_value']>50 and $row['relative_value']<=75){
      echo "3";
    }
    if($row['relative_value']>75 and $row['relative_value']<=100){
      echo "4";
    }
    ?>
  </td>
<td> <form name="change" method="post" action="">
  <select name="Relative Value Change">
  <option value="0">0</option>
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4</option>
  </select>
</form>
</td>
  </tr>
<?php } ?>
</table></p>
