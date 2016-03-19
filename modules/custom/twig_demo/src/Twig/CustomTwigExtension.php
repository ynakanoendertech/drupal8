<?php

namespace Drupal\twig_demo\Twig;

class CustomTwigExtension extends \Twig_Extension {

    public function getName() {
        return 'custom_twig_extension';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                'json_encode_view', array($this, 'jsonEncodeView')
            ),
        );
    }

    public function jsonEncodeView($content) {
        $nodes = array();
        foreach($content['#view']->result as $key3 => $value3) {
            $fields = array();
            foreach($value3 as $key4 => $value4) {
                if ($key4 == '_entity') {
                    foreach($value4 as $key5 => $value5) {
                        $fields[] = array($key5 => $value5->getValue());
                    }
                }
            }
            $nodes[] = $fields;
        }
        $output = json_encode($nodes);
        return $output;
    }
}