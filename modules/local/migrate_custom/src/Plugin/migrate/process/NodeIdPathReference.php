<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\process\NodeIdPathReference.
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
 * Looks up a node ID path by title.
 * Note: This plugin picks up the first match of the title. If there are duplicate titles, the first one will be picked up.
 *
 * @MigrateProcessPlugin(
 *   id = "node_id_path_reference"
 * )
 */
class NodeIdPathReference extends ProcessPluginBase implements ContainerFactoryPluginInterface {

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

    protected function getNodeLink($name) {
        // Generate $this->organizationNode
        if (!$this->matchedNodes) {
            if (isset($this->configuration['content_type'])) {
                $nids = $this->nodeStorage
                    ->getQuery()
                    ->condition('type', $this->configuration['content_type'])
                    ->execute();
                $nodes = $this->nodeStorage->loadMultiple($nids);
                foreach ($nodes as $node) {
//                    error_log('getText: ' . $node->toLink()->getText());
//                    error_log('toUrl: '. $node->toLink()->getUrl()->toString());
                    $this->matchedNodes[$node->label()] = $node->toLink()->getUrl()->toString();
                }
            }
        }
        return $this->matchedNodes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        error_log('-----');
        error_log($value);
        return $this->getNodeLink($value);
    }

}