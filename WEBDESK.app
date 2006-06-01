<?
global $app_acl, $app_desc,$action_desc;

$app_desc= array (
"name" 		=>"WEBDESK",                 //Name
"short_name"	=>N_("webdesk"),                 //Short name
"description"	=>N_("webdesk application"),  //long description
"access_free"	=>"N",                    //Access type (ALL,RESTRICT)
"icon"		=>"webdesk.gif",             //Icon
"with_frame"	=>"Y",			//Use multiframe ? (Y,N)
"displayable"	=>"Y",                    //Should be displayed on an app list
);

$app_acl = array (
  array ( "name" => "USER", "description" => N_("webdesk access"), "group_default"  => "Y" )
);

include_once("Lib.Prefix.php");
global $pubdir;

$action_desc = array (

  array( "acl" => "USER", "name" => "MAIN", "short_name" =>N_("webdesk main page"), "toc" => "N", "root" =>"Y"),

  array( "acl" => "USER",  "name" => "PORTAL",      "short_name" =>N_("portal page"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "PREFERENCES", "short_name" =>N_("preferences page"), "toc" => "N", "root" =>"N"),
  array( "acl" => "ADMIN", "name" => "ADMIN",       "short_name" =>N_("administration page"), "toc" => "N", "root" =>"N"),

  array( "acl" => "USER",  "name" => "SAVESVC", "short_name" =>N_("save service definition"), "toc" => "N", "root" =>"N"),

  // Service in portal management
  array( "acl" => "USER",  "name" => "GETJSSERVICE", "short_name" =>N_("return JS service description"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "ADDSERVICE", "short_name" =>N_("add service in user portal"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "MODSERVICE", "short_name" =>N_("modify service in user portal"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "DELSERVICE", "short_name" =>N_("del service from user portal"), "toc" => "N", "root" =>"N"),

  // For test
  array( "acl" => "USER",  "name" => "SVCTEST", "short_name" =>N_("service test view"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "ESVCTEST", "short_name" =>N_("service test edition"), "toc" => "N", "root" =>"N"),

	
);

?>
