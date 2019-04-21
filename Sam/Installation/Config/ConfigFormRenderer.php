<?php

namespace Sam\Installation\Config;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Class ConfigFormRenderer render config form web layout
 * @package Sam\Installation\Config
 * @author Vakulenko Yura
 */
class ConfigFormRenderer extends \CustomizableClass
{

    const VIEWS_PATH = BASE_DIR . DS . 'views';

    /**
     * View data array
     * @var array
     */
    protected $viewData = [];

    /**
     * @return $this
     */
    public static function getInstance()
    {
        $instance = parent::_getInstance(__CLASS__);
        return $instance;
    }

    /**
     * Render view template with view Data from $this->viewData
     * @return void
     */
    public function render()
    {
        $tmplData = $this->viewData;

        if ($tmplData['page'] != 'error') {
            $tmplData['formActionUrl'] = BASE_URL;
        }

        $tmplData['baseUrl'] = BASE_URL;
        $tmplData['assetsUrl'] = BASE_URL . '/assets/';

        require_once(self::VIEWS_PATH . DS . $tmplData['page'] . '.php');
    }

    /**
     * Store view data to $this->viewData
     * @param $viewData
     */
    public function setViewData($viewData)
    {
        $this->viewData = (is_array($viewData) && count($viewData)) ? $viewData
            : [];
    }

}