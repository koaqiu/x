<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 10:12
 */


define('WEB_ROOT',__DIR__.DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath(WEB_ROOT.'..'.DIRECTORY_SEPARATOR.'server').DIRECTORY_SEPARATOR);
define('APP_TEMP_PATH', realpath(WEB_ROOT.'..'.DIRECTORY_SEPARATOR.'temp').DIRECTORY_SEPARATOR);
define('APP_LIBS', APP_ROOT.'libs'.DIRECTORY_SEPARATOR);
define('APP_SRC', APP_ROOT.'src'.DIRECTORY_SEPARATOR);
$arr = explode(DIRECTORY_SEPARATOR,__FILE__);
define('ENTRY_FILE',array_pop($arr));


//var_dump(WEB_ROOT);
//var_dump(APP_ROOT);

require_once APP_LIBS.'CLoader.class.php';

new x\App('Api');
