<?php
class template {
    var $values = array();
    var $html;
    
    function get_template($template_name) {
        if (empty($template_name) || !file_exists($template_name)) {
            return false;
        }
        else {
            $this->html = file_get_contents($template_name);
        }
    }
    
    function set_value($key, $var) {
        $key = '{' . $key . '}';
        $this->values[$key] = $var;
    }
    
    function template_parse() {
        foreach ($this->values as $find => $replace) {
            $this->html = str_replace($find, $replace, $this->html);
        }
    }
}

$tpl = new template();
?>