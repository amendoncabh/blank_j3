<?php defined('_JEXEC') or die;

define('DS', DIRECTORY_SEPARATOR);
define('J3_CACHE', dirname(__FILE__).DS.'cache'.DS);


class j3
{
	private $mootools_js = array(
		'#\/media\/system\/js\/mootools-core\.js#',
		'#\/media\/system\/js\/mootools-more\.js#',
		'#\/media\/system\/js\/mootools-core-uncompressed\.js#',
		'#\/media\/system\/js\/mootools-more-uncompressed\.js#',
		'#\/media\/system\/js\/modal\.js#',
		'#\/media\/system\/js\/modal-uncompressed\.js#',
		'#\/media\/system\/js\/core\.js#',
		'#\/media\/system\/js\/core-uncompressed\.js#',
		'#\/media\/system\/js\/caption\.js#',
		'#\/media\/system\/js\/caption-uncompressed\.js#'
	);
	
	private $jquery_js = array(
		'#\/media\/jui\/js\/jquery\.min\.js#'
	);
	
	private $yandex_mootools_cdn = 'http://yandex.st/mootools/1.3.1/mootools.min.js';
	private $google_mootools_cdn = '//ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js';
	
	private $yandex_jquery_cdn = 'http://yandex.st/jquery/1.8.2/jquery.min.js';
	private $google_jquery_cdn = '//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js';
	
	public $tpl;
	public $host;
	public $path;
	public $params;
	public $absolute_path;
	public $relative_path;
	
	public $_css = array();
	public $_js = array();
	
	public function __construct()
	{
		$this->tpl = JFactory::getDocument();
		$this->host = JFactory::getURI()->getHost();
		$this->absolute_path = JPATH_THEMES.DS.'blank_j3'.DS;
		$this->relative_path = '/templates/blank_j3/';
		$this->params = $this->tpl->params;
		
		
		$this->use_cdn = ((int)$this->params->get('use_cdn'));

				
		$this->enable_mootools = ((int)$this->params->get('enable_mootools')) === 1;
		$this->enable_bootstrap = ((int)$this->params->get('enable_bootstrap')) === 1;
		$this->enable_jquery = ((int)$this->params->get('enable_jquery')) === 1;
		$this->rewrite_css = ((int)$this->params->get('rewrite_css')) === 1;
		$this->responsive_css = ((int)$this->params->get('responsive_css')) === 1;
		
		$this->JUIEnable();
		$this->disableJS();
		$this->rewriteCSS();
		
		return $this;
	}
	
	public function disableJS()
	{
		
		$remove_js = array();
		
		if (!$this->enable_mootools)
			$remove_js = array_merge($remove_js, $this->mootools_js);
			
		if (!$this->enable_jquery && !$this->enable_bootstrap)
			$remove_js = array_merge($remove_js, $this->jquery_js);
		
		foreach ($this->tpl->_scripts as $script => $value)
		{
			
			if (preg_replace($remove_js, '', $script) !== '')
			{
				if ($script == '/media/system/js/mootools-core.js')
					switch ($this->use_cdn)
					{
						case 0: $this->_js[] = $script; break;
						case 1: $this->_js[] = $this->yandex_mootools_cdn; break;
						case 2: $this->_js[] = $this->google_mootools_cdn; break;
					}
				elseif ($script == '/media/jui/js/jquery.min.js')
					switch ($this->use_cdn)
					{
						case 0: $this->_js[] = $script; break;
						case 1: $this->_js[] = $this->yandex_jquery_cdn; break;
						case 2: $this->_js[] = $this->google_jquery_cdn; break;
					}
				else
					$this->_js[] = $script;
			}
		
			unset($this->tpl->_scripts[$script]);
		
		}
		
			
		foreach ($this->_js as $script)
		{
			$this->tpl->addScript($script);
		}
	}
	
	public function JUIEnable()
	{
		if ($this->enable_bootstrap)
		{
			JHtml::_('bootstrap.framework');
			JHtmlBootstrap::loadCss($this->responsive_css);
			if (!$this->responsive_css)
			{
				$this->tpl->addStyleSheet($this->relative_path.'css/bootstrap.min.css');
			}
		}
	}
	
	public function rewriteCSS()
	{	
		if ($this->rewrite_css)
		{
			$uri = JUri::getInstance();
			foreach ($this->tpl->_styleSheets as $src => $value)
			{
				$url_params = parse_url($src);
				if (!isset($url_params['host']) && $url_params['host'] != $this->host){
					$this->_css[] = JPATH_SITE.$url_params['path'];
					unset($this->tpl->_styleSheets[$src]);
				}
			}
			
			$buffer = '';
			foreach ($this->_css as $file)
			{
				if (file_exists($file))
					$buffer .= file_get_contents($file);
			}
			
			$compress_css = $this->_compressCSS($buffer);
			$md5_css = md5($gzip_css);
			
			$css_file = J3_CACHE.$md5_css.'.css';
			$gz_file = J3_CACHE.$md5_css.'.css.gz';
			
			if (!file_exists($css_file) || !file_exists($gz_file))
			{
				file_put_contents($css_file, $compress_css);
				$gz = gzopen($gz_file, "w9");
				if ($gz !== false)
					gzwrite($gz, $compress_css);
				gzclose($gz);
			}
			
			$this->tpl->addStyleSheet(str_replace($this->absolute_path, $this->relative_path, $css_file));
		}
	}
	
	protected function _compressCSS($buffer) 
	{
	    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
	    $buffer = str_replace('{ ', '{', $buffer);
	    $buffer = str_replace(' }', '}', $buffer);
	    $buffer = str_replace('; ', ';', $buffer);
	    $buffer = str_replace(', ', ',', $buffer);
	    $buffer = str_replace(' {', '{', $buffer);
	    $buffer = str_replace('} ', '}', $buffer);
	    $buffer = str_replace(': ', ':', $buffer);
	    $buffer = str_replace(' ,', ',', $buffer);
	    $buffer = str_replace(' ;', ';', $buffer);
	
	    return $buffer;
	}
}