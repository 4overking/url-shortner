<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\UrlFormType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiController extends Controller
{
    /**
     * @Route("/api/shortener", name="api_shortener")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $data = @json_decode($request->getContent(), true);
        if (null === $data) {
            throw new BadRequestHttpException('JSON is not valid');
        }
        if (!isset($data['originalUrl'])) {
            throw new BadRequestHttpException('JSON does not contains originalUrl parameter');
        }
        $form = $this->createForm(UrlFormType::class, null, ['csrf_protection' => false]);
        $form->submit($data);
        $response = [];
        $code = 200;
        if ($form->isValid()) {
            $entityManager = $this->getEntityManager();
            $url = $form->getData();
            $entityManager->persist($url);
            $entityManager->flush();
            $response['result'] = $this->getRouter()->generate(
                'frontend_shorter_link',
                [
                    'shortTag' => $url->getShortTag()
                ],
                Router::ABSOLUTE_URL
            );
        } else {
            $response = [];
            $code = Response::HTTP_BAD_REQUEST;
            foreach ($form->getErrors(true) as $error) {
                $response['errors'][] = $error->getMessage();
            }
        }

        return new JsonResponse($response, $code);
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        return $this->get('router');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->get('doctrine')->getManager();
    }
}
