<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Url;
use AppBundle\Form\Type\UrlFormType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontendController extends Controller
{
    /**
     * @Route("/", name="frontend_shorter")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(UrlFormType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager = $this->getEntityManager();
            $url = $form->getData();
            $entityManager->persist($url);
            $entityManager->flush();

            return $this->redirectToRoute('frontend_shorter_ready', ['shortTag' => $url->getShortTag() ]);
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{shortTag}/ready", name="frontend_shorter_ready")
     *
     * @param string $shortTag
     *
     * @return Response
     */
    public function doneAction($shortTag)
    {
        $url = $this->getUrl($shortTag);

        return $this->render('default/ready.html.twig', ['url' => $url]);
    }

    /**
     * @Route("/{shortTag}", name="frontend_shorter_link")
     *
     * @param string $shortTag
     *
     * @return RedirectResponse
     */
    public function shortLinkAction($shortTag)
    {
        $url = $this->getUrl($shortTag);
        $url->setUsageCount($url->getUsageCount() + 1);
        $this->getEntityManager()->flush();

        return $this->redirect($url->getOriginalUrl());
    }

    /**
     * @param $shortTag
     *
     * @return Url
     *
     * @throws  NotFoundHttpException
     */
    private function getUrl($shortTag)
    {
        $url = $this->getEntityManager()->getRepository(Url::class)->findOneBy(['shortTag' => $shortTag]);
        if (null === $url) {
            throw new NotFoundHttpException();
        }

        return $url;
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->get('doctrine')->getManager();
    }
}
