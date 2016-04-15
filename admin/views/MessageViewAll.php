	
				<?php if(isset($msg_result) && is_array($msg_result) && count($msg_result) > 0 ) { 
				?>
				<div style="height:25px;text-align:right;padding:10px;font-weight:bold;">No. of message(s) : <?php if(isset($msg_result)) echo count($msg_result) ; else '0'; ?></div>
				<?php } ?>
				<div class="scroll_content">	
				<?php 	$time = $deleteClass  = '';
						if(isset($msg_result) && is_array($msg_result) && count($msg_result) > 0){
							foreach($msg_result as $key=>$value){
							$message = '';
							$deleteClass = '';
							$time		              	=  displayDate($value->message_sent_date);
							$user_image               	= $value->Photo;
							$name = '';
							if(isset($value->FirstName))
								$name = ucfirst($value->FirstName);
							if(isset($value->LastName))
								$name   .= ' '.ucfirst($value->LastName);
							if( $name != '')
								$name = displayText($name,'30','1');
							else
								$name = $value->Email;
							if($name == '')
								$name = $value->Email;
							$j++;
							$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
							if(isset($user_image) && $user_image != ''){
								if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
									if(file_exists(USER_THUMB_IMAGE_PATH_REL.$user_image)){
										$image_path = USER_THUMB_IMAGE_PATH.$user_image;
									}
								}
								else{
									if(image_exists(1,$user_image)){
										$image_path = USER_THUMB_IMAGE_PATH.$user_image;
									}
								}
							}
							$deleteClass   = '2';
						?>
							<div class="conv_odd <?php if($value->ReadBy == '0') {  echo 'unreads'; } else echo 'read';?>" >
								<?php if($value->ReadBy == '0') {  echo '<i class="fa fa-envelope"></i> '; } else echo '';?>
								<span class="img" ><img src="<?php echo $image_path;?>" width="30" height="30" alt="image"></span>
								<span class="coment">
									<span class="user_name"><?php if(isset($value->UniqueUserId) && $value->UniqueUserId !='') { echo 'Guest'.$value->userId; } else echo $name ; ?></span>
									<span class="date"><i class="fa fa-clock-o"></i> <?php echo $time;?></span>
								</span>
								<p style="padding-left:5px;"><?php
									$type = $value->Type;
									if($type =='2'){
										$message = getCommentTextEmoji('web', $value->Message, $value->Platform) ;
										if($message  == "#?@?#1#?@?#")
											$message = "Contact Shared successfully ";
										else if($message == '#?@?#0#?@?#' )
											$message = "Contact has been unshared successfully";
										echo $message ;
									}
									?>
								</p>
							</div>
				<?php } ?>
				<div id="dummy"></div>
				<?php } else { ?>
					<div align="center" style="margin-top:20px" class="no_msgfound error-happy">Looks like you don't have any conversations yet....</div>
				<?php } ?>
			</div>					
<script>
	$(document).ready(function() {
		$(".chat_image_pop_up").colorbox({title:true});
	});
</script>

