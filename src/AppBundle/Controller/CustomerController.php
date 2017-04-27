<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller
{
    /**
     * @Route("/customers")
     * @Route("/customers/{customerId}")
     *
     * @Method({"PUT", "PATCH"})
     */
    public function createUpdateCustomerAction(Request $request, $customerId = null)
    {
        $repo = $this->get('app.repository.customer');

        $customer = is_null($customerId)
            ? new Customer()
            : $repo->getById($customerId);

        if (is_null($customer)) {
            return new JsonResponse([
                'error' => 'Customer does not exist.',
            ], 404);
        }

        $customer->setFirstName($request->get('firstName', $customer->getFirstName()));
        $customer->setLastName($request->get('lastName', $customer->getLastName()));
        $customer->setEmail($request->get('email', $customer->getEmail()));
        $customer->setCountry($request->get('country', $customer->getCountry()));
        $customer->setGender($request->get('gender', $customer->getGender()));

        $validator = $this->get('validator');
        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
            return new JsonResponse([
                'error' => $errors->get(0)->getMessage(),
            ], 400);
        }

        try {
            $repo->persist($customer);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'E-mail address is already in use.',
            ], 400);
        }

        return new JsonResponse([
            'id' => $customer->getId(),
        ], is_null($customerId) ? 201 : 200);
    }
}
