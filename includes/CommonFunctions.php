<?php
ini_set('default_encoding','utf-8');
function admin_login_check()
{
    if(!isset($_SESSION['mgc_admin_user_name'])){
		header('location:index.php');
		die();
    }
}
function developer_login_check(){
	if(!isset($_SESSION["tilt_developer_id"]) || $_SESSION["tilt_developer_id"] == '')
		{ header('location: index.php'); die();}
}
function getCurrPage()
{
	$page = substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1);
	$page=explode('?',$page);
	if(is_array($page))
		$page=$page[0];
	return $page;
}
function displayText($text, $length) {
	if (strlen($text) > $length) return strip_tags(substr($text, 0, $length)).' ...'; else return $text;
}

/********************************************************
  * Function Name: escapeSpecialCharacters
  * Purpose: Escapes special characters in a string for use in an SQL statement
  * $data   - array or text
  *******************************************************/
function escapeSpecialCharacters($data)
{
	global $con;
	
	if (get_magic_quotes_gpc())
		return $data; //No need to escape data if magic quotes is turned on
	$data = is_array($data) ? array_map('escapeSpecialCharacters', $data) : addslashes($data);
    return $data;
}
/********************************************************
  * Function Name: unEscapeSpecialCharacters
  * Purpose: UnEscapes special characters in a string for use in an SQL statement
  * $data   - array or text
  *******************************************************/
function unEscapeSpecialCharacters($data)
{
	$data = is_array($data) ? array_map('unEscapeSpecialCharacters', $data) :stripslashes($data);
    return $data;
}
function sendMail($mailContentArray,$type)
{
	error_reporting(E_ALL);
	
	if(is_array($mailContentArray))
	{
		$heardFrom		= 	'';
		$message		=	'';
		$from 	  		=   $mailContentArray['from'];
		$to   		    =   $mailContentArray['toemail'];
		$subject		= 	$mailContentArray['subject'];
		$sitelinkpath	=	GAME_SITE_PATH.'/';
		$filename       = 	GAME_ABS_PATH.'/webresources/mail_content/'.$mailContentArray['fileName'];
		$mailData 		= 	file_get_contents($filename);
		$filearray 		= 	explode('/',$mailContentArray['fileName']);
		$typearray 		= 	end($filearray);
		$typeextn 		= 	explode('.',$typearray);
		switch($type)
		{
		    case 1:
				//User forgot password
				$mailData			=	str_replace('{EMAIL}', $mailContentArray['email'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}',  $mailContentArray['password'], $mailData);
				break;
			case 2:
				//User Registeration 
				$mailData 			=	str_replace('{EMAIL}', $mailContentArray['toemail'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}', $mailContentArray['password'], $mailData);
				break;
			case 3:
				//Admin forgot password
				$mailData 			=	str_replace('{NAME}', ucfirst($mailContentArray['name']), $mailData);
				$mailData 			=	str_replace('{EMAIL}', $mailContentArray['toemail'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}',  $mailContentArray['password'], $mailData);
				break;	
			case 4:
				//Admin brand Approve
				$mailData 			=	str_replace('{BRAND}', ucfirst($mailContentArray['brand']), $mailData);
				$mailData 			=	str_replace('{NAME}', $mailContentArray['name'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}',  $mailContentArray['password'], $mailData);
				$mailData 			=	str_replace('{BRAND_SITE}',  $mailContentArray['brandSite'], $mailData);
				//die();
				break;
			case 5:
				//Developer Registeration 
				$mailData 			=	str_replace('{EMAIL}', $mailContentArray['toemail'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}', $mailContentArray['password'], $mailData);
				$mailData 			=	str_replace('{VERIFY_LINK}',  $mailContentArray['verifylink'], $mailData);
				break;
			case 6:
				//Developer forgot password
				$mailData			=	str_replace('{EMAIL}', $mailContentArray['email'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}',  $mailContentArray['password'], $mailData);
				break;
			case 7:
				//Developer Approve
				$mailData			=	str_replace('{EMAIL}', ucfirst($mailContentArray['userEmail']), $mailData);
				break;
		}
		$mail_image 	= 	GAME_SITE_PATH.'/webresources/mail_content/';
		$mailData 		=	str_replace('{SITE_PATH}',$sitelinkpath ,$mailData);
		$mailData 		=	str_replace('{SITE_MAIL_PATH}',$mail_image ,$mailData);
		$mailData		=   str_replace('{YEAR}', date('Y'), $mailData);
		$mailData		=   str_replace('{IMAGEPATH}', GAME_IMAGE_PATH, $mailData);
		$headers  		= 	"MIME-Version: 1.0\n";
		$headers 		.= 	"Content-Transfer-Encoding: 8bit\n";
		$headers        .= 	"From: Tilt $from\r\n";
		$headers 		.= 	"Content-type: text/html\r\n";
		if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
			//echo $mailData;		//die();			
		}
		else {
			 // $to .= ',uhanesan@gmail.com,uhanesan@hotmail.com,uhanesan@yahoo.com,uhanesan@rediffmail.com';
			 mail($to,$subject,$mailData,$headers);
			// $sendmail = sendMailSes($from,$to,$subject,$mailData,'');
		}
	}
}

function sendMailSes($from,$to,$subject,$html_message,$text_msg=''){
	
	require_once('sdk.class.php');// Include the SDK
	$ses = new AmazonSES();//// Instantiate the  class
	//$region = $ses->set_region(REGION_US_W2);
	$result = $ses->send_email($from, array(
		        'ToAddresses' => array($to),
		    	), 
				array(
				        // Subject is required
				        'Subject' => array(
				            // Data is required
				            'Data' => $subject,
				            'Charset' => 'utf8',
				        ),
				        // Body is required
				        'Body' => array(
				            'Html' => array(
				                // Data is required
				                'Data' => $html_message, //$html_message
				                'Charset' => 'utf8',
				            ),
				        ),
		    	)
			);
}

function mailThroughAmazon($from,$to,$subject,$html_message='',$text_msg='',$file_text_path='',$file_html_path='',$http_url='',$replyto='',$cc='',$bcc='',$return_path=''){

	require_once('ses.php');

	$ses = new SimpleEmailService(TILTPUPBLIC, TILTPRIVATE);

	$m = new SimpleEmailServiceMessage();
	$m->addTo($to);
	$m->setFrom($from);
	$m->setSubject($subject);
	if($text_msg != '' || $html_message != '') {
		$m->setMessageFromString($text_msg,$html_message);
	}
	else if($file_text_path != '' || $file_html_path != '') {
		$m->setMessageFromFile($file_text_path,$file_html_path);
	}
	else if($http_url != '') {
		$m->setMessageFromURL($http_url);
	}
	else {
		return 'Attempt to send mail with out message';
	}
	if($cc != '')
		$m->addCC($cc);
	if($bcc != '')
		$m->addBCC($bcc);
	if($return_path != '')
		$m->setReturnPath($return_path);
	if($replyto != '')
		$m->addReplyTo($replyto);
	return $ses->sendEmail($m);
}

function destroyPagingControlsVariables() { //clear paging session variables
    unset($_SESSION['orderby']);
    unset($_SESSION['ordertype']);
    unset($_SESSION['curpage']);
    unset($_SESSION['perpage']);
    unset($_SESSION['paging']);
}
//set paging session variables
function setPagingControlValues($default_field,$per_page) {
	if(isset($_POST['per_page']))
		$_SESSION['perpage'] = $_POST['per_page'];
	elseif(!isset($_SESSION['perpage']))
		$_SESSION['perpage'] = $per_page;

	if(isset($_POST['cur_page']))
		$_SESSION['curpage'] = $_POST['cur_page'];
	elseif(!isset($_SESSION['curpage']))
		$_SESSION['curpage'] = 1;

	if(isset($_POST['order_by']))
		$_SESSION['orderby'] = $_POST['order_by'];
	elseif(!isset($_SESSION['orderby']))
		$_SESSION['orderby'] = $default_field;

	if(isset($_POST['order_type']))
		$_SESSION['ordertype'] = $_POST['order_type'];
	elseif(!isset($_SESSION['ordertype']))
		$_SESSION['ordertype'] = 'desc';
	if(isset($_POST['paging_change']) && $_POST['paging_change']!='')
         $_SESSION['curpage'] = $_POST['paging_change'];
}

// Displays the sort icons for the column headings
// Paramters => 	column : field in the database that is merged in the ORDER BY clause of the query
//					title  : column name to be displayed on the screen.
// Output	 =>		Returns as a Hyperlink with given column and field.
function SortColumn($column, $title)
{
	$sort_type = 'ASC';
	$sort_image = 'fa fa-sort ';
	if (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'ASC')){  //asc
		$sort_type = 'DESC';
		$sort_image = 'fa fa-sort-alpha-asc';
	}
	elseif (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'DESC')){ //desc
		$sort_type = 'ASC';
		$sort_image = 'fa fa-sort-alpha-desc';
	}
	$alt_title = 'Sort by '.ucfirst(strtolower($title))." ".strtolower($sort_type);
	$sort_link = "<a href=\"#\" onclick=\"javascript:setPagingControlValues('".$column."','".$sort_type."',".$_SESSION['curpage'].");\" alt=\"".$alt_title."\" title=\"".$alt_title."\" >";
	return $sort_link.'<strong>'.$title.'</strong></a>&nbsp;'.$sort_link.'&nbsp;<i class="'. $sort_image.'"></i></a>';
}
function SortColumnAjax($column, $title,$functionName)
{
	$sort_type = 'ASC';
	$sort_image = 'fa fa-sort ';
	if (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'ASC')){  //asc
		$sort_type = 'DESC';
		$sort_image = 'fa fa-sort-alpha-desc ';
	}
	elseif (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'DESC')){ //desc
		$sort_type = 'ASC';
		$sort_image = 'fa fa-sort-alpha-asc  ';
	}
	$alt_title = 'Sort by '.ucfirst(strtolower($title))." ".strtolower($sort_type);
	$sort_link = "<a href=\"javascript:void(0);\" onclick=\"javascript:setPagingControlValuesAjax('".$column."','".$sort_type."',".$_SESSION['curpage']."); $functionName \" alt=\"".$alt_title."\" title=\"".$alt_title."\" >";
	return $sort_link.'<strong>'.$title.'</strong></a>&nbsp;'.$sort_link.'&nbsp;<i class="'. $sort_image.'"></a>';
}

// Display paging control
//Input : no. of records and URL
function pagingControlLatest($total,$action='')
{
	$per_page 		= $_SESSION['perpage'];
	$page 			= $_SESSION['curpage'];
	$pagination 	= '<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
	<tr>
	<td align="center" ><table cellspacing="0" cellpadding="0" border="0" align="center" width="100%"><tr><td align="center" valign="top"> ';
	if ($action == '')
		$action = $_SERVER['SCRIPT_NAME'];
	?>
	<form name="paging" id="paging" method="post" action="<?php echo($action);?>"  >
		<input type="Hidden" value="<?php echo($_SESSION['curpage']);?>" name="cur_page" id="cur_page">
		<input type="Hidden" value="<?php echo($_SESSION['orderby']);?>" name="order_by" id="order_by">
		<input type="Hidden" value="<?php echo($_SESSION['ordertype']);?>" name="order_type" id="order_type">
		<?php
		if ($total > $per_page)
		{
        $adjacents = "2";

    	$page = ($page == 0 ? 1 : $page);
    	$start = ($page - 1) * $per_page;

		$firstPage = 1;

		$prev = ($page == 1)?1:$page - 1;

    	$prev = $page - 1;
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;

    	if($lastpage > 1)
    	{
    		$pagination .= "<ul class='pagination'>";
			if ($page == 1)
			{
				$pagination.= "<li><a class='current'><i style='margin-top:2px' class='fa fa-angle-double-left'></i><span></span></a></li>";
				$pagination.= "<li><a class='current'><i style='margin-top:2px' class='fa fa-angle-left'></i><span></span></a></li>";
			}
			else
			{
				$pagination.= "<li><a class='nxt_arr_lt' href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$firstPage);\" ><i style='margin-top:2px' class='fa fa-angle-double-left'></i><span></span></a></li>";
				$pagination.= "<li><a class='nxt_arr' href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$prev);\" ><i style='margin-top:2px' class='fa fa-angle-left'></i><span></span></a></li>";
			}

    		if ($lastpage < 7 + ($adjacents * 2))
    		{
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li class='active'><a class='current'>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\">$counter</a></li>";
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'><span  class=\"no-border\">...</span></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1);\">$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage);\">$lastpage</a></li>";
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1);\">1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2);\">2</a></li>";
    				$pagination.= "<li class='dot'><span  class=\"no-border\">...</span></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'><span  class=\"no-border\">..</span></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1);\" >$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage);\" >$lastpage</a></li>";
    			}
    			else
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1);\" >1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2);\" >2</a></li>";
    				$pagination.= "<li class='dot'><span  class=\"no-border\">..</span></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\" >$counter</a></li>";
    				}
    			}
    		}

    		if ($page < $counter - 1){
    			$pagination.= "<li><a  class='pre_arr' href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$next);\" ><span><i style='margin-top:2px' class='fa fa-angle-right'></i></span></a></a></li>";
                $pagination.= "<li><a class='pre_arr_lt' shref='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage);\" ><span><i style='margin-top:2px' class='fa fa-angle-double-right'></i></span></a></a></li>";
    		}else{
    			$pagination.= "<li><a class='current cpre_arr'><i style='margin-top:2px' class='fa fa-angle-right'></i><span></span></a></a></li>";
                $pagination.= "<li><a class='current cpre_arr_lt'><i style='margin-top:2px' class='fa fa-angle-double-right'></i><span></span></a></a></li>";
            }
    		$pagination.= "</ul>";
    	}
	}
		 echo $pagination; ?>
		 	</td></tr>
		 </table>
		</td>
		<?php $per_page_array =  eval(ADMIN_PER_PAGE_ARRAY);
		if($total > $per_page_array[0]){ ?>
		<td  class="record" align="right" width="11%">
			
			<span class="fright">
				<select name="per_page" id="per_page" onchange="setPerPage(this.value);" class="form-control per-page">
				<?php foreach($per_page_array as $value){ ?>
					<option value="<?php echo($value);?>" <?php if($per_page == $value) { echo "selected='selected'"; } ?>><?php echo($value);?></option>
				<?php } ?>
				</select>
			</span>
			<span class="recor_txt">Per page &nbsp;</span>
		</td>
		<?php }?>
		</tr></table>
	</form>
<?php }
// Display paging control
//Input : no. of records and URL
function pagingControlLatestAjax($total,$functionName='')
{
	$per_page 		= $_SESSION['perpage'];
	$page 			= $_SESSION['curpage'];
	$pagination 	= '<br><table cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
	<tr>
	<td align="center" width="90%" ><table cellspacing="0" cellpadding="0" border="0" align="center"><tr><td> ';
	?>
	<form name="pagingAjax" id="pagingAjax" method="post"   >
		<input type="Hidden" value="<?php echo($_SESSION['curpage']);?>" name="cur_page" id="cur_page">
		<input type="Hidden" value="<?php echo($_SESSION['orderby']);?>" name="order_by" id="order_by">
		<input type="Hidden" value="<?php echo($_SESSION['ordertype']);?>" name="order_type" id="order_type">
		<?php
		if ($total > $per_page)
		{
        $adjacents = "2";
    	$page = ($page == 0 ? 1 : $page);
    	$start = ($page - 1) * $per_page;

		$firstPage = 1;

		$prev = ($page == 1)?1:$page - 1;

    	$prev = $page - 1;
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;

    	if($lastpage > 1)
    	{
    		$pagination .= "<ul class='pagination'>";
                    $pagination .= "<li class='details'>Page $page of $lastpage</li>";
			if ($page == 1)
			{
				$pagination.= "<li><a class='current cnxt_arr_lt'><span></span></a></li>";
				$pagination.= "<li><a class='current cnxt_arr'><span></span></a></li>";
			}
			else
			{
				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$firstPage); $functionName \" ><<</a></li>";
				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$prev); $functionName \" ><</a></li>";
			}
    		if ($lastpage < 7 + ($adjacents * 2))
    		{
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li><a class='current'>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \">$counter</a></li>";
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'>...</li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1); $functionName \">$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage); $functionName \">$lastpage</a></li>";
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1); $functionName \">1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2); $functionName \">2</a></li>";
    				$pagination.= "<li class='dot'>...</li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'>..</li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1); $functionName \" >$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage); $functionName \" >$lastpage</a></li>";
    			}
    			else
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1); $functionName \" >1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2); $functionName \" >2</a></li>";
    				$pagination.= "<li class='dot'>..</li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \" >$counter</a></li>";
    				}
    			}
    		}
    		if ($page < $counter - 1){
    			$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$next); $functionName \" >></a></a></li>";
                $pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage); $functionName \" >>></a></a></li>";
    		}else{
    			$pagination.= "<li><a class='current'>></a></a></li>";
                $pagination.= "<li><a class='current'>>></a></a></li>";
            }
    		$pagination.= "</ul>";
    	}
	}
		 echo $pagination; ?>
		 	</td></tr>
		 </table>
		</td>
		<?php  $per_page_array =  eval(ADMIN_PER_PAGE_ARRAY);
		if($total > $per_page_array[0]){ ?>
		<td  class="record">
		<strong>Per page </strong>
		</td><td class="record" width="20%" style="padding-right:10px;">

			<select name="per_page" id="per_page" onchange="setPerPageAjax(this.value);<?php  echo $functionName; ?>" style="width:40px;">
			<?php foreach($per_page_array as $value){?>
				<option value="<?php echo($value);?>" <? if($per_page == $value) echo " selected='selected'"?>><?php echo($value);?></option>
			<?php }?>
			</select>
		</td>
		<?php }?>
		</tr>
		</table>
	</form>
<?php } 
function uploadVideoToS3($video,$videoName){
	$filename = 'demoposts/'.$videoName;
	if (!class_exists('S3'))require_once('S3.php');
			//instantiate the class		
			$s3 = new S3(TILTPUPBLIC, TILTPRIVATE);
			$s3->putBucket(BUCKET_NAME, S3::ACL_PUBLIC_READ);
				
			if ($s3->putObjectFile($video, BUCKET_NAME, $filename, S3::ACL_PUBLIC_READ,
					array( // Custom $requestHeaders
					   "Cache-Control" => "max-age=315360000",
			            "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
			        )
			)) {
					//echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					//echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
			
			
	
}
function uploadImageToS3($image_path,$type,$image_name){
	//error_reporting(E_ALL);
	$image_upload_path = '';
	if($type == 1){ //user thumb
		$image_upload_path = 'users/thumbnail/'.$image_name;
	}else if($type == 3){ //user Original 
		$image_upload_path = 'users/'.$image_name;
	} else if($type == 4){ //brands 
		$image_upload_path = 'brands/'.$image_name;
	}else if($type == 5){ //brands thumb
		$image_upload_path = 'brands/thumbnail/'.$image_name;
	}else if($type == 6){ //games 
		$image_upload_path = 'games/'.$image_name;
	}else if($type == 7){ //message 
		$image_upload_path = 'games/thumbnail/'.$image_name;
	}
	else if($type == 11){ //coupon
		$image_upload_path = 'coupons/'.$image_name;
	}
	else if($type == 12){ //banner
		$image_upload_path = 'banner/'.$image_name;
	}
	else if($type == 13){ //tournament pdf
		$image_upload_path = 'pdf/'.$image_name;
	}
	else if($type == 14){ //Youtube Link
		$image_upload_path = 'youtubelink/'.$image_name;
	}
	else if($type == 15){ //Game developer
		$image_upload_path = 'gamedevelopers/'.$image_name;
	}
	else if($type == 16){ //Game developer thumbnail
		$image_upload_path = 'gamedevelopers/thumbnail/'.$image_name;
	}
	else if($type == 17){ //Custom prize 
		$image_upload_path = 'customprize/'.$image_name;
	}
	// 18 used for website in admin portal
	else if($type == 19){ //Game Certificate 
		$image_upload_path = 'certificate/'.$image_name;
	}else if($type == 21){ //sdk  
		$image_upload_path = 'sdk/'.$image_name;
	}else if($type == 22){ //defaultuserimages
		$image_upload_path = 'defaultuserimages/'.$image_name;
	}
	
	require_once('sdk.class.php');// Include the SDK
	
	$s3 = new AmazonS3();//// Instantiate the AmazonS3 class
	
	$bucket = BUCKET_NAME;
	// Create our new bucket in the US-West region.
	$exists = $s3->if_bucket_exists($bucket);
	if(!$exists){
		$create_bucket_response = $s3->create_bucket($bucket, AmazonS3::REGION_US_W2);
	}
		$filename = $image_path;
		$s3->batch()->create_object($bucket, $image_upload_path, array(
					'fileUpload' => $filename,
					'acl' => AmazonS3::ACL_PUBLIC,
					 'headers' => array( // Custom $requestHeaders //meta headers
					           	 	"Cache-Control" => "max-age=315360000",
					            	"Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
								  )
					),
					array( // Custom $requestHeaders //meta headers
					           	 	"Cache-Control" => "max-age=315360000",
					            	"Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
								  )
					
			);
		$file_upload_response = $s3->batch()->send();
}

function image_exists($type,$image_name){
	//if($type != 21)
	if($type != 21 && $type != 19)
		return true;
	if($type == 1){ //user thumb
		$filename = 'users/thumbnail/'.$image_name;
	}else if($type == 3){ //user Original 
		$filename = 'users/'.$image_name;
	} else if($type == 4){ //brands 
		$filename = 'brands/'.$image_name;
	}else if($type == 5){ //brands thumb
		$filename = 'brands/thumbnail/'.$image_name;
	}else if($type == 6){ //games 
		$filename = 'games/'.$image_name;
	}else if($type == 7){ // 
		$filename = 'games/thumbnail/'.$image_name;
	}
	else if($type == 11){ //coupon
		$filename = 'coupons/'.$image_name;
	}
	else if($type == 12){ //banner
		$filename = 'banner/'.$image_name;
	}
	else if($type == 13){ //tournament pdf
		$filename = 'pdf/'.$image_name;
	}
	else if($type == 14){ //Youtube Link
		$filename = 'youtubelink/'.$image_name;
	}
	else if($type == 15){ //Game developer
		$filename = 'gamedevelopers/'.$image_name;
	}
	else if($type == 16){ //Game developer thumbnail
		$filename = 'gamedevelopers/thumbnail/'.$image_name;
	}
	else if($type == 17){ //Custom prize 
		$filename = 'customprize/'.$image_name;
	} 
	// 18 used for website in admin portal
	else if($type == 19){ //Game Certificate 
		$filename = 'certificate/'.$image_name;
	}
	else if($type == 21){ //sdk  
		$image_upload_path = 'sdk/'.$image_name;
	}else if($type == 22){ //defaultuserimages  
		$image_upload_path = 'defaultuserimages/'.$image_name;
	}
	$bucket = BUCKET_NAME;
	require_once('sdk.class.php');// Include the SDK
	$s3 = new AmazonS3();// Instantiate the AmazonS3 class
	$response =  $s3->if_object_exists($bucket, $filename);
	if ($response){
	 return true;
	}
	else{
		return false;
	}
}
function deleteImages($type,$image_name){
	if($type == 1){ //user thumb
		$filename = 'users/thumbnail/'.$image_name;
	}else if($type == 3){ //user Original 
		$filename = 'users/'.$image_name;
	} else if($type == 4){ //brands 
		$filename = 'brands/'.$image_name;
	}else if($type == 5){ //brands thumb
		$filename = 'brands/thumbnail/'.$image_name;
	}else if($type == 6){ //games 
		$filename = 'games/'.$image_name;
	}else if($type == 7){ //message 
		$filename = 'games/thumbnail/'.$image_name;
	}
	else if($type == 11){ //coupon
		$filename = 'coupons/'.$image_name;
	}
	else if($type == 12){ //banner
		$filename = 'banner/'.$image_name;
	}
	else if($type == 13){ //tournament pdf
		$filename = 'pdf/'.$image_name;
	}
	else if($type == 14){ //Youtube Link
		$filename = 'youtubelink/'.$image_name;
	}
	else if($type == 15){ //Game developer
		$filename = 'gamedevelopers/'.$image_name;
	}
	else if($type == 16){ //Game developer thumbnail
		$filename = 'gamedevelopers/thumbnail/'.$image_name;
	}
	else if($type == 17){ //Custom prize 
		$filename = 'customprize/'.$image_name;
	}
	// 18 used for website in admin portal
	else if($type == 19){ //Game Certificate 
		$filename = 'certificate/'.$image_name;
	}
	else if($type == 21){ //sdk  
		$image_upload_path = 'sdk/'.$image_name;
	}else if($type == 22){ //defaultuserimages  
		$image_upload_path = 'defaultuserimages/'.$image_name;
	}
	$bucket = BUCKET_NAME;
	
	require_once('sdk.class.php');// Include the SDK
	$s3 = new AmazonS3();// Instantiate the AmazonS3 class
	$info = $s3->delete_object($bucket, $filename);
	if ($info){
	 return true;
	}
	else{
		return false;
	}
}

function ipAddress(){
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
 		$ip_address=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
 		$ip_address=$_SERVER['REMOTE_ADDR'];
	}
	return $ip_address;
}
function sendingPNForAllUser($user_id,$message){
	if($_SERVER['REMOTE_ADDR']=='172.21.4.95') {
		echo '===>'.__LINE__.'===>'.$user_id.'<br />';
		echo '===>'.__LINE__.'===>'.$message.'<br />';
	}
}
function imagethumb_new($src, $des,$itype,$exn, $maxwidth, $hght) {
		ini_set('memory_limit', '512M');
		
		if(file_exists($src))
		   	$size=getimagesize($src);
		$mime_type	=	$size['mime'];
		$mime_type	= strtolower($mime_type);
		$itype	=	substr($mime_type,strpos($mime_type,'/')+1);
		if($mime_type	==	'image/gif') $exn	= 'gif';
		if( ($mime_type	==	'image/pjpeg') || ($mime_type	==	'image/jpg') || ($mime_type	==	'image/jpeg')) $exn	= 'jpg';
		if($mime_type	==	'image/png') $exn	= 'png';
		if($mime_type	==	'image/bmp') $exn	= 'bmp';
		
		$n_width=$size[0];
		$n_height=$size[1];
		$imagehw = GetImageSize($src);
	
		$imagewidth = $imagehw[0];
		$imageheight = $imagehw[1];
		$imgorig = $imagewidth;
		$n_width1 = $maxwidth;
		$n_height1 = $hght;
		if (($n_width - $n_width1) > ($n_height - $n_height1)) 
		{
		  $imagewidth=$n_width1; 
		  $imageheight=($n_width1/$n_width)*$n_height;
		}else
		{
		    $imagewidth=($n_height1/$n_height)*$n_width; 
		    $imageheight=$n_height1;	  
	
		}
		
		if($imagewidth > $n_width1){
			$imagewidth = $n_width1;
			$imageheight = round($imageheight / ($imagewidth/$n_width1));
		}
		
		if($n_width <= $n_width1 && $n_height <= $n_height1){
			$imagewidth = $n_width;
			$imageheight = $n_height;
		}
		
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		switch($exn)
		{
		case "jpg":	
		$srcimg=ImageCreateFromJPEG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "jpeg":
		$srcimg=ImageCreateFromJPEG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "pjpeg":
		$srcimg=ImageCreateFromJPEG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "gif":
		$srcimg=ImageCreateFromGIF($src) or die("Problem In opening Source Image");
		$destimg=ImageCreate($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "png":
		$srcimg=ImageCreateFromPNG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "bmp":
		$srcimg =ImageCreateFromBMP($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		}
		
		$int = hexdec('ffffff');
		$arr = array("red" => 0xFF & ($int >> 0x10),
	               "green" => 0xFF & ($int >> 0x8),
	               "blue" => 0xFF & $int);
		$transparent = imagecolorallocate($destimg,  $arr["red"], $arr["green"], $arr["blue"]);
		
		for($x=0;$x<$n_width1;$x++) {
	           for($y=0;$y<$n_height1;$y++) {
	             imageSetPixel( $destimg, $x, $y, $transparent );
	           }
	         }
		$dest_x = (( $n_width1 / 2 ) - ( $imagewidth / 2 )); // centered
		$dest_y = (( $n_height1 / 2 ) - ( $imageheight / 2 )); // centered	
		ImageCopyresampled($destimg,$srcimg,$dest_x,$dest_y,0,0,$imagewidth,$imageheight,$n_width,$n_height) or die("Problem In resizing");
		if(($itype=="jpg")||($itype=="jpeg")||($itype=="pjpeg"))
		{
			imagejpeg($destimg, $des, 100); //ImageJPEG($destimg) or die('Problem In saving');
		}
		else
		if($itype=="gif")
		{
			ImageGIF($destimg,$des) or die("Problem In saving"); //ImageGIF($destimg) or die('Problem In saving');
		}
		else
		if($itype=="png")
		{
			imagepng($destimg, $des, 9, PNG_NO_FILTER) or die("Problem In saving"); //ImagePNG($destimg) or die('Problem In saving');
		}
		else
		if($itype=="bmp")
		{
			imagebmp($destimg, $des, 9, BMP_NO_FILTER) or die("Problem In saving"); //ImagePNG($destimg) or die('Problem In saving');
		}
		imagedestroy($destimg);
	}  
	function checkImage($files,$type){
		$flag = 0;
		if (isset($files['tmp_name']) && $files['tmp_name'] != '') {
	   		$ImageArray = array('image/png','image/jpg','image/jpeg','image/gif');
			$dimension = getImageSize($files['tmp_name']);
			
			if(!in_array($files['type'],$ImageArray)){
				$flag = 1;
			}
			else if(!empty($files['error'])){
				$flag = 2;
			}
			else if($files['size'] > 5242880 ) {
				$flag = 3;
			}
			else if($type ==1 && ($dimension[0] < '100' || $dimension[1] < '100')){
				$flag = 4;
			}
			else if($type ==2 && ($dimension[0] < '300' || $dimension[1] < '100')){
				$flag = 4;
			}
			else if($type ==3 && ($dimension[0] < '640')){
				$flag = 4;
			}
			else{
				$flag = 5;//success
			}
	   }
	   return $flag;
	}
function checkVideo($files){
	if (isset($files['tmp_name']) && $files['tmp_name'] != '') {
		$VideoArray = array('image/mp4');
		if(!in_array($files['type'],$VideoArray)){
				$flag = 1;
		}
		else if(!empty($files['error'])){
			$flag = 2;
		}
		else if($files['size'] > 5242880 ) {
			$flag = 3;
		}
		else{
				$flag = 5;//success
	    }
	}
}
function subval_sort($a,$subkey) {
			$b  = $c  =	array();
			if(is_array($a) && count($a) > 0) {
				foreach($a as $k=>$v) {
					$b[$k] = $v[$subkey];
				}
			}
			if(is_array($b) && count($b) > 0) {
				asort($b);
				foreach($b as $key=>$val) {
					$c[]   = $a[$key];
				}
			}
			return $c;
}
function sorting($a,$subkey) {

			$b  = $c  =	array();
			if(is_array($a) && count($a) > 0) {
				foreach($a as $k=>$v) {
					$b[$k] = $v->$subkey;
				}
			}
			if(is_array($b) && count($b) > 0) {
				asort($b);
				foreach($b as $key=>$val) {
					$c[]   = $a[$key];
				}
			}
			return $c;
}
function getTimeZone(){
	$key="9dcde915a1a065fbaf14165f00fcc0461b8d0a6b43889614e8acdb8343e2cf15";
	if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
		$ip = '27.124.58.84';
	}
	else{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	}
	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=xml";

	$xml = simplexml_load_file($url);
	foreach($xml->children() as $child)
  	{
	 if($child->getName() == 'timeZone')
	 	return $child ;
 	}
}
function convertIntocheckinGmtSite($meet_time, $time_zone=''){
	 if($time_zone=='') {
	 	if(!isset($_SESSION['mgc_ses_from_timeZone']) || $_SESSION['mgc_ses_from_timeZone'] == ''){
			 $time_zone = getTimeZone();
			 $_SESSION['mgc_ses_from_timeZone'] = strval($time_zone);
		} else {
			$time_zone = $_SESSION['mgc_ses_from_timeZone'];
		}
	 }
	 if(substr($time_zone,0,1) == '-') {
	 	$multiplier = -1;
		$time_zone = substr($time_zone,1);
	 }
	 else
	 	$multiplier = 1;
	$offset_array = explode(':',$time_zone);
	$hour= $offset_array[0];
	$minutes= $offset_array[1];
	$offset = $multiplier*(($hour*60*60)+($minutes*60));
	if($meet_time != '') {
		$date = strtotime(gmdate($meet_time))+$offset;
	}
	else{
		$date = strtotime(gmdate('Y-m-d H:i:s'))+$offset;
	}
	return date('Y-m-d H:i:s',$date);
}
/********************************************************
* Function : displayDate
********************************************************/
function displayDate($postedDate,$time_zone='')
{
	//$endDate = '2012-07-24 14:00:01';
	if($postedDate != '')
	{
		$db_date	=	date('d',strtotime($postedDate));
		$db_month	=	date('M',strtotime($postedDate));
		$db_year	=	date('Y',strtotime($postedDate));
		$db_hour	=	date('h',strtotime($postedDate));
		$db_min		=	date('i',strtotime($postedDate));
		$db_sec		=	date('s',strtotime($postedDate));
		$meridian	=	date('a',strtotime($postedDate));


		if($time_zone != '') {
			if(substr($time_zone,0,1) == '-') {
				$multiplier = -1;
				$time_zone = substr($time_zone,1);
			}
			else
				$multiplier = 1;
			$offset_array 	= explode(':',$time_zone);
			$hour			= $offset_array[0];
			$minutes		= $offset_array[1];
			$offset 		= $multiplier*(($hour*60*60)+($minutes*60));

			$c_date 		= strtotime(date('Y-m-d H:i:s'))+$offset;
			$cur_date		= date('d',$c_date);
			$cur_month		= date('M',$c_date);
			$cur_year		= date('Y',$c_date);
		}
		else {
			$cur_date	=	date('d');
			$cur_month	=	date('M');
			$cur_year	=	date('Y');
			$c_date 		= strtotime(date('Y-m-d H:i:s'));
		}
		$posted_week = date('W',strtotime($postedDate));
		$current_week = date('W',$c_date);
		if( ($db_year==$cur_year) && ($db_month==$cur_month) )
		{
			if($db_date==$cur_date)
				$time =	$db_hour.':'.$db_min.' '.$meridian;
			else if( ($db_date+1)==$cur_date)
				$time =	'Yesterday '.$db_hour.':'.$db_min.' '.$meridian;
			else if($posted_week == $current_week) {
				$week_day = date('l',strtotime($postedDate));
				$time =	$week_day.' '.$db_hour.':'.$db_min.' '.$meridian;
			}
			else
				$time =	$db_month. ' '.$db_date.' '.$db_hour.':'.$db_min.' '.$meridian;
		}
		else if($db_year==$cur_year)
			$time =	$db_month. ' '.$db_date.' '.$db_hour.':'.$db_min.' '.$meridian;
		else
			$time =	$db_month. ' '.$db_date.' '.$db_year.' '.$db_hour.':'.$db_min.' '.$meridian;
	}
	else
		$time = 'Null';
	return $time;
}
/********************************************************
* Function : displayDate
********************************************************/
function displayConversationDateTime($postedDate,$time_zone='')
{
	//$endDate = '2012-07-24 14:00:01';
	if($postedDate != '')
	{
		$db_date	=	date('d',strtotime($postedDate));
		$db_month	=	date('m',strtotime($postedDate));
		$db_year	=	date('Y',strtotime($postedDate));
		$db_hour	=	date('h',strtotime($postedDate));
		$db_min		=	date('i',strtotime($postedDate));
		$db_sec		=	date('s',strtotime($postedDate));
		$meridian	=	date('A',strtotime($postedDate));


		if($time_zone != '') {
			if(substr($time_zone,0,1) == '-') {
				$multiplier = -1;
				$time_zone = substr($time_zone,1);
			}
			else
				$multiplier = 1;
			$offset_array 	= explode(':',$time_zone);
			$hour			= $offset_array[0];
			$minutes		= $offset_array[1];
			$offset 		= $multiplier*(($hour*60*60)+($minutes*60));
			$c_date 		= strtotime(date('Y-m-d H:i:s'))+$offset;
			$cur_date		= date('d',$c_date);
			$cur_month		= date('m',$c_date);
			$cur_year		= date('Y',$c_date);
		}
		else {
			$cur_date	=	date('d');
			$cur_month	=	date('m');
			$cur_year	=	date('Y');
		}
		$time =	$db_month. '/'.$db_date.'/'.$db_year.'&nbsp;&nbsp;'.$db_hour.':'.$db_min.' '.$meridian;
	}
	else
		$time = 'Null';
	return $time;
}
/********************************************************
* Function : displayDate
********************************************************/
function displayConversationTime($postedDate,$time_zone='')
{
	//$endDate = '2012-07-24 14:00:01';
	if($postedDate != '')
	{
		$db_date	=	date('d',strtotime($postedDate));
		$db_month	=	date('M',strtotime($postedDate));
		$db_year	=	date('Y',strtotime($postedDate));
		$db_hour	=	date('h',strtotime($postedDate));
		$db_min		=	date('i',strtotime($postedDate));
		$db_sec		=	date('s',strtotime($postedDate));
		$meridian	=	date('a',strtotime($postedDate));


		if($time_zone != '') {
			if(substr($time_zone,0,1) == '-') {
				$multiplier = -1;
				$time_zone = substr($time_zone,1);
			}
			else
				$multiplier = 1;
			$offset_array 	= explode(':',$time_zone);
			$hour			= $offset_array[0];
			$minutes		= $offset_array[1];
			$offset 		= $multiplier*(($hour*60*60)+($minutes*60));
			$c_date 		= strtotime(date('Y-m-d H:i:s'))+$offset;
			$cur_date		= date('d',$c_date);
			$cur_month		= date('M',$c_date);
			$cur_year		= date('Y',$c_date);
		}
		else {
			$cur_date	=	date('d');
			$cur_month	=	date('M');
			$cur_year	=	date('Y');
		}
		if( ($db_year==$cur_year) && ($db_month==$cur_month) )
		{
			if($db_date==$cur_date)
				$time =	$db_hour.':'.$db_min.' '.$meridian;
			else if( ($db_date+1)==$cur_date)
				$time =	'Yesterday ';
			else
				$time =	$db_month. ' '.$db_date;
		}
		else if($db_year==$cur_year)
			$time =	$db_month. ' '.$db_date;
		else
			$time =	$db_month. ' '.$db_date.' '.$db_year;
	}
	else
		$time = 'Null';
	return $time;
}

/********************************************************
* Function : Emoji Comment Text
********************************************************/
function getCommentTextEmoji($plateform,$comments,$from='',$emojiNeeded=''){
		error_reporting(0);
		require_once("emoji.php");
		if($plateform == 'ios' ) 
		{
			 $datas = $comments; 
		}
		else if($plateform == 'android' ) 
		{
			  if($from == '1'){
					if($emojiNeeded == 0)
					 	$datas = $comments; 
					else{
						 $convertedComments = emoji_unified_to_html(base64_decode($comments));
						 preg_match_all('/<span class="emoji (.*?)"><\/span>/', $convertedComments, $matches);
							if(count($matches)>1) {
								$data = $convertedComments;
								foreach($matches as $key => $value) {
									if($key==0) {
										foreach($value as $subKey => $subValue) {
											$data = str_replace($subValue,'%*%'.$matches[1][$subKey].'%*%',$data);
										}
									}
								}
							} else {
								$data = preg_replace("/<span[^>]+\>/i", $matches[1][0], $htmlContent);
							}
							if(!strstr($convertedComments,'class="emoji')){
								$data = $convertedComments;
							}
							
							$datas = base64_encode($data);
					}
			 }
			 else{
				$datas = $comments; 
			 }
			
		}
		else{
			 if($from == '1' ) 
			{
				 $data_caption = base64_decode($comments);
				 $datasComment       = emoji_softbank_to_unified($data_caption);
			}
			else if($from == '2' ) 
			{
				 $data_caption = base64_decode($comments);
				 $datasComment = emoji_google_to_unified($data_caption);
			}
			else{
				  $datasComment = base64_decode($comments);
				  return  $datasComment;
			}
			if( preg_match('/(mac)/i', $_SERVER['HTTP_USER_AGENT'])) { 	
				 $datas = emoji_unified_to_html($datasComment);
			}
			else{
				$datas = emoji_unified_to_html($datasComment);
			}
		}
	return $datas;
}

/********************************************************
* Function : Emoji Comment Text
********************************************************/
function addCommentTextEmoji($plateform,$comments){
	if($plateform == 'web' ){
		$datas = base64_encode($comments);
	}
	else
		$datas = $comments;
	return $datas;
}
function html_entities ( $string )
{
     return str_replace ( array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' , '&mdash;', '&nbsp;'), array ( '&', '"', "'", '<', '>' ,'--', ' '), $string );
}
function logEntry($event,$txt_file){
	$log_dir	=	'C:/wamp/www/MGC/logs';
	if(!is_dir($log_dir)) {
		mkdir($log_dir,0777);
	}
	$filename = "C:/wamp/www/MGC/logs".$txt_file;
	$f		  = fopen($filename, 'a' );			// open the log file for writing and put the pointer at the end of the file
	fwrite ($f, $event . chr(13) . chr(10) );
	fclose($f);
}
function getImageMimeType($imagedata)
{
  $imagemimetypes = array( 
    "jpeg" => "FFD8", 
    "png" => "89504E470D0A1A0A", 
    "gif" => "474946",
    "bmp" => "424D"
  );

  foreach ($imagemimetypes as $mime => $hexbytes)
  {
    $bytes = getBytesFromHexString($hexbytes);
    if (substr($imagedata, 0, strlen($bytes)) == $bytes)
      return $mime;
  }

  return NULL;
}
function getBytesFromHexString($hexdata)
{
  for($count = 0; $count < strlen($hexdata); $count+=2)
    $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

  return implode($bytes);
}
function createEndpointARNAWS($PlatformApplicationArn,$Token,$CustomUserData){
	error_reporting(E_ALL);
	$endpoint = require("sns-create.php");
	return $endpoint;die();
}
function sendNotificationAWS($message,$EndpointArn,$platform,$badge,$type,$processId,$userId,$unreadCount=0,$userName=''){
	error_reporting(E_ALL);
	$endpoint = require("sns-send.php");
	return $endpoint;die();
}
function dateValidation($date){
	$result = 0;
	$date  = explode('/', $date);
	if (count($date) == 3) {
		if($date[0] != '' && $date[1] != '' && $date[2] != ''){
			if($date[2] >= '1983'){
			    if (checkdate($date[0], $date[1], $date[2]))
				    $result = 1;
			   	else 
			       $result = 0;
			}
			else 
		       $result = 0;
		}
		else {
		    $result = 0;
		}
	} 
	else {
	    $result = 0;
	}	
	return $result;
}
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function logEntryProcess($event,$txt_file){
	if(!is_dir(ABS_PATH.'/logs')) {
		mkdir(ABS_PATH.'/logs',0777);
	}
	$log_dir	=	ABS_PATH.'/logs/'.date('mY');
	if(!is_dir($log_dir)) {
		mkdir($log_dir,0777);
	}
	$filename = ABS_PATH."/logs/".date('mY')."/".$txt_file;
	$f		  = fopen($filename, 'a' );			// open the log file for writing and points to end of file
	fwrite ($f, $event . chr(13) . chr(10) );
	fclose($f);
}

function decodelanguage($string)
{
	require_once("emoji.php");
	$convertedstring	=	emoji_unified_to_html(base64_decode($string));
	preg_match_all('/<span class="emoji (.*?)"><\/span>/', $convertedstring, $matches);
	return $convertedstring;
}

function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

function getPassphrase($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    }
    return $token;
}
/********************************************************
  * Function Name: translateLatLngtoAddress
  * Purpose: Gets latitude, longitude from given address
  * Paramters :
  *			$address  - Full Address
  * Output : Returns Boolean value.
  *******************************************************/
function translateAddresstoLatLng($address)
{
	$latLong	=  new stdClass();
	 
	$url="http://maps.google.com/maps/api/geocode/xml?address=".urlencode($address)."&output=xml&sensor=false";
	$page = file_get_contents($url);
	// Parse the returned XML file
	$service_xml = new SimpleXMLElement($page);
	if (isset($service_xml->status)  && $service_xml->status == 'OK' ) {
		$latLong->latitude 	=	$service_xml->result->geometry->location->lat;
		$latLong->longitude =	$service_xml->result->geometry->location->lng;
		return $latLong;
	}
	else  {
		return 1;
	}
}

function getCurrentTime($zone = '',$format)	{
	$zone	=   'America/New_York';
	$tz		=	new DateTimeZone($zone);
	$date	=	new DateTime();
	$date->setTimeZone($tz);
	$deviceDate	= $date->format($format);
	return $deviceDate;
}
function encode($id) {
	return dechex(($id + 5) * 101);
}
function decode($id) {
	return hexdec($id)/101-5;
}
//getting country location
function getCountryLocation($query){
	$key = "AIzaSyBKjejRFhDxiLyfoR7ogZOWkIXbVrqztNY";
	$url = "https://maps.googleapis.com/maps/api/place/textsearch/xml?query=$query&type=&sensor=true&key=$key";
	$xml = simplexml_load_file($url);
	$locationdata 	=	array();
	if(isset($xml->result->geometry->location->lat) && !empty($xml->result->geometry->location->lat))
		$locationdata['latitude']	=	trim((string)$xml->result->geometry->location->lat);
	else
		$locationdata['latitude']	=	0;
	
	if(isset($xml->result->geometry->location->lng) && !empty($xml->result->geometry->location->lng))
		$locationdata['longitude']	=	trim((string)$xml->result->geometry->location->lng);
	else
		$locationdata['longitude']	=	0;
	return $locationdata;
}
// Get latitude longitude for Address
function getAddresstoLatLng($address)
{
	$locationdata 	=	array();
	 
	$url="http://maps.google.com/maps/api/geocode/xml?address=".urlencode($address)."&output=xml&sensor=false";
	$page = file_get_contents($url);
	// Parse the returned XML file
	$service_xml = new SimpleXMLElement($page);
	if (isset($service_xml->status)  && $service_xml->status == 'OK' ) {
		$locationdata['latitude'] 	=	$service_xml->result->geometry->location->lat;
		$locationdata['longitude']	=	$service_xml->result->geometry->location->lng;
	}
	else  {
		$locationdata['latitude'] 	= 0;
		$locationdata['longitude']	=	0;
	}
	return $locationdata;
}

//getting Foursquare Autocomplete
function getFourSquareInfo($latitude,$longitude,$quryString,$limit,$radius){
	
	//$foursquareUrl = "https://api.foursquare.com/v2/venues/search?ll=".$latitude.",".$longitude."$quryString$limit$radius&intent=global&oauth_token=S3M4501E3S0CFG1OSSZC5SCVQFQ4BPOW51DSXRCZSN14GP0L&v=".date('Ymd');
	$foursquareUrl = "https://api.foursquare.com/v2/venues/search?ll=".$latitude.",".$longitude."$quryString$limit$radius&oauth_token=S3M4501E3S0CFG1OSSZC5SCVQFQ4BPOW51DSXRCZSN14GP0L&v=".date('Ymd');
	//echo"<br>===================>".$foursquareUrl."<========>";
	$jSonContents 		= 	file_get_contents($foursquareUrl);
	$contents			=   json_decode($jSonContents,1);
	return $contents['response']['venues'];
}
/** 
* Function To Generate PDF
**/
function generatePDF($developerId,$tournamentId)
{
	require_once(file_exists('../controllers/AdminController.php')?'../controllers/AdminController.php':'controllers/AdminController.php');
	require_once(file_exists('../controllers/DeveloperController.php')?'../controllers/DeveloperController.php':'controllers/DeveloperController.php');
	require_once(file_exists('../controllers/TournamentController.php')?'../controllers/TournamentController.php':'controllers/TournamentController.php');
	require_once(file_exists('../controllers/GameController.php')?'../controllers/GameController.php':'controllers/GameController.php');
	$tourManageObj   =   new TournamentController();
	$gameDevObj   	=   new DeveloperController();
	$adminLoginObj	=	new AdminController();
	$gameManageObj   =   new GameController();
	$TournamentDetailsArray = $tourManageObj->selectTournament("id,PdfName,fkGamesId,Prize,StartDate,TournamentName, Type,PIN ",'id="'.$tournamentId.'"');
	$pdflocation1 = $pdfDevDetail = $pdflocation2 = '';
	$tournamentPin = 'NO PIN REQUIRED';
	$filePath = $filesize = $title = $BannerImage = '';
	$pinHeight		= 0;
	$pinDisplay		= '.display {display:none;}';
	if(isset($TournamentDetailsArray) && count($TournamentDetailsArray) > 0) {
		require_once('MPDF57/mpdf.php');
		$tournamentid = $TournamentDetailsArray[0]->id;
		//Game Developer details
		$pdfDevDetail	= $gameDevObj->selectSingleDeveloper("Photo,Company","id=".$developerId);
		if(isset($TournamentDetailsArray[0]->PIN) && $TournamentDetailsArray[0]->PIN == 1){
			$PinDetailsArray = $tourManageObj->checkPinCode("*"," fkTournamentsId = ".$tournamentId);
			if(isset($PinDetailsArray[0]->PinCode) && $PinDetailsArray[0]->PinCode != ''){
				$tournamentPin 	= $PinDetailsArray[0]->PinCode;
				$pinHeight		= 30;
				$pinDisplay		= '';
			}
		}
		$pdflocation1 .= isset($pdfDevDetail[0]->Company) && $pdfDevDetail[0]->Company != '' ? $pdfDevDetail[0]->Company.", " : '' ;
		$prizeImagePath = $customPrize	=	'';
		if(isset($TournamentDetailsArray[0]->Type) && $TournamentDetailsArray[0]->Type == 4){
			$titlCoin = '.tdisplay{display:none}';
			$titlCoin .= '.virtual{display:none}';
		}else if(isset($TournamentDetailsArray[0]->Type) && $TournamentDetailsArray[0]->Type == 3){
			$titlCoin = '.custom{display:none}';
			$titlCoin .= '.tdisplay{display:none}';
		}else{
			$titlCoin = '.custom{display:none}';
			$titlCoin .= '.virtual{display:none}';
		}
			
		$pdflocation = trim($pdflocation1) != '' && trim($pdflocation2) != '' ? $pdflocation1.'<br>'.$pdflocation2 : $pdflocation1.$pdflocation2;
		if(trim($pdflocation) != ''){
			$pdflocation = (substr($pdflocation, 0, -2));
		}
		$dev_image	=	isset($pdfDevDetail[0]->Photo) && !empty($pdfDevDetail[0]->Photo)? 'thumbnail/'.$pdfDevDetail[0]->Photo : '';
		$pdfDevImage	=	GAME_IMAGE_PATH."developer_logo.png";
		if($dev_image != '' ){
			if (!SERVER){
				if(file_exists(DEVELOPER_IMAGE_PATH_REL.$dev_image))
					$pdfDevImage = DEVELOPER_IMAGE_PATH.$dev_image;
			}
			else{
				if(image_exists(15,$dev_image))
					$pdfDevImage = DEVELOPER_IMAGE_PATH.$dev_image;
			}
		}
		
		///game details	
		$pdfgameDetail	=	$gameManageObj->selectGameDetails("Name, Photo"," id=".$TournamentDetailsArray[0]->fkGamesId);
		$game_logo	=	isset($pdfgameDetail[0]->Photo) && !empty($pdfgameDetail[0]->Photo) ? $pdfgameDetail[0]->Photo : '';
		$game_image_path = GAME_IMAGE_PATH.'add_game.png';
		if($game_logo != '' ){
			if (!SERVER){
				if(file_exists(GAMES_IMAGE_PATH_REL.$game_logo))
					$game_image_path = GAMES_IMAGE_PATH.$game_logo;
			}
			else{
				if(image_exists(10,$game_logo))
					$game_image_path = GAMES_IMAGE_PATH.$game_logo;
			}
		}
		$conversionValue = $coinConverted = '0';
		$setting_details	=	$adminLoginObj->getSettingDetails(" ConversionValue ",' id = 1 ');
		if(isset($setting_details) && is_array($setting_details) && count($setting_details)>0){
			foreach($setting_details as $key => $value){
				$conversionValue	=	$value->ConversionValue;
			}
		}
		$coinConverted = ($TournamentDetailsArray[0]->Prize/$conversionValue);
		$where	= ' id = 5 ';
		$pdfContent	=	$adminLoginObj->getCMS(' Content ',$where);
		$BannerDetailsArray = $tourManageObj->checkCouponBannerLink("File"," Type=2 AND InputType = 2 AND Status = 1 AND fkTournamentsId = ".$tournamentId);
		$file_types_array = array("jpeg","pjpeg","jpg","png");
		$BannerImage = '';
		if(is_array($BannerDetailsArray) && count($BannerDetailsArray) > 0 && $BannerDetailsArray[0]->File != ''){
			$fileExt = getFileExtension(basename($BannerDetailsArray[0]->File));
			$bannerFile = $BannerDetailsArray[0]->File;
			if(in_array($fileExt, $file_types_array)){
				$banner_file_path = '';
				if(SERVER){
					if(image_exists(12,$tournamentId.'/'.$bannerFile))	$banner_file_path = BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile;
					else	$banner_file_path = '';	
				}else{
					if(file_exists(BANNER_IMAGE_PATH_REL.$tournamentId.'/'.$bannerFile))	$banner_file_path = BANNER_IMAGE_PATH.$tournamentId.'/'.$bannerFile;
					else	$banner_file_path = '';	
				}
				if(!empty($banner_file_path))
				$BannerImage = ".banner {background:url(".$banner_file_path.") no-repeat;background-position: center top;background-size:400px auto;background-image-resize:4;} .banner td{height:120px;}";
			}
		}
		$htmlContent	= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
							<html>
							<head>
								<title>".$TournamentDetailsArray[0]->TournamentName." Tournament</title>
								<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
								<style>
									html,body {padding:0;margin:0;background:#D1D1D1;width:100%;height:100%;font:normal 16px 'nevis',arial;border:0;border-spacing:0;position:relative}
									.blue_top {color:#ffffff;background:#174a8e;font:normal 20px 'nevis',arial}
									.blue_top td{color:#ffffff;font:normal 20px 'nevis',arial;text-align: justify;}
									tr,td,table,div,span,img{padding:0;margin:0;border:0;border-spacing:0}".$BannerImage.$titlCoin.$pinDisplay."
								</style>
							</head>
							<body>".$pdfContent[0]->Content."</body>
							</html>";
		$htmlContent	= str_replace("{TOURNAMENT_NAME}",ucfirst($TournamentDetailsArray[0]->TournamentName).' Tournament',$htmlContent);
		$htmlContent	= str_replace("{TOURNAMENT_DATE}","Starts on ".date("F d, Y", strtotime($TournamentDetailsArray[0]->StartDate)),$htmlContent);
		$htmlContent	= str_replace("{TILT_COIN}",number_format($TournamentDetailsArray[0]->Prize),$htmlContent);
		$htmlContent	= str_replace("{TILT_COIN_CONVERTED}",number_format($coinConverted),$htmlContent);
		$htmlContent	= str_replace("{BRAND_IMAGE}",$pdfDevImage,$htmlContent);
		$htmlContent	= str_replace("{GAME_IMAGE}",$game_image_path,$htmlContent);
		$htmlContent	= str_replace("{GAME-NAME}",$pdfgameDetail[0]->Name,$htmlContent);
		$htmlContent	= str_replace("{TOURNAMENT_LOCATION}",$pdflocation,$htmlContent);
		$htmlContent	= str_replace("{ADMIN_IMAGE_PATH}",GAME_IMAGE_PATH,$htmlContent);
		$htmlContent	= str_replace("{HEIGHT}",$pinHeight,$htmlContent);
		$htmlContent	= str_replace("{TOURNAMENT_PIN}",$tournamentPin,$htmlContent);
		$htmlContent 	= iconv('Windows-1252', 'UTF-8//TRANSLIT',$htmlContent);
		if ( !file_exists(ABS_PDF_PATH_UPLOAD.$tournamentid) ){
			mkdir(ABS_PDF_PATH_UPLOAD.$tournamentid, 0777);
		}
		$fileName		= $tournamentid.'_'.time().'.pdf';
		$pdfName 		= $tournamentid.'/'.$fileName;
		ob_start();
		ob_clean();	
		$mpdf	= new mPDF('utf-8','A4','','Trebuchet MS' , 5 , 5 , 5 , 5 , 0 , 0);
		$mpdf->SetDisplayMode('fullwidth');
		$mpdf->debug = true;
		$mpdf->WriteHTML($htmlContent);
		$destFilePath 	= ABS_PDF_PATH_UPLOAD.$pdfName;
		$mpdf->Output($destFilePath,'F');
		
		if (file_exists($destFilePath) ){
			if (SERVER){
				if($pdfName!='') {
					if(image_exists(13,$pdfName))
						deleteImages(13,$pdfName);
				}
				uploadImageToS3($destFilePath,13,$pdfName);
				unlink($destFilePath);
			}
			$tourManageObj->updateTournamentDetail("PdfName='".$fileName."'","id=".$tournamentid);
		}
		$fileExistsPath	= PDF_PATH_REL.$tournamentid.'/'.$fileName;
		 if(file_exists($fileExistsPath)){
			$filesize		= filesize($fileExistsPath);
		} 
	}
	
}
function getFileExtension($filename)
{
		$name_fichier	=	trim($filename);
		$fileformat 	= 	explode(".",$name_fichier);
		$original_file_name = str_replace(" ","_",$fileformat[0]); 
		$count 			= 	count($fileformat);
		if($fileformat[$count-1] != '')
			 $fileformat = $fileformat[$count-1];
		else
			$fileformat = substr($name_fichier,(strpos($name_fichier,'.')+1));
		return strtolower($fileformat);
}

function replaceAccentByCode($string)
{
	$accentArray = array('–','—','à','è','ì','ò','ù',
'á','é','í','ó','ú','ý',
'â','ê','î','ô','û',
'ã','ñ','õ',
'ä','ë','ï','ö','ü','ÿ',
'À','È','Ì','Ò','Ù',
'Á','É','Í','Ó','Ú','Ý',
'Â','Ê','Î','Ô','Û',
'Ã','Ñ','Õ',
'Ä','Ë','Ï','Ö','Ü','Ÿ',
'¡','¿','Ç','ç','Œ','œ','º','ª',
'ß','Ø','ø','Å','å','Æ','æ','Þ','þ',
'Ð','ð','«','»','‹','›',
'‚','„','“','”','‘','’',
'¢','£','¥','€','¤','ƒ',
'>','<','÷','°','¬','±','µ',
'®','©','™','¶','•','·','§',
"%u2019","%u201C","%u201D","%u2013","%u2014","%u0153","%u0152","%u2039","%u203A","%u201A","%u201E","%u2018","%u0178",
"%u20AC","%u0192","%u2122","%u2022"
);
	$accentNumeriCodeArray = array("&#150;","&#151;","&#224;","&#232;","&#236;","&#242;","&#249;",
"&#225;","&#233;","&#237;","&#243;","&#250;","&#253;",
"&#226;","&#234;","&#238;","&#244;","&#251;", 
"&#227;","&#241;","&#245;",
"&#228;","&#235;","&#239;","&#246;","&#252;","&#255;",
"&#192;","&#200;","&#204;","&#210;","&#217;",
"&#193;","&#201;","&#205;","&#211;","&#218;","&#221;",
"&#194;","&#202;","&#206;","&#212;","&#219;",
"&#195;","&#209;","&#213;",
"&#196;","&#203;","&#207;","&#214;","&#220;","&#159;",
"&#161;","&#191;","&#199;","&#231;","&#338;","&#339;","&#186;","&#170;",
"&#223;","&#216;","&#248;","&#197;","&#229;","&#198;","&#230;","&#222;","&#254;",
"&#208;","&#240;","&#171;","&#187;","&#8249;","&#8250;",
"&#8218;","&#8222;","&#147;","&#148;","&#145;","&#146;",
"&#162;","&#163;","&#165;","&#8364;","&#164;","&#402;",
"&#62;","&#60;","&#247;","&#176;","&#172;","&#177;","&#181;",
"&#174;","&#169;","&#153;","&#182;","&#149;","&#183;","&#167;",
"&#146;","&#147;","&#148;","&#150;","&#151;","&#339;","&#338;","&#8249;","&#8250;","&#8218;","&#8222;","&#145;","&#159;",
"&#8364;","&#402;","&#153;","&#149;"
); 
	for($i=0;$i<count($accentArray);$i++)
	{
		if(isset($accentArray[$i]) && isset($accentNumeriCodeArray[$i])){
			$string	 =	str_replace($accentArray[$i],$accentNumeriCodeArray[$i],$string);
		}
	}
	//return $returnStr;
	return $string;
}
function forcedownload($filePath,$filesize)
{
	//Required for IE, otherwise Content-disposition is ignored
	if(ini_get('zlib.output_compression'))
		ini_set('zlib.output_compression', 'Off');
	$fileExt = getFileExtension(basename($filePath));
	switch($fileExt)
	{
		case "pdf": $ctype="application/pdf"; break;
		case "doc": $ctype="application/vnd.ms-word"; break;
		case "jpg": $ctype="image/jpg"; break;
		case "txt": $ctype="text/plain"; break;
		case "avi": $ctype="video/x-msvideo"; break;
		case "zip": $ctype="application/zip"; break;
		default: $ctype="application/force-download";
	}
	ob_clean_all();
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Type: $ctype");
	header("Content-Disposition: attachment; filename=".basename($filePath).";" );
	header("Content-Transfer-Encoding: binary");
	// header("Content-Length: ". $fileSize);
	readfile($filePath);
	if($fileExt == 'zip')
		unlink($filePath);
	exit();
} 
function ob_clean_all ()
{
	$ob_active = ob_get_length ()!== FALSE;
	while($ob_active)
	{
		ob_end_clean();
		$ob_active = ob_get_length ()!== FALSE;
	}
	return FALSE;
}
/* Get Time Zone of country by providing lat, lon
*  her we used to get Newyork Offset
*/
function getTimezoneFromLocation($lat,$long){
	$url = TIMEZONELATLNG."&location=".$lat.",".$long."&timestamp=".time();
	$result 	= curlRequest($url, 'GET', $data = null, $access_token = '');
	if(isset($result['status']) && $result['status'] == 'OK'){
		$dst = $result['rawOffset'];
		$utc = $result['dstOffset'];
		$timezone = ($utc + $dst)/3600;
		$num = $timezone;
		$res = ($num < 0 ? '-' : '+') . (abs($num) < 10 ? '0' : '') . abs((int)$num) . '.';
		$mins = round((abs(floatval($num)) - abs((int)$num)) * 60);
		$data = $res.($mins < 10 ? '0' : '') . $mins;
		return $data;
	}
	else{
		return '';
	}
}

function curlRequest($url, $method, $data = null, $access_token = '')
{
//echo '<pre>'; print_r($data); exit;
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
	if ($access_token != '') {
		# headers and data (this is API dependent, some uses XML)
		if ($method == 'PUT') {
		$headers = array(
						'Accept: application/json',
						'Content-Type: application/json',
						'Authorization: '.$access_token,
						);
		} else {
			$headers = array(
						'Authorization: '.$access_token
						);
		}
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	} 
	
	
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)"); 
	curl_setopt($handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
	
	switch($method) {
		case 'GET':
		break;
		case 'POST':
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		break;
		case 'PUT':
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		break;
		case 'DELETE':
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
		break;
	}
	$response = curl_exec($handle);
	if($_SERVER['REMOTE_ADDR'] == '172.21.4.176'){
		//echo'<div style="width:200px"><pre>';print_r($response);echo'</pre></div>';
	}
	
	$response = json_decode($response, true);
	return $response; 
}
?>
