<?php

namespace App\Controller;

use App\Entity\Magazine;
use App\Form\MagazineType;
use App\Repository\MagazineRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/magazine")
 */
class MagazineController extends Controller
{
    //Отображает список журналов конкретного автора
    public function listHisMagazines($list): Response
    {
        return $this->render('magazine/index.html.twig', ['magazines' => $list]);
    }

    /**
     * @Route("/", name="magazine_index", methods="GET")
     */
    public function index(MagazineRepository $magazineRepository): Response
    {
        $page = $this->get('request_stack')->getCurrentRequest()->query->get('page') ? $this->get('request_stack')->getCurrentRequest()->query->get('page') : 1;


        //Адаптер для работы с БД через доктрин
        $queryBuilder = $magazineRepository->createQueryBuilder('c')->orderBy('c.date', 'ASC');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(3);
        $pagerfanta->setCurrentPage($page);
//        $magazines = $magazineRepository->findAllSort();
        $magazines = $pagerfanta->getCurrentPageResults();



        return $this->render('magazine/index.html.twig', [
            'magazines' => $magazines,
            'my_pager' => $pagerfanta,
            ]);
    }

    /**
     * @Route("/new", name="magazine_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $magazine = new Magazine();
        $form = $this->createForm(MagazineType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($magazine);
            $em->flush();

            return $this->redirectToRoute('magazine_index');
        }

        return $this->render('magazine/new.html.twig', [
            'magazine' => $magazine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="magazine_show", methods="GET")
     */
    public function show(Magazine $magazine): Response
    {
        $list = $magazine->getAuthors();
        return $this->render('magazine/show.html.twig', ['magazine' => $magazine, 'list' => $list]);
    }

    /**
     * @Route("/{id}/edit", name="magazine_edit", methods="GET|POST")
     */
    public function edit(Request $request, Magazine $magazine): Response
    {
        $form = $this->createForm(MagazineType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('magazine_edit', ['id' => $magazine->getId()]);
        }

        return $this->render('magazine/edit.html.twig', [
            'magazine' => $magazine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="magazine_delete", methods="DELETE")
     */
    public function delete(Request $request, Magazine $magazine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$magazine->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($magazine);
            $em->flush();
        }

        return $this->redirectToRoute('magazine_index');
    }
}
