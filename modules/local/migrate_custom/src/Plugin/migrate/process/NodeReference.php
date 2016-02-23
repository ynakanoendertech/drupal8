<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\process\NodeReference.
 */

namespace Drupal\migrate_custom\Plugin\migrate\process;

use Drupal\node\NodeStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Entity\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Looks up a node by title.
 *
 * @MigrateProcessPlugin(
 *   id = "node_reference"
 * )
 */
class NodeReference extends ProcessPluginBase implements ContainerFactoryPluginInterface {

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, NodeStorageInterface $nodeStorage) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->migration = $migration;
        $this->nodeStorage = $nodeStorage;
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
            $container->get('entity.manager')->getStorage('node')
        );
    }

    protected function getNodeId($name) {
        // Generate $this->organizationNode
        if (!$this->organizationNodes) {
            if (isset($this->configuration['content_type'])) {
                $nids = $this->nodeStorage
                    ->getQuery()
                    ->condition('type', $this->configuration['content_type'])
                    ->execute();
                $nodes = $this->nodeStorage->loadMultiple($nids);
                foreach ($nodes as $node) {
                    $this->organizationNodes[$node->label()] = $node->id();
                }
            }
        }
        return $this->organizationNodes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        return $this->getNodeId($value);
    }

}