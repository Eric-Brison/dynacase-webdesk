<?php
/* $Id: phpweather-default.php,v 1.1 2006/06/29 16:14:08 marc Exp $
 * Freedom service configuration for phpweather
 */

/******************************************************************/
/*                        General Options                         */
/******************************************************************/
/* The following options have been changed:                       */
$this->properties['verbosity'] = '3';
$this->properties['icao'] = 'LFBO';
$this->properties['pref_units'] = 'only_metric';
$this->properties['language'] = 'fr';
$this->properties['use_proxy']     = false;
$this->properties['proxy_host']    = '';
$this->properties['proxy_port']    = 3128;
/******************************************************************/
/*                        Database Options                        */
/******************************************************************/
/* The following options have been changed:                       */

$this->properties['db_type']       = 'pgsql';
$this->properties['db_pconnect']   = true;
$this->properties['db_hostname']   = 'localhost';
$this->properties['db_port']       = 5432;
$this->properties['db_username']   = 'anakeen';
$this->properties['db_password']   = 'anakeen';
$this->properties['db_database']   = 'phpweather';
$this->properties['db_metars']     = 'pw_metars';      
$this->properties['db_tafs']       = 'pw_tafs';
$this->properties['db_stations']   = 'pw_stations';
$this->properties['db_countries']  = 'pw_countries';
/******************************************************************/
/*                       Rendering Options                        */
/******************************************************************/
/* All options are at their default values.                       */

?>
