<?php
/**
 * Index.php
 * @copyright (c) 2014, Nick Gejadze
 */

/**
 * Do not use development environment in production
 * Compression will gzip and remove whitespace from output
 */
define('DEVELOPMENT_ENVIRONMENT', false);
define('COMPRESS_OUTPUT', true);

/**
 * Check if it's NOT development environment and compression is enabled, run compression
 */
if(!DEVELOPMENT_ENVIRONMENT AND COMPRESS_OUTPUT):
	gzip_compression();
	ob_start("OutputCompression");
endif;

/**
 * Validate PHP version, minimum 5.3 Required
 */
checkPHPVersion();

/**
 * Check if Encoding is accepted and zlib is not already compressing, use ob_gzhandler
 */
function gzip_compression() {
	if( empty($_SERVER['HTTP_ACCEPT_ENCODING']) ) :
		return false;
	endif;
	if (( ini_get('zlib.output_compression') == 'On' OR 
		ini_get('zlib.output_compression_level') > 0 ) OR 
		ini_get('output_handler') == 'ob_gzhandler' ):
    	return false;
    endif;
	if ( extension_loaded( 'zlib' ) AND (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) ) :
    	ob_start('ob_gzhandler');
    endif;
}

/**
 * Remove extra whitespace from output
 */
function OutputCompression($buffer) {
    $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    $replace = array('>','<','\\1');
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}

/**
 * Compare minimum required php version to current installed version and kill the application if minimum required version is not detected
 */
function checkPHPVersion(){
	if (version_compare(PHP_VERSION, '5.3.0', '<')):
		exit("ERROR: Application Requires PHP 5.3 or above, you PHP version is ".PHP_VERSION.", Please contact your hosting provider to upgrade PHP version.");
	endif;
}

/**
 * Define application named constants
 */

define('VENDOR', realpath(dirname(__FILE__)."/vendor/"));

define('ROOT', realpath(dirname(__FILE__)));
define('PUBLIC_PATH', realpath(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);
define('APPDIR', 'Application');

require VENDOR . DIRECTORY_SEPARATOR . 'autoload.php';

new Bootstrap();

/**
 * Check if it's NOT development environment and compression is enabled, end compression
 */
if(!DEVELOPMENT_ENVIRONMENT AND COMPRESS_OUTPUT):
	ob_end_flush();
endif;