<?php

class HTMLPurifier_HTMLFilter implements Zend_Filter_Interface {

    /**
     * The HTMLPurifier instance
     *
     * @var HTMLPurifier
     */
    protected $_instance;
    protected $config;

    /**
     * Constructor
     *
     * @param mixed $config
     * @return void
     */
    public function __construct() {
        if (!class_exists('HTMLPurifier_Bootstrap', false)) {
            require_once PROJECT_PATH .
                    '/lib/HTMLPurifier/HTMLPurifier/Bootstrap.php';
            spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'));
        }

        $this->configure();


        $this->_instance = new HTMLPurifier($this->config);
    }

    private function configure() {
        $this->config = HTMLPurifier_Config::createDefault();
        $this->config->set('HTML.Doctype', 'HTML 4.01 Strict');

        $this->config->set('HTML.AllowedElements', 'b,i,p,br,ul,ol,li,table,tr,td,thead,div,span,strong,em');
        $this->config->set('Attr.AllowedClasses', '');
        $this->config->set('HTML.AllowedAttributes', 'style,href,title,class,id');
        $this->config->set('AutoFormat.RemoveEmpty', true);
    }

    public function addAttribute($str) {
        return $this->config->getHTMLDefinition(true)->addAttribute($str);
    }
    
    public function addElement($str) {
        return $this->config->getHTMLDefinition(true)->addElement($str);
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, purified by HTMLPurifier
     *
     * @param string $value
     * @return string
     */
    public function filter($value) {
        $value = $this->_instance->purify($value);

        return $value;
    }

}
