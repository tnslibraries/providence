<?php
/* ----------------------------------------------------------------------
 * viewers/apps/tilepic.php : given a Tilepic image url on the localhost and a tile number, will return the tile image
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2004 - 2012 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */

ini_set("display_errors", "On");
if (defined('E_DEPRECATED')) {	// PHP 5.3/5.4
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
} else {	// PHP <= 5.2
	error_reporting(E_ALL & ~E_NOTICE);
}

# Only works with JPEG tiles; we skip extracting tile mimetype from the Tilepic file
# to save time. If you are using non-JPEG tiles (unlikely, right?) then change the 
# Content-type header below.
#require("../../setup.php");
define("__CA_BASE_DIR__", "/collectiveaccess/app/public_html/admin");
define("__CA_APP_DIR__", __CA_BASE_DIR__."/app");
define("__CA_APP_CONFIG__", __CA_APP_DIR__."/config");
define("__CA_CONF_DIR__", __CA_APP_DIR__."/config");
define("__CA_LIB_DIR__", __CA_APP_DIR__."/lib");
define("__CA_MODELS_DIR__", __CA_APP_DIR__."/models");
define("__CA_APP_NAME__", "");
set_include_path(__CA_LIB_DIR__.'/ca'.PATH_SEPARATOR.__CA_LIB_DIR__.'/core'.PATH_SEPARATOR.__CA_MODELS_DIR__.PATH_SEPARATOR.get_include_path());

require_once(__CA_BASE_DIR__.'/vendor/autoload.php');	// composer
require_once(__CA_LIB_DIR__."/core/Zend/Cache.php");
require_once(__CA_LIB_DIR__."/core/Cache/MemoryCache.php"); // is used in utilityHelpers
require_once(__CA_LIB_DIR__."/core/Cache/ExternalCache.php"); // is used in utilityHelpers
require_once(__CA_LIB_DIR__."/core/Cache/CompositeCache.php"); // is used in utilityHelpers
require_once(__CA_APP_DIR__."/helpers/utilityHelpers.php");
require_once(__CA_LIB_DIR__."/core/Parsers/TilepicParser.php");
require_once(__CA_LIB_DIR__."/core/Configuration.php");

$vo_conf = Configuration::load();

$ps_filepath = 	$_REQUEST["p"];
$pn_tile = $_REQUEST["t"];

$ps_filepath = preg_replace("/^http[s]{0,1}:\/\/[^\/]+/i", "", $ps_filepath);
$ps_filepath = preg_replace("/\.tpc\$/", "", $ps_filepath);
$ps_filepath = str_replace($vo_conf->get('ca_media_url_root'),"", $ps_filepath);
$ps_filepath = preg_replace("/[^A-Za-z0-9_\-\/]/", "", $ps_filepath);

$vs_media_root = "/collectiveaccess/app/public_html"; //$vo_conf->get('ca_media_root_dir');
if (file_exists($vs_media_root."/".$ps_filepath.".tpc")) {
	header("Content-type: image/jpeg");
	$vs_output = TilepicParser::getTileQuickly($vs_media_root."/".$ps_filepath.".tpc", $pn_tile);
	header("Content-Length: ".strlen($vs_output));
	print $vs_output;
	exit;
} else {
	die("Invalid file");
}
?>