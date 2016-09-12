<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/5
 * Time: 10:26
 */

namespace x\Mvc;


use Smarty;
use x\BaseHandler;

class BaseController extends BaseHandler
{
    private $_routePath;
    private $_config;
    private $_smarty;

    protected $action;
    protected $controller;
    function _init(array $routePath, array $config)
    {
        $this->_routePath = $routePath;
        $this->_config = $config;
        $arr = explode("\\",get_class($this));
        $this->controller = array_pop($arr);

        $this->_smarty = new Smarty();
        $this->_smarty->setTemplateDir($config["templates"]);
        $this->_smarty->setCompileDir($config["templates_c"]);
//      $this->_smarty->setConfigDir(APP_TEMP_PATH.'app'.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR);
        $this->_smarty->setCacheDir($config["cache"]);
        $this->action = $routePath[0];
	    $this->pageInit();
        $this->$routePath[0]();
    }
    protected function pageInit(){}
    protected function assign($tpl_var, $value = null, $nocache = false){
        $this->_smarty->assign($tpl_var, $value, $nocache);
    }
    protected function display($tpl = null){
        if($tpl){
            $this->_smarty->display($tpl);
        }else{
            $this->_smarty->display($this->getTpl());
        }
        exit(0);
    }
    protected function getTpl(){
        return  $this->controller.DIRECTORY_SEPARATOR.$this->action.".tpl";
    }
}