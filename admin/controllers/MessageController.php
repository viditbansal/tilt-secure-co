<?php
class MessageController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->MessageModelObj))
			$this->loadModel('MessageModel', 'MessageModelObj');
		if ($this->MessageModelObj)
			return $this->MessageModelObj->getTotalRecordCount();
	}
	function getUsersChatLists($from_user_id)
	{
		if (!isset($this->MessageModelObj))
			$this->loadModel('MessageModel', 'MessageModelObj');
		if ($this->MessageModelObj)
			return $this->MessageModelObj->getUsersChatLists($from_user_id);
	}
	function messageLists($from_user_id,$to_user_id)
	{
		if (!isset($this->MessageModelObj))
			$this->loadModel('MessageModel', 'MessageModelObj');
		if ($this->MessageModelObj)
			return $this->MessageModelObj->messageLists($from_user_id,$to_user_id);
	}
	function updateMessageReadStatus($to_user_id,$from_user_id)
	{
		if (!isset($this->MessageModelObj))
			$this->loadModel('MessageModel', 'MessageModelObj');
		if ($this->MessageModelObj)
			return $this->MessageModelObj->updateMessageReadStatus($to_user_id,$from_user_id);
	}
	function selectMessageDetails($user_id)
	{
		if (!isset($this->MessageModelObj))
			$this->loadModel('MessageModel', 'MessageModelObj');
		if ($this->MessageModelObj)
			return $this->MessageModelObj->selectMessageDetails($user_id);
	}
	function getBlockedIds($userId)
	{
		if (!isset($this->MessageModelObj))
			$this->loadModel('MessageModel', 'MessageModelObj');
		if ($this->MessageModelObj)
			return $this->MessageModelObj->getBlockedIds($userId);
	}
}
?>
