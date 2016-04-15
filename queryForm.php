<?php 
ini_set('max_execution_time', 3600);
ini_set('memory_limit', '1024M');
if($_SERVER['SERVER_ADDR']=='::1')
{
	$con 	= mysqli_connect("localhost","root","") or die('Error in connecting MySQL');
	$db_con = mysqli_select_db($con,"intermingl");
}
else
{
	$mysql_server = 'mgc-tilt.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
	$mysql_user = 'TiltUser';
	$mysql_pass = 'TiltUser123';
	$mysql_name = 'ebdb';
	//echo "here";die();
	$con	= mysqli_connect($mysql_server,$mysql_user,$mysql_pass) or die('Error in connecting MySQL');
	$db_con = mysqli_select_db($con,$mysql_name) or die('Error in selecting databse MySQL');
//echo $db_con;
	//die();
} 
global $globalDbManager;
//$this->dbConnect  = $globalDbManager->dbConnect;
$adminValue	=	0;
if(isset($_POST['querySubmit'])){
	//$sql = mysql_real_escape_string(htmlspecialchars($getArray['query']));
	$sql = htmlspecialchars($_POST['query']);
	$adminValue	=	0;
			
	if($adminValue != 1 &&	$sql != '')	{
		if(stristr($sql,"update")	||	stristr($sql,"delete") || stristr($sql,"truncate") || stristr($sql,"drop")){
			header('location:queryForm.php?cs=1&redirect=1');
			die();
		}
	}
	echo "<br>==================>".$sql;
	echo "<br>=====start============>".$ms = microtime(true);
	//mysql_query($sql);
	


	$sql = str_replace(array("\r\n", "\n", "\r"), ' ',$sql);
	//echo "<br>=====start============>".date('Y-m-d H:i:s');
	$retval = mysqli_query( $con,$sql);
	//echo "<br>======end============>".date('Y-m-d H:i:s');
	echo "<br>=====end============>".$ms = microtime(true);
	$ms = microtime(true) - $ms;
	echo "<br>====query time==========".$ms.' secs'; //seconds
	echo "<br>====query time==========".($ms * 1000).' millisecs'; //millseconds
	if(!$retval)
	{
	  die('<br>Could not connect table: ' . mysqli_error($con));
	}
	if(stripos($sql,"insert")  === false  &&  stripos($sql,"update")  === false &&  stripos($sql,"delete")  === false)
	{
		$group_lists = array();
		while($rows = mysqli_fetch_object($retval)){
		$group_lists[] = $rows;
		}
		echo'<pre>';print_r($group_lists);echo'</pre>';
		$mss = microtime(true) - $ms;
		echo "<br>====query time=====end=====".$mss.' secs'; //seconds
		echo "<br>====query time=====end=====".($mss * 1000).' millisecs'; //millseconds
	}
	else
	{
		echo  "Query execution is success";
	}
	//return $retval;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Form</title>
	<style type="text/css">
		.bor {
			border:1px solid black;
		}
	</style>
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center">
<tr><td height="10">&nbsp;</td></tr>
<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" width="97%" align="center" class="bor">
			<tr><td height="10">&nbsp;</td></tr>
			<tr><td align="center"><h2>Excute Query</h2></td></tr>
			<tr>
				<td>
					<form onsubmit='return validateContent(<?php echo $adminValue;?>);' name="excutequery" id="excutequery" action="queryForm.php" method="post" enctype="multipart/form-data">
						<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
							<tr><td height="10">&nbsp;</td></tr>
							<tr>
								<td width="45%">Query</td>
								<td width="5%" align="center">:</td>
								<td width="45%"><textarea rows="10" cols="50" name="query" id="query"></textarea></td>
							</tr>
							<tr><td height="10">&nbsp;</td></tr>
							<tr>
								<td colspan="2">&nbsp;</td>
								<td>
									<input type="hidden" name="action" id="action" value="excuteQuery" />
									<input type="submit" name="querySubmit" id="querySubmit" value="Submit" tabindex="1" />
								</td>
							</tr>
							<tr><td height="10">&nbsp;</td></tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
	</td>
</tr>


<tr><td height="10">&nbsp;</td></tr>

</table>
<script>
function validateContent(option)	{
	var url			=	location.search;
	var actualurl	=	window.location.href;
	var query		=	document.getElementById("query").value;
	query			=	query.toLowerCase();
	var updateCheck	=	query.indexOf('update');
	var adminOption	=	0; 
	
	if(query.indexOf('update') == -1	&&	query.indexOf('delete') == -1	&&	query.indexOf('truncate') == -1	&&	query.indexOf('drop') == -1)	{
		return true;
	}
	else if(option == 1){
		return true;
	}
	
	/*var splitUrl	=	url.split('?');
	if(typeof splitUrl[1] != 'undefined')	{
		var splitParams	=	splitUrl[1].split('admin=');
		var finalValue	=	splitParams[1].split('&');
		adminOption	=	finalValue[0];
		
	}*/
	// document.getElementById('query').style.border="1px solid red";
	return false;
}
</script>
</body>
</html>