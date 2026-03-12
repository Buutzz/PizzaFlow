<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use App\Service\OrderQueueService;
use App\Enum\OrderStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class OrderAdminController extends AbstractController
{
    private OrderRepository $orderRepository;
    private OrderQueueService $queueService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderQueueService $queueService
    ) {
        $this->orderRepository = $orderRepository;
        $this->queueService = $queueService;
    }

    #[Route('/orders', name: 'admin_orders')]
    public function index(): Response
    {

        $orders = $this->orderRepository->findBy([], ['createdAt' => 'DESC']);
        $deliveredToday = $this->orderRepository->countDeliveredToday();
        return $this->render('admin/orders.html.twig', [
            'orders' => $orders,
            'queue' => $this->queueService->getQueueLength(),
            'delivered' => $deliveredToday,
        ]);
    }

    #[Route('/orders/data', name: 'admin_orders_data', methods: ['POST'])]
    public function data(): JsonResponse
    {
        $orders = $this->orderRepository->findBy([], ['createdAt' => 'DESC']);

        $result = [];

        foreach ($orders as $order) {
            $result[] = [
                'id' => $order->getId(),
                'pizza' => $order->getPizzaType(),
                'quantity' => $order->getQuantity(),
                'email' => $order->getEmail(),
                'address' => $order->getAddress(),
                'status' => $order->getStatus()->value,
                'created' => $order->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json($result);
    }

    #[Route('/orders/stats', name: 'admin_orders_stats', methods: ['POST'])]
    public function stats(): JsonResponse
    {
        $queue = $this->queueService->getQueueLength();
        $deliveredToday = $this->orderRepository->countDeliveredToday();

        return $this->json([
            'queue' => $queue,
            'deliveredToday' => $deliveredToday
        ]);
    }
}