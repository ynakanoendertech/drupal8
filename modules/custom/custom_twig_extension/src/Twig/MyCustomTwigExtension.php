<?php

namespace Drupal\custom_twig_extension\Twig;

class MyCustomTwigExtension extends \Twig_Extension {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'custom_twig_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                'json_encode_location_view', array($this, 'jsonEncodeLocationView')
            ),
        );
    }

    /**
     * Helper function to remove new line characters from retrieved string values before JSON generation
     */
    public function removeNewlineCharacters($str) {
        if ($str == '') {
            return '';
        }
        $formatted = $str;
        $formatted = trim($formatted);
        $formatted = str_replace("\n", "", $formatted);
        $formatted = str_replace("\r", ",", $formatted);
        $formatted = str_replace("\n",',', $formatted);
        $formatted = rtrim($formatted, "\x00..\x1F");
        return $formatted;
    }

    /**
     * JSON encode Location view content data
     */
    public function jsonEncodeLocationView($content) {
        $nodes = array();
        foreach($content['#view']->result as $key3 => $value3) {
            $fields = array();
            foreach($value3 as $key4 => $value4) {
                if ($key4 == '_entity') {
                    foreach($value4 as $key5 => $value5) {

                        switch ($key5) {

                            // Simple fields
                            case 'nid':
                            case 'type':
                            case 'status':
                            case 'title':
                            case 'field_location_zip':
                            case 'field_room_number':
                            case 'field_location_url':
                                $fields[$key5] = $value5->getString();
                                break;

                            // HTML fields
                            case 'field_eligibility':
                            case 'field_how_best_to_access':
                            case 'field_location_address':
                            case 'field_location_hours':
                            case 'field_location_phone':
                            case 'field_services':
                            case 'field_services_contact':
                                $fieldValueData = $value5->getValue();
                                if (isset( $fieldValueData[0]['value'] )) {
                                    $fields[$key5] = $this->removeNewlineCharacters( $fieldValueData[0]['value'] );
                                }
                                break;

                            // Multi value fields
                            case 'field_organization':
                            case 'field_category':
                            case 'field_tags':
                                $fieldValueData = $value5->getValue();
                                $fieldValueArray = array();
                                if (is_array( $fieldValueData )) {
                                    foreach ($fieldValueData as $f) {
                                        $fieldValueArray[] = (isset( $f['target_id'] )) ? $f['target_id'] : '';
                                    }
                                }
                                $fields[$key5] = $fieldValueArray;
                                break;

                            // Fields to be skipped
                            case 'uuid':
                            case 'vid':
                            case 'langcode':
                            case 'uid':
                            case 'created':
                            case 'changed':
                            case 'promote':
                            case 'sticky':
                            case 'revision_timestamp':
                            case 'revision_uid':
                            case 'revision_log':
                            case 'revision_translation_affected':
                            case 'default_langcode':
                            case 'path':
                                break;
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