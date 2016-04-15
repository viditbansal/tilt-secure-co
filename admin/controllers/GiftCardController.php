<?php
class GiftCardController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->getTotalRecordCount();
	}
	function getGiftCardList($fields,$condition)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->getGiftCardList($fields,$condition);
	}
  function createNewMerchant($name, $url){
    if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->createNewMerchant($name,$url);    
  }
	function updateGiftCardDetails($update_string,$condition)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->updateGiftCardDetails($update_string,$condition);
	}

	function changeGiftCardStatus($GiftIds,$updateStatus)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->changeGiftCardStatus($GiftIds,$updateStatus);
	}

	function deleteGiftCardEntries($cardId)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->deleteGiftCardEntries($cardId);
	}
	function getUserGiftCardList($fields,$condition)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->getUserGiftCardList($fields,$condition);
	}
  function selectGiftCardMerchantDetails($fields,$condition){
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->selectGiftCardMerchantDetails($fields,$condition);
	}
  function selectGiftCardDetails($fields,$condition){
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->selectGiftCardDetails($fields,$condition);
	}
  function selectInAppDetails($fields,$condition)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->selectInAppDetails($fields,$condition);
	}
	function getInAppList($fields,$condition)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->getInAppList($fields,$condition);
	}
  function insertInAppDetails($insert_values)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->insertInAppDetails($insert_values);
	}
  function insertGiftCardDetails($insert_values)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->insertGiftCardDetails($insert_values);
	}
	function updateInAppDetails($update_string,$condition)
	{
		if (!isset($this->GiftCardModelObj)){
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
    }
		if ($this->GiftCardModelObj){
			return $this->GiftCardModelObj->updateInAppDetails($update_string,$condition);
    }
	}
	function changeProductStatus($ids,$updateStatus)
	{
		if (!isset($this->GiftCardModelObj))
			$this->loadModel('GiftCardModel', 'GiftCardModelObj');
		if ($this->GiftCardModelObj)
			return $this->GiftCardModelObj->changeProductStatus($ids,$updateStatus);
	}
}
