<?php error_reporting(E_ALL);
	//session_destroy();
	session_start();
	global $tableName;
	global $fieldnames;
	global $result;
	global $perpage;
if($_SERVER['SERVER_ADDR']=='172.21.4.104')
{
	$con 	=	mysqli_connect("localhost","root","") or die('Error in connecting MySQL');
	$db_con = 	mysqli_select_db($con,"intermingl");
}
else
{
	$mysql_server = 'aa1vo4uao1prtm6.cslj5bbd5oqi.us-west-2.rds.amazonaws.com';
	$mysql_user = 'tiltbetadbuser';
	$mysql_pass = 'tb2db0us1er4';
	$mysql_name = 'ebdb';
	$con = mysqli_connect($mysql_server,$mysql_user,$mysql_pass);
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$db_con = mysqli_select_db($con,$mysql_name) or die('Error in selecting databse MySQL');//
}
//echo '<pre>=0000==>';print_r($_GET);echo '<===</pre>';
$adminValue	=	0;
	
/*Start:Fetching table names from the connected DB*/
$table_array = array();
$result = mysqli_query($con,"show tables"); // run the query and assign the result to $result
$i = 0;
while($data = mysqli_fetch_array($result)) { // go through each row that was returned in $result
   $table_array[$i] = $data[0];
   $i++;
}
/*End:Fetching table names from the connected DB*/

	function destorySession()
	{
		unset($_SESSION["table"]);
		unset($_SESSION["query"]);
		unset($_SESSION["orderBy"]);
		unset($_SESSION["per_page"]);
		unset($_SESSION["total"]);
	}

	function getFieldName($query,$table)
	{
		global $con;
		global $fieldnames;
		$fieldnames = array();
		$field = @mysqli_num_fields($query);
	    for ($i=0; $i < $field; $i++)
		{
			$fieldnames[] = mysqli_fetch_field_direct($query, $i)->name;
			//$fieldnames[] = mysqli_field_name($query, $i);
        }
		/*$result2 = mysql_query($fields);
		if (mysql_num_rows($result2) > 0)
		{
			while ($row = mysql_fetch_assoc($result2))
			{
        		$fieldnames[] = $row['Field'];
    		}
		}*/
		return $fieldnames;
	}

	function paginationControl($start,$page)
	{
		global $con;
		global $result;
		global $perpage;
		global $tableName;
		global $adminValue;
		$_SESSION['per_page'] = $page;
		$total = $_SESSION['total'];
		$tableName = 1;
		$query 		= $_SESSION['query'].' order by id '.$_SESSION['orderBy'].' LIMIT '.$start.','. $page;
		if($adminValue != 1	&&	$query != '')
			checkQuery($query);
		$result 	= mysqli_query($con,$query);
		$fieldnames = getFieldName($result,$_SESSION['table']);
		$_SESSION['pagination'] = 'false';
		if($total > $page)
		{
			$_SESSION['pagination'] = 'true';
			$perpage = ceil($total/$page);
		}
	}

	// BEGIN : To restrict alter queries
	function checkQuery($sql)	{
		if(stristr($sql,"update")	||	stristr($sql,"delete") || stristr($sql,"truncate") || stristr($sql,"drop")){
			header('location:tableForm.php?cs=1&redirect=1');
			die();
		}
	}
	// END : To restrict alter queries
	//echo "<pre>===1111111==>";print_r($_GET);echo "<=====</pre>";
	if(!isset($_POST['submit']) && !isset($_POST['per_page']) && !isset($_POST['cur_page']))
		destorySession();
	$tableName = '';
	if(isset($_POST['Submit']) && ($_POST['table'] != '' || $_POST['query'] != ''))
	{
		$adminValue	=	0;
		if($adminValue != 1	&&	$_POST['query'] != '')	{
			checkQuery($_POST['query']);
		}
		
		if($_POST['query'] == '')
		{
			$query = 'select * from `'.$_POST['table'].'` where 1 ';
			$_SESSION['query']	 = $query;
			$_SESSION['table']	 = $_POST['table'];
			$_SESSION['orderBy'] = $_POST['order'];
			
		}
		else
		{
			$query = $_POST['query'];
			foreach($table_array as $value) {
				if(strpos($query,$value)) {
					$_SESSION['table'] = $value;
				}
			}
			$_SESSION['query'] = $query;
			$_SESSION['orderBy'] = $_POST['order'];
		}
		$count = mysqli_query($con,$query);
		$total = mysqli_affected_rows($con);
		$_SESSION['total'] = $total;
		paginationControl(0, 10);
	}
	if(isset($_POST['per_page']) || isset($_POST['cur_page']) )
	{
		$start	= ($_POST['cur_page']-1) * $_POST['per_page'];
		if($_POST['per_page'] == '')
			$page	=	10;
		else
			$page	=	$_POST['per_page'];
		paginationControl($start,$page);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Form</title>
	<style type="text/css">
		.bor {
			border:1px solid black;
			border-collapse:collapse;
		}
	</style>
</head>

<body>
<h1 align="center">DATA</h1>
<table align="center" class="bor">
	<form onsubmit='return validateContent(<?php echo $adminValue;?>);' name="" id="" action="" method="post">
		<tr>
			<td>Select table:</td>
			<td width="45%">
				<select name="table" id="table" onchange="clearQuery();">
					<option value="">Select</option>
					<?php foreach($table_array as $value) {?>
					<option value="<?php echo $value; ?>" <?php if(isset($_SESSION['table']) && $_SESSION['table'] == $value) echo 'selected="selected"'; else echo '';?>><?php echo ucfirst($value); ?></option>
					<?php } ?>
				</select>
			</td>
			<td valign="top">Query:</td>
			<td><textarea rows="5" cols="35" name="query" id="query" value=""><?php if(isset($_SESSION['query'])) echo $_SESSION['query']; else echo '';?></textarea></td>
		</tr>
		<tr>
			<td>Order By:</td>
			<td>
				<select id="order" name="order">
					<option value="asc" <?php if(isset($_SESSION['table']) && $_SESSION['orderBy'] == 'asc') echo 'selected="selected"'; else echo '';?>>Asc</option>
					<option value="desc" <?php if(isset($_SESSION['table']) && $_SESSION['orderBy'] == 'desc') echo 'selected="selected"'; else echo '';?>>Desc</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center">
				<input type="submit" name="Submit" id="Submit" value="Submit" tabindex="1"/>
			</td>
		</tr>
		</form>
</table>
<h3><span>RECORDS:</span></h3><br>
<table align="center">
	<tr>
	<?php
	if(isset($_POST['per_page']) || isset($_POST['Submit']) && isset($_SESSION['pagination'])	&& $_SESSION['pagination'] =='true' && $tableName != '')
	{
		for($i=1;$i<=$perpage;$i++) { ?>
		<td><a href="javascript:void(0);" onclick="setPagingControlValues('<?php echo $i;?>');"><?php echo $i;?></a></td>
	<?php } } ?>
	</tr>
</table>
<?php
	if($tableName != '') { ?>
	<p align="center"><?php echo strtoupper($_SESSION['table']); ?></p>
	<table border="1" width="50%" align="center" class="bor">
		<tr>
			<?php foreach($fieldnames as $value) { ?>
		       <th><?php echo $value; ?></th> 
		    <?php }	?>
		</tr>
		<?php
		if(!empty($result))	{
		 while($row = mysqli_fetch_array($result)) {?>
		<tr>
			<?php foreach($fieldnames as $value) { ?>
		       <td align="center"><?php echo $row[$value]; ?></td> 
		    <?php }	?>
		</tr>
		<?php } ?>
	</table><br><br>
<?php } } ?>

<?php if(isset($_POST['per_page']) || isset($_POST['Submit']) && isset($_SESSION['pagination'])	&&	$_SESSION['pagination'] =='true' && $tableName != '') { ?>
	<table align="center" width="50%">
		<form name="paging" id="paging" method="post" action="">
		<input type="Hidden" id="cur_page" name="cur_page" value="1">
		<tr>
			<td>Per Page:
			<select id="per_page" name="per_page" onchange="setPerPage(this.value);">
					<option value="5" <?php if($_SESSION['per_page'] == 5) echo 'selected="selected"'; else echo '';?>>5</option>
					<option value="10" <?php if($_SESSION['per_page'] == 10) echo 'selected="selected"'; else echo '';?>>10</option>
					<option value="20" <?php if($_SESSION['per_page'] == 20) echo 'selected="selected"'; else echo '';?>>20</option>
					<option value="30" <?php if($_SESSION['per_page'] == 30) echo 'selected="selected"'; else echo '';?>>30</option>
					<option value="100" <?php if($_SESSION['per_page'] == 100) echo 'selected="selected"'; else echo '';?>>100</option>
					<option value="200" <?php if($_SESSION['per_page'] == 200) echo 'selected="selected"'; else echo '';?>>200</option>
					<option value="500" <?php if($_SESSION['per_page'] == 500) echo 'selected="selected"'; else echo '';?>>500</option>
			</select>
			</td>
		</tr>
		</form>
	</table>
<?php } ?>
</body>
</html>
<script src="jquery-latest.js" type="text/javascript"></script>
<script>
function setPerPage(obj)
{
	$("#per_page").val(obj);
	$("#paging").submit();
}
function setPagingControlValues(cur_page)
{
	$("#cur_page").val(cur_page)
	$("#paging").submit();
}
function clearQuery()
{
	$('#query').val('');
}

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