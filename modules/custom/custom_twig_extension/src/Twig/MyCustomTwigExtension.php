<?php

namespace Drupal\custom_twig_extension\Twig;

use \Drupal\Core\Url;

class MyCustomTwigExtension extends \Twig_Extension {

    protected $termStorage;
    protected $nodeStorage;

    /**
     * {@inheritdoc}
     */
    public function __construct() {
        $this->termStorage = \Drupal::entityManager()->getStorage('taxonomy_term');
        $this->nodeStorage = \Drupal::entityManager()->getStorage('node');
    }

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

        if (is_array($content['#view']->result)) {
            foreach($content['#view']->result as $key3 => $value3) {
                $fields = array();
                foreach($value3 as $key4 => $value4) {
                    if ($key4 == '_entity') {
                        foreach($value4 as $key5 => $value5) {

                            switch ($key5) {

                                // Location node id and target link
                                case 'nid':
                                    // Add nid to JSON data
                                    $fields[$key5] = $value5->getString();
                                    // Add target link
                                    $linkUrl = Url::fromRoute('entity.node.canonical', array('node' => $value5->getString()));
                                    $fields['url'] = $linkUrl->toString();
                                    break;

                                // Simple fields
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
                                    if (isset( $value5->getValue()[0]['value'] )) {
                                        $fields[$key5] = $this->removeNewlineCharacters( $value5->getValue()[0]['value'] );
                                    }
                                    break;

                                // Multi value fields
                                case 'field_organization':
                                case 'field_category':
                                case 'field_tags':
                                    $ids = array();
                                    if (is_array($value5->getValue())) {
                                        foreach ($value5->getValue() as $ref_entity) {
                                            $ids[] = (isset( $ref_entity['target_id'] )) ? $ref_entity['target_id'] : '';
                                        }
                                    }
                                    if ($key5 == 'field_organization') {
                                        // Find organization from node storage
                                        $orgs = $this->nodeStorage->loadMultiple($ids);
                                        $orgLinks = array();
                                        foreach ($orgs as $org) {
                                            $linkUrl = Url::fromRoute('entity.node.canonical', array('node' => $org->id()));
                                            $orgLinks[] = array(
                                                'id' => $org->id(),
                                                'title' => $org->label(),
                                                'url' => $linkUrl->toString(),
                                            );
                                        }
                                        $fields[$key5] = $orgLinks;
                                    } else {
                                        // Find category/search-keywords from term storage
                                        $terms = $this->termStorage->loadMultiple($ids);
                                        $termLinks = array();
                                        foreach ($terms as $term) {
                                            $linkUrl = Url::fromRoute('entity.taxonomy_term.canonical', array('taxonomy_term' => $term->id()));
                                            $termLinks[] = array(
                                                'id' => $term->id(),
                                                'title' => $term->label(),
                                                'url' => $linkUrl->toString(),
                                            );
                                        }
                                        $fields[$key5] = $termLinks;
                                    }
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
        }

        $output = json_encode($nodes);
        return $output;
    }
}