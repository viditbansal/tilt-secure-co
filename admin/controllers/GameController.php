<?php
class GameController extends Controller
{
	function getTotalRecordCount(){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTotalRecordCount();
	}
	function getLeadersBoardList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getLeadersBoardList($fields,$condition);
	}
	function getGameList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameList($fields,$condition);
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
	function getSingleGameDetails($field,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getSingleGameDetails($field,$condition);
	}
	function changeGameStatus($deleteid,$updateStatus){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->changeGameStatus($deleteid,$updateStatus);
	}
	function getDeveloperList($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getDeveloperList($fields,$condition);
	}
	function approveDeveloper($id,$val){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->approveDeveloper($id,$val);
	}
	function getGameDetails($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameDetails($fields,$condition);
	}
	function getGameArray($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameArray($fields,$condition);
	}
	function getBrandArray($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getBrandArray($fields,$condition);
	}
	function getGameUser($fields,$condition){
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameUser($fields,$condition);
	}
	function getBrandUser($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getBrandUser($fields,$condition);
	}
	function userWinsCount($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->userWinsCount($fields,$condition);
	}
	function getUserList($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getUserList($fields,$condition);
	}
	function getTotalCoins($fields,$condition,$where)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTotalCoins($fields,$condition,$where);
	}
	function winnerIds($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->winnerIds($fields,$condition);
	}
	function getTurnsList($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTurnsList($fields,$condition);
	}
	function selectGameDeveloper($where)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->selectGameDeveloper($where);
	}
	function getUserTourn($fields,$condition,$where)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getUserTourn($fields,$condition,$where);
	}
	function getUserCoinsBalance($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getUserCoinsBalance($fields,$condition);
	}
	function getRedeemedUserCoins($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getRedeemedUserCoins($fields,$condition);
	}
	function getTotalCoinsIapp($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTotalCoinsIapp($fields,$condition);
	}
	function SingleGameDeveDetails($where)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->SingleGameDeveDetails($where);
	}
	function getGameDeveDetails($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameDeveDetails($fields,$condition);
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
	function deleteGameRules($id)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->deleteGameRules($id);
	}
	function updateGameRules($update_string,$id)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->updateGameRules($update_string,$id);
	}
	function updateGameDevDetails($update_string,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->updateGameDevDetails($update_string,$condition);
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
	function userWinnerCount($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->userWinnerCount($fields,$condition);
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
	function getRoundsList($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getRoundsList($fields,$condition);
	}
	function gameDeveloperDetails($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->gameDeveloperDetails($fields,$condition);
	}
	function getGameDevCommission($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getGameDevCommission($fields,$condition);
	}
	function getTournamentUsers($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getTournamentUsers($fields,$condition);
	}
	function getActiveTournamentUsers($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getActiveTournamentUsers($fields,$condition);
	}
	function getPlayerCount($fields,$condition)
	{
		if (!isset($this->GameModelObj))
			$this->loadModel('GameModel', 'GameModelObj');
		if ($this->GameModelObj)
			return $this->GameModelObj->getPlayerCount($fields,$condition);
	}
}
?>