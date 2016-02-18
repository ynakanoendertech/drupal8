<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\process\TermReference.
 */

namespace Drupal\migrate_custom\Plugin\migrate\process;

use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Entity\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Looks up a taxonomy term by title.
 *
 * @MigrateProcessPlugin(
 *   id = "term_reference"
 * )
 */
class TermReference extends ProcessPluginBase implements ContainerFactoryPluginInterface {

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, TermStorageInterface $termStorage) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->migration = $migration;
        $this->termStorage = $termStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $migration,
            $container->get('entity.manager')->getStorage('taxonomy_term')
        );
    }
    protected function getTermId($name) {
        // Check vocabulary_name is specified
        if (isset($this->configuration['vocabulary_name'])) {
            $vocabulary_name = $this->configuration['vocabulary_name'];
            if (!$this->terms) {
                //$tids = $this->termStorage
                //    ->getQuery()
                //    ->condition('vid', $vocabulary_name)
                //    ->execute();
                //$terms = $this->termStorage->loadMultiple($tids);
                $terms = $this->termStorage->loadTree($vocabulary_name, 0, NULL, TRUE);
                foreach ($terms as $term) {
                    //$this->terms[$term->name->value] = $term->id();
                    $this->terms[$term->label()] = $term->id();
                }
            }
        }
        return $this->terms[$name];
    }
    /**
     * {@inheritdoc}
     */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        return $this->getTermId($value);
    }

}