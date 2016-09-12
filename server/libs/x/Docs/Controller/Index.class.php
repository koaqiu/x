<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/10
 * Time: 21:34
 */

namespace x\Docs\Controller;


use Parsedown;
use x\App;
use x\Mvc\BaseController;

class Index extends BaseController {

	function pageInit() {
		$config = App::getConfig();
		$this->assign("title", $config['appTitle']);
	}

	function Index(){
		$Parsedown = new Parsedown();
		$content = file_get_contents(WEB_ROOT."..".DIRECTORY_SEPARATOR."README.md");
		$html = $Parsedown->parse($content);
		$this->assign("mdContent",$html);
		$this->display();
	}

	function Api(){

		$cachePath = APP_TEMP_PATH."api_cache.php";
		if(file_exists_case($cachePath)){
			$apiList = include $cachePath;
		}else{
			$apiList = array();
		}
		ksort ($apiList);
		$this->assign("apiList",$apiList);
		$this->display();
	}
}