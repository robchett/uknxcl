<?php
$time = time();

define('load_core', false);
require $_SERVER['DOCUMENT_ROOT'] . '/.core/config.php';
set_time_limit(0);

if (!is_dir(root . '/bin')) {
    mkdir(root . '/bin');
}
if (!is_dir(root . '/bin/' . $time)) {
    mkdir(root . '/bin/' . $time);
} else {
    die('A build already exists with the timestamp ' . $time);
}

$index_contents = '<?php';

$classes = [];
$res = \classes\db::select('_autoloader_log')
                  ->retrieve(['classes'])
                  ->execute();
if ($res->rowCount()) {
    $row = $res->fetchObject();
    $classes = explode(',', $row->classes);
    while ($row = $res->fetchObject()) {
        $classes = array_intersect($classes, explode(',', $row->classes));
    }

    foreach ($classes as &$class) {
        $class = str_replace('\\', '/', $class) . '.php';
    }
}
unset($class);

$file_mappings = [];
$files = \classes\get::recursive_glob(root . '/.core/', '*.php');
foreach ($files as $file) {
    $new_path = str_replace(root . '/.core/dependent/', '', $file);
    $new_path = str_replace(root . '/.core', 'core', $new_path);
    $file_mappings[$new_path] = $file;
}
$files = \classes\get::recursive_glob(root . '/inc', '*.php');
foreach ($files as $file) {
    $new_path = str_replace(root . '/inc/', '', $file);
    $file_mappings[$new_path] = $file;
}

$dependencies = [];
foreach ($classes as $class) {
    $dependencies[$class] = find_dependencies($file_mappings[$class]);
}

$met_dependencies = ['Serializable.php'];

$iterations = 0;
while ($classes && $iterations < 10) {
    foreach ($classes as $index => $class) {
        foreach ($dependencies[$class] as $dependant) {
            if (!in_array($dependant, $met_dependencies)) {
                continue 2;
            }
        }
        $index_contents .= sanitise_file($file_mappings[$class], $class);
        $met_dependencies[] = $class;
        unset($classes[$index]);
    }
    $iterations++;
}
if ($classes) {
    die('Dependencies on the following files could not be met. ' . implode(', ', $classes));
}

$index_contents .= sanitise_file(root . '/.core/config.php', 'core/config.php');

file_put_contents(root . '/index.php', $index_contents);


//ini_set('phar.readonly', 0);
//$phar = new Phar(root . '/bin/' . $time . '/application.phar');
//$phar->startBuffering();

//$index_contents = '<?php namespace { define("production", true); require "phar://application.phar/core/config.php"; }';
//$css = \classes\css\css::get();
//file_put_contents(root . '/bin/' . $time . '/live.css', $css);
//
//$js = \classes\js\js::get();
//file_put_contents(root . '/bin/' . $time . '/live.js', $js);
//
//$phar['index.php'] = $index_contents;
//$phar->stopBuffering();
//$phar->setMetadata('Testing testing 12345');
//$phar->createDefaultStub('index.php');


function sanitise_file($file, $class) {
    static $loaded = [];
    if (!in_array($class, $loaded)) {
        $loaded[] = $class;
        $contents = file_get_contents($file);
        $contents = preg_replace('#^(<\?[ph\s]*)#m', '', $contents);
        $contents = preg_replace('#(?>\s*)$#m', '', $contents);
        if (preg_match('#namespace (.*);#', $contents)) {
            $contents = preg_replace('#namespace (.*);#', 'namespace $1 {', $contents);
        } else {
            $contents = "namespace {\n" . $contents;
        }
        $contents = preg_replace('#^(//.*?)$#m', ' ', $contents);
        $contents = preg_replace('#\s(//.*?)$#m', ' ', $contents);
        $contents = preg_replace('#\s+#ms', ' ', $contents);
        return "\n" . $contents . "}";
    }
}

function find_dependencies($file) {
    $namespace = '';
    $aliases_raw = $aliases = $classes = $classes2 = $classes3 = $extends = $interfaces = $uses = [];
    $contents = file_get_contents($file);
    preg_match('#namespace (.*);#', $contents, $namespace);
    preg_match_all('#(?:use\s+([^\s^{]*)\s+as\s([^\s^;]*?);)+.*(?:class\s|trait\s)#ims', $contents, $aliases_raw);
    preg_match_all('#use\s+([^\s^;]*).*(?:class\s|trait\s)#ims', $contents, $aliases2_raw);
    preg_match_all('#class\s+([^\s^{]*)#', $contents, $classes);
    preg_match_all('#interface\s+([^\s^{]*)#', $contents, $classes2);
    preg_match_all('#trait\s+([^\s^{]*)#', $contents, $classes3);
    $classes = array_merge($classes[1], $classes2[1], $classes3[1]);

    if ($namespace && $namespace[1]) {
        $namespace = $namespace[1] . '\\';
    } else {
        $namespace = '';
    }

    foreach ($classes as $class) {
        if ($class && class_exists('\\' . $namespace . $class)) {
            $reflection = new ReflectionClass('\\' . $namespace . $class);
            $interfaces = array_merge($reflection->getInterfaceNames(), $interfaces);
            if ($extend = $reflection->getParentClass()) {
                $extends[] = $extend->getName();
            }
            $uses = array_merge($reflection->getTraitNames(), $uses);
        }
    }

    $namespace .= '/';
    if ($aliases_raw[1] || $aliases2_raw[1]) {
        $aliases = $aliases_flipped = $aliases2 = $aliases2_flipped = [];
        foreach ($aliases_raw[1] as $index => $alias) {
            $aliases[$alias] = $aliases_raw[2][$index];
        }
        $aliases_flipped = array_flip($aliases);

        foreach ($aliases2_raw[1] as $alias) {
            $parts = explode('\\', $alias);
            $aliases2[$alias] = end($parts);
        }
        $aliases2_flipped = array_flip($aliases2);
    }

    return array_map(function ($dependency) use ($namespace) {
        return str_replace('\\', '/', $dependency) . '.php';
    }, array_diff(array_merge($interfaces, $extends, $uses), $classes));
}