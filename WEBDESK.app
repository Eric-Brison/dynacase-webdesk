<?php
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
		  array ( "name" => "USER",   "description" => N_("webdesk access"), "group_default"  => "Y" ),
		  array ( "name" => "BARSET", "description" => N_("webdesk change top bar content"), "group_default"  => "Y" ),
		  array ( "name" => "APPCHG", "description" => N_("webdesk change default application"), "group_default"  => "Y" ),
		  array ( "name" => "ADMIN",  "description" => N_("webdesk admin") )
);

include_once("Lib.Prefix.php");
global $pubdir;

$action_desc = array (

  array( "acl" => "USER", "name" => "MAIN", "short_name" =>N_("webdesk main page"), "toc" => "N", "root" =>"Y"),

  array( "acl" => "USER",  "name" => "PORTAL",      "short_name" =>N_("portal page"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "PREFERENCES", "short_name" =>N_("preferences page"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "THEME", 	    "short_name" =>N_("theme preferences"), "toc" => "N", "root" =>"N"),
  array( "acl" => "ADMIN", "name" => "ADMIN",       "short_name" =>N_("administration page"), "toc" => "N", "available" =>"N"),
  array( "acl" => "ADMIN", "name" => "ADMINS",      "short_name" =>N_("list all admin pages"), "toc" => "N", "root" =>"N"),

  array( "acl" => "ADMIN",  "name" => "APPADMIN", "short_name" =>N_("webdesk general admnistration"), "toc" => "N", "root" =>"N"),

  // Service in portal management

  array( "acl" => "USER",  "name" => "GETJSSERVICE", "short_name" =>N_("return JS service description"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "ADDSERVICE", "short_name" =>N_("add service in user portal"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "SAVESVC", "short_name" =>N_("save service parameters"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "GEOSERVICE", "short_name" =>N_("position of service in user portal"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "DELSERVICE", "short_name" =>N_("del service from user portal"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "GURL", "short_name" =>N_("view any url"), "toc" => "N", "root" =>"N"),	
   array( "acl" => "USER",  "name" => "EGURL", "short_name" =>N_("edit for view any url"), "toc" => "N", "root" =>"N"),

  // For test
  array( "acl" => "USER",  "name" => "SVCTEST", "short_name" =>N_("service test view"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "ESVCTEST", "short_name" =>N_("service test edition"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "INCIDENT", "short_name" =>N_("service incident"), "toc" => "N", "root" =>"N"),

  array( "acl" => "USER",  "name" => "SVCMAIL", "short_name" =>N_("service mail"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "SVCLOCALMAIL", "short_name" =>N_("service local mail"), "toc" => "N", "root" =>"N"),
  array( "acl" => "USER",  "name" => "ESVCMAIL", "short_name" =>N_("service mail edition"), "toc" => "N", "root" =>"N"),

   array( "acl" => "USER",  "name" => "SVCCONTACT", "short_name" =>N_("service contact"), "toc" => "N", "root" =>"N"),
 
   array( "acl" => "USER",  "name" => "SVCARDOISE", "short_name" =>N_("service ardoise"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "ESVCARDOISE", "short_name" =>N_("service ardoise"), "toc" => "N", "root" =>"N"),

   array( "acl" => "USER",  "name" => "SVCRSS", "short_name" =>N_("service rss"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "ESVCRSS", "short_name" =>N_("service edit rss"), "toc" => "N", "root" =>"N"),
	
   array( "acl" => "USER",  "name" => "FREEDOM_SEARCH", "short_name" =>N_("service freedom search execution"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "EFREEDOM_SEARCH", "short_name" =>N_("service freedom search execution"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "FREEDOM_FSEARCH", "short_name" =>N_("service freedom search in familie"), "toc" => "N", "root" =>"N"),   array( "acl" => "USER",  "name" => "EFREEDOM_FSEARCH", "short_name" =>N_("service edit freedom search in familie"), "toc" => "N", "root" =>"N"),

   array( "acl" => "USER",  "name" => "GSVC", "short_name" =>N_("generic service"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "COUNTAFFECTDOC", "short_name" =>N_("number of affected document"), "toc" => "N", "root" =>"N"),
   array( "acl" => "USER",  "name" => "PREVIEWTHEME", "short_name" =>N_("preview of theme"), "toc" => "N", "root" =>"N")

);

?>
