<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transaction;
use AppBundle\Exception\AppException;
use AppBundle\Exception\EntityNotFoundError;
use AppBundle\Exception\ValidationError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * @Route("/customers/{customerId}/transactions")
     * @Method({"PUT"})
     */
    public function createTransactionAction(Request $request, $customerId)
    {
        try {
            $customerRepo = $this->get('app.repository.customer');
            $txRepo = $this->get('app.repository.transaction');

            $transaction = new Transaction(null);
            $transaction->setCountry($request->get('country'));
            $transaction->setAmount($request->get('amount'));

            $validator = $this->get('validator');
            $errors = $validator->validate($transaction);

            if (count($errors) > 0) {
                throw new ValidationError($errors->get(0)->getMessage());
            }

            $txRepo->transactional(function () use ($txRepo, $customerRepo, $transaction, $customerId) {
                $customer = $customerRepo->getById($customerId);
                $transaction->setCustomer($customer);

                if (is_null($customer)) {
                    throw new EntityNotFoundError('Customer does not exist.');
                }

                // Step 1: Ascertain the bonus for this transaction
                $bonus = $txRepo->getNextBonusForCustomer($customer, $transaction);
                $transaction->setBonus($bonus);

                // Step 2: Update the balance of the customer
                $customerRepo->updateBalance($customer, $transaction);

                // Step 3: Store the transaction in the database
                $txRepo->persist($transaction);
            });

            $customer = $transaction->getCustomer();

            return new JsonResponse([
                'balance' => $customer->getBalance() + $customer->getBonusBalance(),
                'availableBalance' => intval($customer->getBalance()),
                'bonus' => $transaction->getBonus(),
            ], 201);
        } catch (AppException $e) {
            // Application errors with custom status codes
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            // Unexpected errors that we cannot deal with
            return new JsonResponse(['error' => 'An unexpected error has occured.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/transactions")
     * @Method("GET")
     */
    public function transactionReportAction(Request $request)
    {
        $txRepo = $this->get('app.repository.transaction');

        return new JsonResponse($txRepo->getReport($request->get('days', 7)), 200);
    }
}
