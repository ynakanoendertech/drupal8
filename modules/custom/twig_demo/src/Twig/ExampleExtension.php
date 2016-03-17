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
            )
        );
    }

    public function generateGravatarUrl($email, $size = 100) {
        return sprintf('http://www.gravatar.com/avatar/%s?s=%s',
            md5($email),
            $size
        );
    }
}