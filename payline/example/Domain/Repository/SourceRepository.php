<?php
declare(strict_types=1);

namespace Payline\Example\Domain\Repository;

use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;
use Payline\App\Interface\Repository\SourceRepositoryInterface;

class SourceRepository implements SourceRepositoryInterface
{
    public function __construct(
        private readonly \PDO $PDO,
        private readonly string $tableName
    ) {}

    public function getAll(): iterable
    {
        $query = sprintf('SELECT * FROM %s', $this->tableName);
        $statement = $this->PDO->query($query);

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            yield $this->mapRowToEntity($row);
        }
    }

    public function findById(int $id): ?SourceInterface
    {
        $query = sprintf('SELECT * FROM %s WHERE id = :id', $this->tableName);
        $statement = $this->PDO->prepare($query);

        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function save(SourceInterface $source): bool
    {
        if ($source->getId() === null) {
            // Insert
            $query = sprintf('INSERT INTO %s (name, state) VALUES (:name, :state)', $this->tableName);
            $statement = $this->PDO->prepare($query);
        } else {
            // Update
            $query = sprintf('UPDATE %s SET name = :name, state = :state WHERE id = :id', $this->tableName);
            $statement = $this->PDO->prepare($query);
            $statement->bindValue(':id', $source->getId(), \PDO::PARAM_INT);
        }

        $statement->bindValue(':name', $source->getName(), \PDO::PARAM_STR);
        $statement->bindValue(':state', $source->getState(), \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function delete(SourceInterface $source): bool
    {
        $query = sprintf('DELETE FROM %s WHERE id = :id', $this->tableName);
        $statement = $this->PDO->prepare($query);

        $statement->bindValue(':id', $source->getId(), \PDO::PARAM_INT);
        return $statement->execute();
    }

    public function findAllByState(StateEnumInterface $state): iterable
    {
        $query = sprintf('SELECT * FROM %s WHERE state = :state', $this->tableName);
        $statement = $this->PDO->prepare($query);

        $statement->bindValue(':state', $state->getValue(), \PDO::PARAM_STR);
        $statement->execute();

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            yield $this->mapRowToEntity($row);
        }
    }

    private function mapRowToEntity(array $row): SourceInterface
    {
        return new Source($row['id'], $row['name']);
    }
}
