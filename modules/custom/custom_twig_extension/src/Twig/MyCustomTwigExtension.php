<?php

namespace Drupal\custom_twig_extension\Twig;

class MyCustomTwigExtension extends \Twig_Extension {

    protected $fieldList = array(
        'nid',
        //'uuid',
        //'vid',
        'type',
        //'langcode',
        'title',
        //'uid',
        'status',
        //'created',
        //'changed',
        //'promote',
        //'sticky',
        //'revision_timestamp',
        //'revision_uid',
        //'revision_log',
        //'revision_translation_affected',
        //'default_langcode',
        //'path',
        'field_category',
        'field_eligibility',
        'field_how_best_to_access',
        'field_location_address',
        'field_location_hours',
        'field_location_phone',
        'field_location_url',
        'field_location_zip',
        'field_organization',
        'field_room_number',
        'field_services',
        'field_services_contact',
        'field_tags',
    );

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
                        // Filter with allowed field list
                        if (in_array($key5, $this->fieldList)) {
                            $fields[$key5] = $value5->getValue();
                        }
                    }
                }
            }
            $nodes[] = $fields;
        }
        $output = json_encode($nodes);
        return $output;
    }
}