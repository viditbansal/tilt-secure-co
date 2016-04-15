<?php
class Model extends Database
{
    var $gameDeveTable           				=   "gamedevelopers";
	var $wordsTable                 			=   "words";
	var $tournamentsTable 						=	"tournaments";
	var $gameTable           					=   "games";
	var $tournamentsPlayedTable 				=	"tournamentsplayed";
	var $adminTable              				=   "admins";
	var $locRestrictTable						=	"locationrestriction";
	var $countriesTable 						=	"countries";
	var $gamePaymentTable 						=	"gamepayments";
	var $paymentHistoryTable           			=   "paymenthistory";
	var $tournamentCoupAdLinkTable				=	'tournamentscouponadlink';
	var $pinStatsTable							=	'pinstats';
	var $customPrizeTable						=	'tournamentcustomprize';
	var $GameRulesTable 				        =	"gamerules";
	var $GameFilesTable 				        =	"gamefiles";
	var $GamePushNotificationTable 				=	"gamespushnotification";
	var $settingsTable 						    =	"settings";
	var $tournamentRulesTable 				    =	"tournamentsrules";
	var $activityTable 							=	"activities";
	var $userTable               				=   "users";
	var $tournamentsStatsTable 					=	"tournamentsstats";
	var $eliminationTable 						=	"eliminationtable";
	var $eliminationPlayerTable 				=	"eliminationplayer";
	var $websiteTermofuseTable 					=	"websitetermofuse";
	var $websitePrivacypolicyTable 				=	"websiteprivacypolicy";
	var $GameLevelTable 				        =	"gamelevel";
	var $sdkTable 				       			=	"sdk";
	var $locationRestrictionTable      			=	"locationrestriction";
	var $staticpagesTable           			=   "staticpages";
	var $followTable 							=	"follow";
	var $deviceTokenTable 						=	"devicetoken";
	var $gamePaymentsTable 						=	"gamepayments";
	function Model()
	{
		global $globalDbManager;
		$this->dbConnect = $globalDbManager->dbConnect;
	}
}?>