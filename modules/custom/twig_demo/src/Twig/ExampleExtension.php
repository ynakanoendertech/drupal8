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
                'my_function', array($this, 'myFunction')
            ),
        );
    }

    public function generateGravatarUrl($email, $size = 100) {
        return sprintf('http://www.gravatar.com/avatar/%s?s=%s',
            md5($email),
            $size
        );
    }

    public function myFunction($content) {
        $outputArray = array();

        foreach($content as $key1 => $value1) {
            if ($key1 == '#view') {
                foreach($value1 as $key2 => $value2) {
                    if ($key2 == 'result') {
                        foreach($value2 as $key3 => $value3) {

                            $nodeFields = array();

                            foreach($value3 as $key4 => $value4) {
                                if ($key4 == '_entity') {
                                    foreach($value4 as $key5 => $value5) {
                                        $nodeFields[] = array($key5 => $value5->getValue());
                                    }
                                }
                            }

                            $outputArray[] = $nodeFields;
                        }
                    }
                }
            }
        }

        $output = json_encode($outputArray);

        return $output;
    }
}