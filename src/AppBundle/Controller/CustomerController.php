<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Exception\AppException;
use AppBundle\Exception\EntityNotFoundError;
use AppBundle\Exception\ValidationError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        try {
            $repo = $this->get('app.repository.customer');

            $customer = is_null($customerId)
                ? new Customer()
                : $repo->getById($customerId);

            if (is_null($customer)) {
                throw new EntityNotFoundError('Customer does not exist.');
            }

            $customer->setFirstName($request->get('firstName', $customer->getFirstName()));
            $customer->setLastName($request->get('lastName', $customer->getLastName()));
            $customer->setEmail($request->get('email', $customer->getEmail()));
            $customer->setCountry($request->get('country', $customer->getCountry()));
            $customer->setGender($request->get('gender', $customer->getGender()));

            $validator = $this->get('validator');
            $errors = $validator->validate($customer);

            if (count($errors) > 0) {
                throw new ValidationError($errors->get(0)->getMessage());
            }

            $repo->persist($customer);

            return new JsonResponse(['id' => $customer->getId()], is_null($customerId)
                ? Response::HTTP_CREATED
                : Response::HTTP_OK);
        } catch (AppException $e) {
            // Application errors with custom status codes
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            // Unexpected errors that we cannot deal with
            return new JsonResponse(['error' => 'An unexpected error has occured.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
