<?php

namespace Drupal\twig_demo\Twig;

class ExampleExtension extends \Twig_Extension {

    public function getName() {
        return 'example';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                'gravatar', array($this, 'generateGravatarUrl')
            ),
            new \Twig_SimpleFunction(
                'json_encode_view', array($this, 'jsonEncodeView')
            ),
        );
    }

    public function generateGravatarUrl($email, $size = 100) {
        return sprintf('http://www.gravatar.com/avatar/%s?s=%s',
            md5($email),
            $size
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