<!doctype html>
<html>
<head>
<meta charset="UTF-8">
	<title>Police Emergency Service System</title>
	<link href="clementstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="container">
<?php require 'nav.php';?> <!--menu bar -->
<?php require 'db_config.php'; //database details

//create database connection
$mysqli = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
//check connection
if ($mysqli->connect_errno)
	{
	die("Unable to connect to MySQL: ".$mysqli->connect_errno);
	}
	
$sql = "SELECT * FROM incidenttype";
//Test the sql command in $sql, if got error display error message and exit.
if (!($stmt = $mysqli->prepare($sql)))
	{
	die("The command have failed: ".$mysqli->errno);
	}
//Checking command
if (!$stmt->execute())
	{
	die("Cannot run database(mysql) command: ".$stmt->errno);
	}
//Check any data in resultset
if (!($resultset = $stmt->get_result()))
	{
	die("There is no data in resultset: ".$stmt->errno);
	}
	
$incidentType; //an array variable
	
while ($row =$resultset->fetch_assoc())
	{
	//Create an assoicative array of $incidentType {incident_type_id, incident_type_desc}
	$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
	}
	
$stmt->close();

$resultset->close();

$mysqli->close();
?>
<form class="fields" name="loginCall" method="post" action="dispatch.php" onSubmit="return validation();">
	<table width="960" height="500" border="1" align="center" cellpadding="12" cellspacing="0">
	<tr>
	<td width="20%">Name of Caller:</td>
	<td width="50%"><input type="text" name="cname" id="cname" placeholder="Please enter the name" pattern="[A-Za-z]{3,}"></td>
	</tr>
	<tr>
	<td width="20%">Contact Number:</td>
	<td width="50%"><input type="text" name="cno" id="cno" placeholder="Please enter the number" pattern="[0-9]{8}"></td>
	</tr>
	<tr>
	<td width="20%">Location:</td>
	<td width="50%"><input type="text" name="locate" id="locate" placeholder="Please enter the location"></td>
	</tr>
	<tr>
	<td width="20%">Incident Type:</td>
	<td width="50%"><select name="incidentType" id="incidentType">
		<option disabled selected value="">--Select an option--</option>
		<?php foreach($incidentType as $key=> $value) {?>
		<option value="<?php echo $key ?> " >
		<?php echo $value ?> </option>
		<?php } ?>
	</select>
		
	</td>
	</tr>
		
	<tr>
	<td width="20%">Description:</td>
	<td width="50%"><textarea name="desc" id="desc" cols="60" rows="6" placeholder="Please enter your description"></textarea></td>
	</tr>
	</table>
	<tr>
	<table width="40%" border="0" align="center" cellpadding="5" cellspacing="5">
		<td><p align="center"><input type="reset" name="resetProcess" id="resetProcess" value="Reset"</p></td>	
	<td><p align="center"><input type='submit' name="btnProcess" id="btnProcess" value="Process Call"</p></td>
	</tr>
	</table>
</form>
</fieldset>

    <script>
        const navIcon = document.querySelector(".nav-icon");
        const nav = document.querySelector("nav");

        navIcon.onclick = function () {
            nav.classList.toggle('show');
        }
		
		
		function validation()
{
	var name = document.forms ["loginCall"]["cname"].value;
	var number = document.forms ["loginCall"]["cno"].value;
	var location = document.forms ["loginCall"]["loate"].value;
	var incident = document.forms ["loginCall"]["incidentType"].value;
	
	if (name == "")
	{
		alert("Please enter your name in alphabetical order.");
		return false;
	}
	
	if (number == "")
	{
		alert("Please key in at least 8 digt number.");
		return false;
	}
	
	if (location == "")
	{
		alert("Please key in the location.");
		return false;
	}
	
	if (incident == "")
	{
		alert("Please select the incident type."); 
		return false;
	}
	
	return true;
	}
		
	function validation() {
		var x = document.forms["loginCall"]['desc'].value;
		if(x == "") {
			alert("Description Must Be Filled Out!");
			return false;
		}
	}
    </script>
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
</footer>
</body>
</html>
