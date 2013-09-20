<?php

class auto_loader {

    public function __construct() {
        spl_autoload_register(['auto_loader', 'load']);
    }

    public function load($class) {
        $namespace = '';
        if (false !== ($namespace_pos = strripos($class, '\\'))) {
            $namespace = substr($class, 0, $namespace_pos);
            $class = substr($class, $namespace_pos + 1);
        }
        $class = str_replace(array('_iterator', '_array'), '', $class);

        if ($class == 'controller' && $this->load_module($class, $namespace)) {
        } else if ($this->load_object($class, $namespace)) {
        } else if ($this->load_form($class, $namespace)) {
        } else if ($this->load_class($class, $namespace)) {
        } else {
            return false;
        }
        return true;
    }

    public function load_module($class, $namespace) {
        if (is_readable($filename = root . '/inc/module/' . $namespace . '/' . $class . '.php')) {
            require($filename);
            return true;
        }
        return false;
    }

    public function load_object($class, $namespace) {
        if ($namespace !== '') {
            $namespace = 'module/' . $namespace . '/';
        }
        if (is_readable($filename = root . '/inc/' . $namespace . 'object/' . $class . '.php')) {
            require($filename);
            return true;
        }
        return false;
    }

    public function load_form($class, $namespace) {
        if ($namespace !== '') {
            $namespace = 'module/' . $namespace . '/';
        }
        if (is_readable($filename = root . '/inc/' . $namespace . 'form/' . $class . '.php')) {
            require($filename);
            return true;
        }
        return false;
    }

    public function load_class($class, $namespace) {
        if ($namespace !== '') {
            $namespace .= '/';
        }
        if (is_readable($filename = core_dir . '/classes/' . $namespace . $class . ".php")) {
            require($filename);
            return true;
        }
        return false;
    }
}