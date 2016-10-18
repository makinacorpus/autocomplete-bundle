<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

use Drupal\Core\Entity\EntityManager;
use Drupal\node\NodeInterface;

use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * This is a sample using Drupal node entities as an input source.
 */
class NodePageAutocompleteSource implements AutocompleteSourceInterface
{
    private $entityManager;
    private $database;

    /**
     * Default constructor
     *
     * @param EntityManager $entityManager
     * @param \DatabaseConnection $database
     */
    public function __construct(EntityManager $entityManager, \DatabaseConnection $database)
    {
        $this->entityManager = $entityManager;
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function find($string, $limit = 16)
    {
        $idList = $this
            ->database
            ->select('node', 'n')
            ->fields('n', ['nid'])
            ->condition('n.title', '%' . $this->database->escapeLike($string) . '%', 'LIKE')
            ->condition('n.type', 'page')
            ->addTag('node_access')
            ->range(0, $limit)
            ->execute()
            ->fetchCol()
        ;

        return $this->entityManager->getStorage('node')->loadMultiple($idList);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!$value instanceof NodeInterface || 'page' !== $value->bundle() || !$value->access('view')) {
            throw new TransformationFailedException();
        }

        if ($value->isPublished()) {
            return $value->getTitle() . ' [' . $value->id() . ']';
        } else {
            return $value->getTitle() . ' (NON PUBLIE) [' . $value->id() . ']';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $matches = [];

        if (preg_match('/\[(\d+)\]/', $value, $matches)) {

              $node = $this->entityManager->getStorage('node')->load($matches[1]);

              if (!$node || !$node->access('view') || 'page' !== $node->bundle()) {
                  throw new TransformationFailedException();
              }

              return $node;
        }

        throw new TransformationFailedException();
    }
}
