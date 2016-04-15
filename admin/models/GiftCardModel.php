<?php
class GiftCardModel extends Model
{
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}

	function getGiftCardList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' gc.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_merchant_name']) && $_SESSION['mgc_sess_merchant_name'] != '')
			$condition .= " and m.MerchantName LIKE '%".$_SESSION['mgc_sess_merchant_name']."%' ";
		if(isset($_SESSION['mgc_sess_card_name']) && $_SESSION['mgc_sess_card_name'] != '')
			$condition .= " and gc.GiftCardName LIKE '%".$_SESSION['mgc_sess_card_name']."%' ";
		if(isset($_SESSION['mgc_sess_amount']) && $_SESSION['mgc_sess_amount'] != '')
			$condition .= " and gc.Amount =".$_SESSION['mgc_sess_amount']." ";
		if(isset($_SESSION['mgc_sess_giftCard_date']) && $_SESSION['mgc_sess_giftCard_date'] != '')
			$condition .= " and date(gc.DateCreated) = '".$_SESSION['mgc_sess_giftCard_date']."'";
		if(isset($_SESSION['mgc_sess_user_status']) && $_SESSION['mgc_sess_user_status'] != '')
			$condition .= " and gc.Visibility = '".$_SESSION['mgc_sess_user_status']."' ";
		if(isset($_SESSION['mgc_sess_available']) && $_SESSION['mgc_sess_available'] != '')
			$condition .= " and gc.Status = '".$_SESSION['mgc_sess_available']."' ";
		else
			$condition .= " and gc.Status != 0 ";
		// $sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->giftCardTable} as gc
		// 				WHERE 1 ".$condition." GROUP BY gc.id ORDER BY ".$sorting_clause." ".$limit_clause;
		$sql = "SELECT SQL_CALC_FOUND_ROWS gc.*, m.MerchantName, m.MerchantIcon
			FROM giftcards gc
			LEFT JOIN giftcardmerchants m ON gc.GiftCardMerchantId = m.id
			WHERE 1 ".$condition." GROUP BY gc.id ORDER BY ".$sorting_clause." ".$limit_clause;
		//error_log('SQL::: '.$sql);
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}

	function getUserGiftCardList($fields,$condition)
	{
		 $limit_clause='';
		$sorting_clause = ' gc.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		if(isset($_SESSION['mgc_sess_merchant_name']) && $_SESSION['mgc_sess_merchant_name'] != '')
			$condition .= " and gc.MerchantName LIKE '%".$_SESSION['mgc_sess_merchant_name']."%' ";
		if(isset($_SESSION['mgc_sess_amount']) && $_SESSION['mgc_sess_amount'] != '')
			$condition .= " and gc.Amount =".$_SESSION['mgc_sess_amount']." ";
		if(isset($_SESSION['mgc_sess_tournament_purchase']) && $_SESSION['mgc_sess_tournament_purchase'] != '')
			$condition .= " and date(r.DateCreated) = '".date('Y-m-d',strtotime($_SESSION['mgc_sess_tournament_purchase']))."'";

		$sql	=	" SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->redeemsTable} as r
						LEFT JOIN giftcards as gc on (gc.id = r.fkGiftCardsId)
						WHERE 1 ".$condition." ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}

	function createNewMerchant($name, $url){
		$sql	 =	"insert into giftcardmerchants set ";
		$sql	.=	"MerchantName='".$name."',";
		if(isset($url)	&&	trim($url!="")){
			$sql	.=	"MerchantIcon='".$url."'";
		}
		error_log('### create new merchant SQL:'.$sql);
		$this->result = $this->insertInto($sql);
		error_log('### create new merchant RESULT:'.$this->result);
		$insertId = $this->sqlInsertId();
		error_log('### create new merchant NEW ID:'.$insertId);
    return $insertId;
	}
	function updateGiftCardDetails($update_string,$condition){
		$sql	 =	"update {$this->giftCardTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}

	function changeGiftCardStatus($GiftIds,$updateStatus){
		$update_string 	= " Visibility =  ".$updateStatus;
		$condition 		= " id IN(".$GiftIds.") ";
		$this->updateGiftCardDetails($update_string,$condition);
	}

	function deleteGiftCardEntries($cardId){
		$like_postIds = $like_hashIds = $follow_hashIds = $hashIds = $postIds = '';
		$update_string 	= " Status = 0 ";
		$condition 		= " id IN(".$cardId.") ";
		$this->updateGiftCardDetails($update_string,$condition);
	}
	//inappPackageTable
	function updateInAppDetails($update_string,$condition){
		$sql	 =	"update {$this->inappPackageTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function selectGiftCardDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->giftCardTable} WHERE  ".$condition." ORDER BY id DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectGiftCardMerchantDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM giftcardmerchants WHERE  ".$condition." ORDER BY id DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectInAppDetails($fields,$condition)
	{
		$sql	=	"SELECT ".$fields." FROM  {$this->inappPackageTable} WHERE  ".$condition." ORDER BY id DESC";
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertInAppDetails($productArray)
	{
		$sql	 =	"insert into  {$this->inappPackageTable}  set ";
		if(isset($productArray['ProductId'])	&&	trim($productArray['ProductId']!=""))
			$sql	.=	"ProductId 	= 	'".$productArray['ProductId']."',";
		if(isset($productArray['product_name'])	&&	trim($productArray['product_name']!=""))
			$sql	.=	"Name	= 	'".$productArray['product_name']."',";
		if(isset($productArray['product_desc'])	&&	trim($productArray['product_desc']!=""))
			$sql	.=	"Description	= 	'".$productArray['product_desc']."',";
		if(isset($productArray['product_price'])	&&	trim($productArray['product_price']!=""))
			$sql	.=	"Price	= 	'".$productArray['product_price']."',";
		if(isset($productArray['product_status'])	&&	trim($productArray['product_status']!=""))
			$sql	.=	"Status	= 	'".$productArray['product_status']."',";

		$sql 	.=	" DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function insertGiftCardDetails($productArray){
		$sql	 =	"insert into  {$this->giftCardTable}  set ";
		if(isset($productArray['GiftCardId'])	&&	trim($productArray['GiftCardId']!="")){
			$sql	.=	"CardId 	= 	'".$productArray['GiftCardId']."',";
		}
		if(isset($productArray['GiftCardMerchantId'])	&&	trim($productArray['GiftCardMerchantId']!="")){
			$sql	.=	"GiftCardMerchantId	= 	'".$productArray['GiftCardMerchantId']."',";
		}
		if(isset($productArray['CurrencyCode'])	&&	trim($productArray['CurrencyCode']!="")){
			$sql	.=	"CurrencyCode	= 	'".$productArray['CurrencyCode']."',";
		}
		if(isset($productArray['MerchantName'])	&&	trim($productArray['MerchantName']!="")){
			$sql	.=	"MerchantName	= 	'".$productArray['MerchantName']."',";
		}
		if(isset($productArray['GiftCardName'])	&&	trim($productArray['GiftCardName']!="")){
			$sql	.=	"GiftCardName	= 	'".$productArray['GiftCardName']."',";
		}
		if(isset($productArray['Amount'])	&&	trim($productArray['Amount']!="")){
			$sql	.=	"Amount	= 	'".$productArray['Amount']."',";
		}
		if(isset($productArray['CoverImage'])	&&	trim($productArray['CoverImage']!="")){
			$sql	.=	"CoverImage	= 	'".$productArray['CoverImage']."',";
		}
		if(isset($productArray['MerchantIcon'])	&&	trim($productArray['MerchantIcon']!="")){
			$sql	.=	"MerchantIcon	= 	'".$productArray['MerchantIcon']."',";
		}
		if(isset($productArray['Status'])	&&	trim($productArray['Status']!="")){
			$sql	.=	"Status	= 	'".$productArray['Status']."',";
		}
		if(isset($productArray['Visibility'])	&&	trim($productArray['Visibility']!="")){
			$sql	.=	"Visibility	= 	'".$productArray['Visibility']."',";
		}else{
			$sql	.=	"Visibility	= 	'1',";
		}


		$sql 	.=	" DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		error_log('amih-MODEL-giftcard INSERT sql::-->>'.$sql);
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function getInAppList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['mgc_sess_product_id']) && $_SESSION['mgc_sess_product_id'] != '')
			$condition .= " and ProductId LIKE '%".$_SESSION['mgc_sess_product_id']."%' ";
		if(isset($_SESSION['mgc_sess_product_name']) && $_SESSION['mgc_sess_product_name'] != '')
			$condition .= " and Name LIKE '%".$_SESSION['mgc_sess_product_name']."%' ";
		if(isset($_SESSION['mgc_sess_product_status']) && $_SESSION['mgc_sess_product_status'] != '')
			$condition .= " and Status =".$_SESSION['mgc_sess_product_status']." ";
		if(isset($_SESSION['mgc_sess_product_price']) && $_SESSION['mgc_sess_product_price'] != '')
			$condition .= " and Price =".$_SESSION['mgc_sess_product_price']." ";
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS  ".$fields." FROM  {$this->inappPackageTable} WHERE  ".$condition."ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function changeProductStatus($ids,$updateStatus){
		$sql	 =	"update {$this->inappPackageTable}  set Status =".$updateStatus." where id IN (".$ids.")";
		$this->updateInto($sql);
	}
}
?>
