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
<h1 style="color: Blue; text-align: center;"> Orb Information</h1>

<p><table align="center" style="border: 1px solid black; ">
  <tr style="text-align:left;">
    <th>Orb Name</th>
    <th>IP Number</th>
    <th>Water UUID</th>
    <th>Electric UUID</th>
    <th>Electric RVID</th>
    <th>Water RVID</th>
    <th>Water Relative Value(0-100)</th>
    <th>Water Relative Value(0-4)</th>
    <th>Electric Relative Value(0-100)</th>
    <th>Electric Relative Value(0-4)</th>
    <th>Test Orb</th>

  </tr>

<?php
  foreach ($db->query('SELECT name,inet_ntoa(ip),ip, water_uuid, elec_uuid, elec_rvid, water_rvid FROM orbs o') as $row) {
  $waterrvid= $row['water_rvid'];
  $elecrvid= $row['elec_rvid'];
  $inwater='SELECT relative_value FROM relative_values WHERE id='.$waterrvid;
  $inelec='SELECT relative_value FROM relative_values WHERE id='.$elecrvid;
  $waterrel=$db->query($inwater)->fetchColumn();
  $wgone=false;
  $egone=false;
  if(empty($waterrel)){
    $waterrel="N/A";
    $wgone=true;
  }
  $elecrel=$db->query($inelec)->fetchColumn();
  if(empty($elecrel)){
    $elecrel="N/A";
    $egone=true;
  }
 ?>

  <tr>
    <td><?php echo $row['name'] ?></td>
    <td><?php echo $row['inet_ntoa(ip)'] ?></td>
    <td><?php echo $row['water_uuid']
    ?></td>
    <td><?php echo $row['elec_uuid']
    //CHECK Empty>
    ?></td>
    <td><?php echo $row['elec_rvid'] ?></td>
    <td><?php echo $row['water_rvid'] ?></td>
    <td><?php echo $waterrel?></td>
    <td><?php if($wgone){
      echo "N/A";
    }
    else{
      echo (int)(($waterrel/100)*4);
    }
    ?></td>

<td><?php echo $elecrel?></td>
<td><?php if($egone){
  echo "N/A";
}
else{
  echo (int)(($elecrel/100)*4);
}
?></td>
<td>
<form method="post" action="update.php">
<input type="submit" name="change" value=<?php echo $row['ip'] ?> >
</form>
</td>
  </tr>
<?php } ?>
</table></p>
