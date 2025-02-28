<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Example\Payment\Domain\Repository;

use Noritoshi\Payline\Example\Payment\Domain\Entity\PaymentSource;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;
use Noritoshi\Payline\Interface\Repository\SourceRepositoryInterface;
use PDO;

readonly class PaymentSourceRepository implements SourceRepositoryInterface
{
    public function __construct(
        private PDO   $PDO,
        private string $tableName
    ) {}

    public function getAll(): iterable
    {
        $query = sprintf('SELECT * FROM %s', $this->tableName);
        $statement = $this->PDO->query($query);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield $this->mapRowToEntity($row);
        }
    }

    public function findById(int $id): ?SourceInterface
    {
        $query = sprintf('SELECT * FROM %s WHERE id = :id', $this->tableName);
        $statement = $this->PDO->prepare($query);

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function save(SourceInterface $source): bool
    {
        if ($source->getId() === null) {
            // Insert
            $query = sprintf('INSERT INTO %s (name) VALUES (:name)', $this->tableName);
            $statement = $this->PDO->prepare($query);
        } else {
            // Update
            $query = sprintf('UPDATE %s SET name = :name WHERE id = :id', $this->tableName);
            $statement = $this->PDO->prepare($query);
            $statement->bindValue(':id', $source->getId(), PDO::PARAM_INT);
        }

        $statement->bindValue(':name', $source->getName(), PDO::PARAM_STR);

        return $statement->execute();
    }

    public function delete(SourceInterface $source): bool
    {
        $query = sprintf('DELETE FROM %s WHERE id = :id', $this->tableName);
        $statement = $this->PDO->prepare($query);

        $statement->bindValue(':id', $source->getId(), PDO::PARAM_INT);
        return $statement->execute();
    }

    private function mapRowToEntity(array $row): PaymentSource
    {
        return new PaymentSource($row['id'], $row['name']);
    }
}
