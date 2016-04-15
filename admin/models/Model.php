<?php
class Model extends Database
{
    /*Table Name*/
    var $adminTable              			=   "admins";
	var $userTable               			=   "users";
	var $wordsTable                 		=   "words";
	var $staticpagesTable           		=   "staticpages";
	var $gameTable           				=   "games";
	var $oauthClientEndpointsTable  		=   "oauth_client_endpoints";
	var $oauthClientEndpointsParamsTable	=	"oauth_client_endpoints_params";
	var $contactTable               		=   "contact";
	var $oauthSessionAccessTokensTable 		=	"oauth_session_access_tokens";
	var $oauthSessionTable 					=	"oauth_sessions";
	var $oauthClientsTable 					=	"oauth_clients";
	var $logTable 							=	"logs";
	var $tournamentsTable 					=	"tournaments";
	var $brandTable 						=	"brands";
	var $tournamentsPlayedTable 			=	"tournamentsplayed";
	var $chatsTable 						=	"chats";
	var $giftCardTable 						=	"giftcards";
	var $redeemsTable 						=	"redeems";
	var $settingsTable 						=	"settings";
	var $tournamentsStatsTable 				=	"tournamentsstats";
	var $brandpaymentsTable 				=	"brandpayments";
	var $cronTable 							=	"cronstats";
	var $pinStatsTable 						=	"pinstats";
	var $tournamentRulesTable 				=	"tournamentsrules";
	var $virtCoinsTable						=	"virtualcoins";
	var $tiltCoinsTable						=	"tiltcoins";
	var $locRestrictTable					=	"gftlocationrestriction";
	var $countriesTable 					=	"countries";
	var $statesTable 						=	"states";
	var $tiltlocationrestrictionTable 		=	"tiltlocationrestriction";
	var $iapTable							=	"inapppurchasedetails";
	var $gameDeveTable						=	"gamedevelopers";
	var $gamepaymentsTable					=	"gamepayments";
	var $activitiesTable					=	"activities";
	var $payHistTable 						=	"paymenthistory";
	var $aboutusTable 						=	"aboutus";
	var $websiteHomeTable 					=	"wesitehome";
	var $websiteDeveloperTable 				=	"websitedeveloper";
	var $websiteMediaTable 					=	"websitemedia";
	var $websiteTermofUseTable 				=	"websitetermofuse";
	var $GameRulesTable 				    =	"gamerules";
	var $GameFilesTable 				    =	"gamefiles";
	var $websitePrivacyPolicyTable 		    =	"websiteprivacypolicy";
	var $locationRestrictionTable 		    =	"locationrestriction";
	var $tournamentsCouponadLinkTable 		=	"tournamentscouponadlink";
	var $tournamentCustomPrizeTable 		=	"tournamentcustomprize";
	var $GamePushNotificationTable 			=	"gamespushnotification";
	var $inappPackageTable 					=	"inapppackages";
	var $GameLevelTable 					=	"gamelevel";
	var $eliminationTable 					=	"eliminationtable";
	var $eliminationPlayerTable 			=	"eliminationplayer";
	var $sdkTable 							=	"sdk";
	var $userDefaultImageTable 				=	"userdefaultimage";
	
	/*Table Name*/
	function Model()
	{
		global $globalDbManager;
		$this->dbConnect = $globalDbManager->dbConnect;
	}
}?>