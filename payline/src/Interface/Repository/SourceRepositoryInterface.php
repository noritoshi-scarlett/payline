<?php
declare(strict_types=1);

namespace Payline\App\Interface\Repository;

use Payline\App\Interface\Entity\Source\SourceInterface;

interface SourceRepositoryInterface
{
    /**
     * @return iterable<SourceInterface>
     */
    public function getAll(): iterable;

    /**
     * @param int $id
     * @return SourceInterface|null
     */
    public function findById(int $id): ?SourceInterface;

    /**
     * @param SourceInterface $source
     * @return bool
     */
    public function save(SourceInterface $source): bool;

    /**
     * @param SourceInterface $source
     * @return bool
     */
    public function delete(SourceInterface $source): bool;
}
