<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;

/**
 * Base implementation for lazzy people
 *
 * Trait AutocompleteSourceTrait
 * @package MakinaCorpus\AutocompleteBundle\Autocomplete
 */
trait AutocompleteSourceTrait /* implements AutocompleteSourceInterface */
{
    /**
     * @param $string
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function autocomplete($string, $limit = 30, $offset = 0)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('entity');
        $query = $qb
            ->where('lower(entity.name) LIKE :NamePart')
            ->setParameter('NamePart', '%' . mb_strtolower($string) . '%')
            ->getQuery();
        if ($limit > 0) {
            $query
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }
        return $query->getResult();
    }

    /**
     * @param array $valueList
     * @return Collection
     */
    public function findAllById($valueList, $append)
    {
        $result = new ArrayCollection();
        foreach ($valueList as $value) {
            $entity = $this->findById($value);
            if (!$entity && $append) {
                $entity = $this->newEntity($value);
            }
            $result->add($entity);
        }
        return $result;
    }

    /**
     * @param int|string $value
     * @return Collection
     */
    public function findById($value)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('entity');
        $result = $qb
            ->where('entity.id = :Identifier')
            ->setParameter('Identifier', $value)
            ->getQuery()
            ->getOneOrNullResult();
        return $result;
    }

    /**
     * @param object $value Entity
     * @return mixed ID of entity
     */
    public function getId($value)
    {
        return $value->getId();
    }

    /**
     * @param object $value Entity
     * @return mixed Label of entity
     */
    public function getLabel($value)
    {
        return $value->getName();
    }

    /**
     * @param object $value Entity
     * @return array
     */
    public function getExtraData($value)
    {
        return [];
    }

    /**
     * @param object $value Entity
     * @param string $string Searched string
     * @return string
     */
    public function getMarkup($value, $string = '')
    {
        $str = $value->getName();
        return $string === '' ? $str : preg_replace('/(' . $string . ')/ui', '<b>$1</b>', $str);
    }

    /**
     * @param string $value Label to new entity
     * @return object
     */
    public function newEntity($value)
    {
        /** @var object $entity */
        $entity = new $this->getEntityName();
        $entity->setName($value);
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return $entity;
    }
}
