<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", methods={"POST"}, name="api_create_category")
     */
    function create(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category();
        $category->setName($content["name"]);

        $entityManager->persist($category);
        $entityManager->flush();

        return new JsonResponse([
            "id"=>$category->getId(),
            "name"=>$category->getName(),
            "slug"=>$category->getSlug()
        ]);
    }
    /**
     * @Route("/categories/{id}", methods={"PUT"}, name="api_update_category")
     */
    function update(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);

        $category->setName($content["name"]);

        $entityManager->flush();

        return new JsonResponse([
            "id"=>$category->getId(),
            "name"=>$category->getName(),
            "slug"=>$category->getSlug()
        ]);
    }

    /**
     * @Route("/categories/{id}", methods={"DELETE"}, name="api_delete_category")
     */
    function delete(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $entityManager->getRepository(Category::class)->find($id);
        $entityManager->remove($category);

        $entityManager->flush();

        return new JsonResponse([
            "id"=>$category->getId(),
            "name"=>$category->getName(),
            "slug"=>$category->getSlug()
        ]);
    }
}
