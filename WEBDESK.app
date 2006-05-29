<?
global $app_acl, $app_desc,$action_desc;

$app_desc= array (
"name" 		=>"WEBDESK",                 //Name
"short_name"	=>N_("webdesk application"),                 //Short name
"description"	=>N_("webdesk application"),  //long description
"access_free"	=>"N",                    //Access type (ALL,RESTRICT)
"icon"		=>"webdesk.gif",             //Icon
"with_frame"	=>"Y",			//Use multiframe ? (Y,N)
"displayable"	=>"N",                    //Should be displayed on an app list
);

$app_acl = array (
  array ( "name" => "USER", "description" => N_("webdesk access"), "group_default"  => "Y" )
);

include_once("Lib.Prefix.php");
global $pubdir;

$action_desc = array (

// Transfer to CORE ?
  array( "acl" => "USER", "name" => "MAIN", "short_name" =>N_("webdesk main page"), "toc" => "N", "root" =>"Y"),

);

?>
