<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Enum\OrderStatus;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderQueueService
{

    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em
    ) {}

    public function getQueueLength(): int
    {
        return $this->orderRepository->count([
            'status' => OrderStatus::Pending
        ]);
    }

    public function getEstimatedDeliveryTime(): int
    {
        return $this->getQueueLength() * 10;
    }

    public function processNextOrder(): ?Order
    {

        $order = $this->orderRepository->findNextOrder();

        if (!$order) {
            return null;
        }

        $order->setStatus(OrderStatus::Delivered);

        $this->em->flush();

        return $order;
    }
}