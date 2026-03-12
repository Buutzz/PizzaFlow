<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Enum\OrderStatus;
use App\Form\OrderType;
use App\Service\OrderQueueService;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{

    #[Route('/', name: 'order_index')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        OrderQueueService $queueService
    ): Response {

        $order = new Order();

        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('front/order/index.html.twig', [
            'form' => $form->createView(),
            'queueCount' => $queueService->getQueueLength(),
            'deliveryTime' => $queueService->getEstimatedDeliveryTime()
        ]);
    }

    #[Route('/orders/stats', name: 'orders_stats', methods: ['POST'])]
    public function stats(OrderRepository $orderRepository): JsonResponse
    {
        $queue = $orderRepository->count(['status' => OrderStatus::Pending]);

        $estimatedTime = $queue * 10;

        return $this->json([
            'queue' => $queue,
            'estimatedTime' => $estimatedTime
        ]);
    }
}