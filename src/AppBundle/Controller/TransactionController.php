<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TransactionController extends Controller
{
    /**
     * @Route("/customers/{customerId}/transactions")
     * @Method({"PUT"})
     */
    public function createTransactionAction(Request $request, $customerId)
    {
        return new JsonResponse([], 201);
    }

    /**
     * @Route("/transactions")
     * @Method("GET")
     */
    public function transactionReportAction(Request $request)
    {
        return new JsonResponse([], 200);
    }
}
