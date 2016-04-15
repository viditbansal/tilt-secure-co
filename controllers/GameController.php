<?php
class GameController extends Controller
{
	function getTotalRecordCount(){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTotalRecordCount();
	}
	function getGameList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameList($fields,$condition);	
	}
	function GameList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->GameList($fields,$condition);	
	}
	function insertGameDetails($array){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->insertGameDetails($array);	
	}
	function selectGameDetails($field,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->selectGameDetails($field,$condition);	
	}
	function updateGameDetails($field,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->updateGameDetails($field,$condition);	
	}
	function selectWordDetails(){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->selectWordDetails();	
	}
	function getTournamentList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTournamentList($fields,$condition);	
	}
	function getTourGameList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTourGameList($fields,$condition);	
	}
	function insertGameRules($values)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->insertGameRules($values);
	}
	function selectGameRules($where)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->selectGameRules($where);
	}
	function updateGameRules($update_string,$id)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->updateGameRules($update_string,$id);
	}
	function selectGameFiles($where)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->selectGameFiles($where);
	}
	function insertGameFiles($values)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->insertGameFiles($values);
	}
	function updateGameFiles($update_string,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->updateGameFiles($update_string,$condition);
	}
	function insertGamePushNotification($input)	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->insertGamePushNotification($input);
	}
	function updateGamePushNotification($field,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->updateGamePushNotification($field,$condition);
	}
	function selectGamePushNotification($field,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->selectGamePushNotification($field,$condition);
	}
}
?>