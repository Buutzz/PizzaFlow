<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\OrderStatus;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findNextOrder(): ?Order
    {
        return $this->findOneBy(
            ['status' => OrderStatus::Pending],
            ['createdAt' => 'ASC']
        );
    }

    public function countDeliveredToday(): int
    {
        $today = new \DateTimeImmutable('today midnight');

        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.status = :delivered')
            ->andWhere('o.createdAt >= :today')
            ->setParameter('delivered', OrderStatus::Delivered)
            ->setParameter('today', $today)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
