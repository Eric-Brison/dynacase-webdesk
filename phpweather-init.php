<?php

define('PHPWEATHER_BASE_DIR', "/home/httpd/html/phpweather");
require_once(PHPWEATHER_BASE_DIR . '/db_layer.php');

$db = new db_layer();
if ($db->db->create_tables()) {
  $num_rows = 0;
  $num_countries = 0;
  echo "phpweather tables have been created. They will now be filled with data, please wait...\n";
  flush();
  $fp = fopen(PHPWEATHER_BASE_DIR . '/stations.csv', 'r');
  while ($row = fgets($fp, 1024)) {
    $row = trim($row);
    if (substr($row, 0, 2) == '##' && substr($row, -2) == '##') {
      /* We've found a country */
      $cc = substr($row, 3, 2); // The country-code.
      $country = substr($row, 6, -3); // The name of the country.
      $countries[$cc] = $country;
      $num_countries++;
      //echo "<p>Now processing stations in $country.</p>\n";
    } elseif (!empty($row) && $row[0] != '#') {
      list($icao, $name) = explode(';', $row, 2);
      $num_rows++;
      $data[$cc][$icao] = $name; 
    } 
  } 
  $db->db->insert_stations($data, $countries);
  $db->db->disconnect();
  echo "Data about $num_rows stations from $num_countries countries were inserted.\n";
} else {
  echo "There was a problem with the creation of the tables!\n";
}
?>
