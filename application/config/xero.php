<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration options for Xero private application
 */

$config = array(
	'consumer'	=> array(
    	'key'		=> 'YOUR_XERO_KEY',
    	'secret'	=> 'YOUR_XERO_SECRET'
    ),
    'certs'		=> array(
    	'private'  	=> APPPATH.'certs/private-key.pem',
    	'public'  	=> APPPATH.'certs/public-key.cer'
    ),
    'format'    => 'xml'
);