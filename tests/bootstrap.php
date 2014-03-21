<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__).DS.'..').DS.'application');

defined('VENDOR_PATH')
    || define('VENDOR_PATH', realpath(dirname(__FILE__).DS.'..').DS.'vendor');

set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH,
    VENDOR_PATH,
    get_include_path()
)));

$composer_autoload = VENDOR_PATH.DS.'autoload.php';

if( !file_exists($composer_autoload) ){
    throw new Exception('Please, install composer!!');
}

require_once($composer_autoload);