<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dispatch</title>
		<link href="clementstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
		<div class="container">
		<?php require 'nav.php';?>
			
		<?php //if post back
	if (isset($_POST["btnDispatch"]))
	{
		require_once 'db_config.php';
		
		//create database connection
		$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
		//check connection
		if ($mysqli->connect_errno)
		{
			die("Unable to connect to database(MySql): ".$mysqli->connect_errno);
		}
		
		$patrolcarDispatched = $_POST["chkPatrolcar"]; // array of patrolcar being dispatched from post back
		$numOfPatrolcarDispatched = count($patrolcarDispatched);
		
		//insert new incident
		$incidentStatus;
		if ($numOfPatrolcarDispatched > 0)
		{
			$incidentStatus='2'; //incident status to be set as Dispatched
		}
		else
		{
			$incidentStatus='1'; //incident status to be set as Pending
		}
		
		$sql = "INSERT INTO incident (callerName,phoneNumber,incidentLocation,incidentTypeId,incidentDesc,incidentStatusId)
		VALUES (?, ?, ?, ?, ?, ?)";
		
		if(!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
		
		if(!$stmt->bind_param('ssssss', $_POST['cname'],
							 			$_POST['cno'],
							  			$_POST['locate'],
							  			$_POST['incidentType'],
							  			$_POST['desc'],
							 			$incidentStatus))
		
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
		
		if (!$stmt->execute())
		{
			die("Insert incident table failed: ".$stmt->errno);
		}
		
		// retrieve incident_id for the newly inserted incident
		$incidentId=mysqli_insert_id($mysqli);
		
		//update patrolcar status table and add into dispatch table
		for($i=0; $i < $numOfPatrolcarDispatched; $i++)
			
	{
		// update patrol car status
		$sql = "UPDATE patrolcar SET patrolcarStatusId ='1' WHERE patrolcarId = ?";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if (!$stmt->execute())
		{
			die("Update patrolcar_status table failed: ".$stmt->errno);
		}
			
		//insert dispatch data
		$sql = "INSERT INTO dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
			
		if (!$stmt->bind_param('ss', $incidentId,
							  		$patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if(!$stmt->execute())
		{
			die("Insert dispatch table failed: ".$stmt->errno);
		}
	}
		
		$stmt->close();
		
		$mysqli->close();
	} ?>
			
	<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
	<table width="960" height="500" border="1" align="center" cellpadding="12" cellspacing="0">	
		<tr>
		<td colspan="2" align="center"><strong>Incident Detail</strong></td>
		</tr>
		<tr>
		<td>Caller's Name :</td>
		<td><?php echo $_POST['cname'] ?><input type="hidden" name="cname" Id="cname" value="<?php echo $_POST['cname'] ?>"</td>
		</tr>
		<tr>
		<td>Contact No :</td>
		<td><?php echo $_POST['cno']?> <input type="hidden" name="cno" id="cno" value="<?php echo $_POST['cno'] ?>"</td>
		</tr>
		<tr>
		<td>Location :</td>
		<td><?php echo $_POST['locate']?> <input type="hidden" name="locate" id="locate" value="<?php echo $_POST['locate'] ?>"</td>
		</tr>
		<tr>
		<td>Incident Type :</td>
		<td><?php echo $_POST['incidentType']?> <input type="hidden" name="incidentType" id="incidentType" value="<?php echo $_POST['incidentType'] ?>"</td>
		</tr>
		<tr>
		<td>Description :</td>
		<td><textarea name="desc" cols="45"
					  rows="5" readonly id="desc"><?php echo $_POST['desc'] ?></textarea>
			<input name="desc" type="hidden"
				   id="desc" value="<?php echo $_POST['desc'] ?>"</td>
		</tr>
		
	
	
		</table>
		
		
		<?php 
// connect to a database
require_once'db_config.php';
	
// create database connection
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// check connection
if($mysqli->connect_errno) 
{
	die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

// retrieve from patrolcar table those patrol cars that are 2:Patrol or 3:Free
$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status
ON patrolcar.patrolcarStatusId=patrolcar_status.statusId
WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";

	if (!($stmt = $mysqli->prepare($sql)))
	{
		die("Prepare failed: ".$mysqli->errno);
	}
	if (!$stmt->execute())
	{
		die("Cannot run SQL command: ".$stmt->errno);
	}
	if(!($resultset = $stmt->get_result()))
	{
		die("No data in resultset: ".$stmt->errno);
	}
	
	$patrolcarArray; // an array variable
	
	while  ($row = $resultset->fetch_assoc()) 
	{
		$patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
	}
	
	$stmt->close();
	$resultset->close();
	$mysqli->close();
	?>
		
		<br><br>
        <table align="center" width="70%" height="300" border="1" align="center" cellspacing="0" cellpadding="12"> 
            <tr> 
      <td colspan="3" align="center">Dispatch Patrolcar Panel</tr>
            
        <?php 
            foreach($patrolcarArray as $key=>$value){ 
?> 
    <tr> 
    <td><input type="checkbox" name="chkPatrolcar[]" value="<?php echo $key?>">
    </td> 
    <td><?php echo $key ?></td>
    <td><?php echo $value ?></td>
        
    </tr> <?php } ?> 
    <tr>
    <td><input type="reset" name="btnCancel" value="Reset" > </td>
    <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnDispatch" value="Dispatch" >
</td>
        </tr>
        </table>
	</form>
	</div>
	      <!-- Site footer -->
    <footer class="site-footer">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <h6>About</h6>
            <p class="text-justify">Police Emergency Service System is an web-based system to help the Police Radio Division to handle all emergency calls for police service and dispatching of police patrol to scenes of incident.</p>
          </div>
        <hr>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-sm-6 col-xs-12">
            <p class="copyright-text">Copyright &copy; 2020 All Rights Reserved by 
         <a href="#">Clement</a>.
            </p>
          </div>
        </div>
      </div>
		  
</body>
</html>
