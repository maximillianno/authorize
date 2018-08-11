<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/author")
 */
class AuthorController extends Controller
{

    //Отображает список авторов конкретного журнала
    public function listHisAuthors($list): Response
    {
        return $this->render('author/index.html.twig', ['authors' => $list]);
    }

    /**
     * @Route("/", name="author_index", methods="GET")
     */
    public function index(AuthorRepository $authorRepository): Response
    {
        $page = $this->get('request_stack')->getCurrentRequest()->query->get('page') ? $this->get('request_stack')->getCurrentRequest()->query->get('page') : 1;

        $queryBuilder = $authorRepository->createQueryBuilder('c')->orderBy('c.firstName', 'ASC');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(3);
        $pagerfanta->setCurrentPage($page);



        return $this->render('author/index.html.twig', ['authors' => $pagerfanta->getCurrentPageResults(), 'my_pager' => $pagerfanta]);
    }



    /**
     * @Route("/new", name="author_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_show", methods="GET")
     */
    public function show(Author $author): Response
    {

        $list = $author->getMagazines();
        return $this->render('author/show.html.twig', ['author' => $author, 'list' => $list]);
    }

    /**
     * @Route("/{id}/edit", name="author_edit", methods="GET|POST")
     */
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('author_edit', ['id' => $author->getId()]);
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_delete", methods="DELETE")
     */
    public function delete(Request $request, Author $author): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($author);
            $em->flush();
        }

        return $this->redirectToRoute('author_index');
    }
}
