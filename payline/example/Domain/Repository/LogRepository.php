<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Example\Domain\Repository;

use Noritoshi\Payline\Application\Exception\EntityMappingException;
use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;
use Noritoshi\Payline\Interface\Repository\LogRepositoryInterface;
use Noritoshi\Payline\Example\Payment\Domain\Entity\PaymentLog;
use Noritoshi\Payline\Example\Payment\Plugin\PayU\Domain\Entity\PayUPaymentLogEnum;

/**
 * @template T of object
 * @template V of object
 * @template-implements LogEntityInterface<T, V>
 */
readonly class LogRepository implements LogRepositoryInterface
{
    public function __construct(
        private \PDO   $PDO,
        private string $tableName,
    )
    {
    }

    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return iterable<LogEntityInterface<T, V>
     */
    public function getAllForRelatedEntityCollection(RelatedEntityCollectionInterface $relatedEntityCollection): iterable
    {
        try {
            $query = sprintf('SELECT * FROM %s WHERE related_entity_collection_id = :relatedEntityCollectionId', $this->tableName);
            $statement = $this->PDO->prepare($query);

            $statement->bindValue(':relatedEntityCollectionId', $relatedEntityCollection->getId(), \PDO::PARAM_INT);

            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                yield $this->mapRowToEntity($row);
            }
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch logs for related entity collection: ' . $e->getMessage(), 0, $e);
        } catch (\DateMalformedStringException|\ValueError $e) {
            //todo logger
        } catch (EntityMappingException $e) {
            //todo logger
        }
    }

    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>|null
     */
    public function getNewestForRelatedEntityCollection(RelatedEntityCollectionInterface $relatedEntityCollection): ?LogEntityInterface
    {
        return null;
        //TODO IMPLEMENT
    }

    /**
     * @param SourceInterface $source
     * @return iterable<LogEntityInterface<T, V>>
     */
    public function getAllForSource(SourceInterface $source): iterable
    {
        return [];
        //TODO IMPLEMENT
    }

    /**
     * @return iterable<LogEntityInterface<T, V>>
     * @throws EntityMappingException
     */
    public function findBySourceAndState(SourceInterface $source, StateEnumInterface $state): iterable
    {
        try {
            $query = sprintf(
                'SELECT * FROM %s WHERE source_id = :sourceId AND state = :state',
                $this->tableName
            );
            $statement = $this->PDO->prepare($query);

            $statement->bindValue(':sourceId', $source->getId(), \PDO::PARAM_INT);
            $statement->bindValue(':state', $state->value, \PDO::PARAM_STR);

            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                yield $this->mapRowToEntity($row);
            }
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to retrieve logs by source and state: ' . $e->getMessage(), 0, $e);
        } catch (\DateMalformedStringException|\ValueError $e) {
            throw new \RuntimeException('Data mapping error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param mixed $row
     * @return LogEntityInterface<T, V>
     * @throws \DateMalformedStringException
     * @throws \ValueError
     * @throws EntityMappingException
     */
    private function mapRowToEntity(mixed $row): LogEntityInterface
    {
        if (is_array($row)) {
            return new PaymentLog(
                $row['id'],
                $row['source_id'],
                $row['related_entity_collection_id'],
                PayUPaymentLogEnum::from($row['state']),
                new \DateTimeImmutable($row['created_at']),
                !empty($row['message']) ? $row['message'] : null
            );
        }
        throw new EntityMappingException('Failed to map row to entity');
    }

    public function save(LogEntityInterface $log): bool
    {
        try {
            $query = sprintf(
                'INSERT INTO %s 
                    (source_id, related_entity_collection_id, state, data, created_at, message)
                    VALUES (:sourceId, :relatedEntityCollectionId, :state, :data, :createdAt, :message)',
                $this->tableName
            );

            $statement = $this->PDO->prepare($query);

            $statement->bindValue(':sourceId', $log->getSource()->getId(), \PDO::PARAM_INT);
            $statement->bindValue(':relatedEntityCollectionId', $log->getRelatedEntityCollection()->getId(), \PDO::PARAM_INT);
            $statement->bindValue(':state', $log->getState()->value, \PDO::PARAM_STR);
            $statement->bindValue(':data', $log->getRelatedEntityCollection()->getCalculatedDataHub()->serializeToJson(), \PDO::PARAM_STR);
            $statement->bindValue(':createdAt', $log->getCreatedAt()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
            $statement->bindValue(':message', $log->getMessage(), $log->getMessage() !== null ? \PDO::PARAM_STR : \PDO::PARAM_NULL);

            return $statement->execute();
        } catch (\PDOException $e) {
            //TODO -> add to logger
            return false;
        }
    }
}