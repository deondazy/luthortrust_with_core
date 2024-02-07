<?php

declare(strict_types=1);

namespace Denosys\Core\Pagination;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Traversable;

use function count;

class Paginator
{
    final public const PAGE_SIZE = 15;

    private int $currentPage;
    private int $numResults;

    /**
     * @var Traversable<int, object>
     */
    private Traversable $results;

    private array $meta = [];

    public function __construct(
        private readonly DoctrineQueryBuilder $queryBuilder,
        private readonly int $pageSize = self::PAGE_SIZE
    ) {
    }

    public function paginate(int $page = 1): self
    {
        $this->currentPage = max(1, $page);
        $firstResult = ($this->currentPage - 1) * $this->pageSize;

        $query = $this->queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        /** @var array<string, mixed> $joinDqlParts */
        $joinDqlParts = $this->queryBuilder->getDQLPart('join');

        if (0 === count($joinDqlParts)) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new DoctrinePaginator($query, true);

        /** @var array<string, mixed> $havingDqlParts */
        $havingDqlParts = $this->queryBuilder->getDQLPart('having');

        $useOutputWalkers = count($havingDqlParts ?: []) > 0;
        $paginator->setUseOutputWalkers($useOutputWalkers);

        $this->results = $paginator->getIterator();
        $this->numResults = $paginator->count();
        $this->meta = $this->getMeta();

        return $this;
    }

    public function getMeta(): array
    {
        $meta = [];
        $lastPage = $this->getLastPage();
        $currentPage = $this->getCurrentPage();

        $meta['previous'] = $this->getPreviousPage();

        for ($i = 1; $i <= $lastPage; $i++) {
            if ($i == 1 || $i == $lastPage || ($i >= $currentPage - 4 && $i <= $currentPage + 4)) {
                $meta['links'][] = $i;
            } elseif (count($meta['links']) && end($meta['links']) != '...') {
                $meta['links'][] = '...';
            }
        }

        $meta['next'] = $this->getNextPage();

        return $meta;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): ?int
    {
        return ($this->currentPage - 1 > 0) ? $this->currentPage - 1 : null;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function getNextPage(): ?int
    {
        return ($this->currentPage + 1 <= $this->getLastPage()) ? $this->currentPage + 1 : null;
    }

    public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    /**
     * @return Traversable<int, object>
     */
    public function getResults(): Traversable
    {
        return $this->results;
    }
}
