<?php 
namespace Sam\Installation\Config;

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

/**
 * Class ConfigFormRenderer render config form web layout
 * @package Sam\Installation\Config
 * @author Vakulenko Yura
 */
class ConfigFormRenderer extends \CustomizableClass
{

  protected $viewData = [];

  const VIEWS_PATH = BASE_DIR . DS . 'views';  

  /** 
   * Class instantiation method
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

    if ($tmplData['page'] !='error') {
      $tmplData['formActionUrl'] = BASE_URL;  
    }   

    // building html Assets url
    $parseBaseUrl = parse_url(BASE_URL);    
    $urlPath = '';    
    if (isset($parseBaseUrl['path']) && !empty($parseBaseUrl['path'])) {
      $explPath = explode('/', $parseBaseUrl['path']);      
      foreach ($explPath as $k => $path) {
        if (stripos($path, '.php') !== false) {
          unset($explPath[$k]);
        }
      }
      $urlPath = implode('/', $explPath);      
    }    
    $tmplData['assetsUrl'] = $parseBaseUrl['scheme'].'://'.$parseBaseUrl['host'].$urlPath.'/assets/';



    require_once (self::VIEWS_PATH . DS . $tmplData['page'].'.php');
  }


    /**
     * Store view data to $this->viewData
     * @param $viewData
     */
    public function setViewData($viewData)
  {
    $this->viewData = (is_array($viewData) && count($viewData)) ?  $viewData : [];
  }


  
}