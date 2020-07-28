<?php

namespace App\GraphQL\Mutation;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class CategoryMutation implements MutationInterface, AliasedInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function create(Argument $args)
    {
        $category = new Category();
        $category->setName($args["category"]["name"]);
        $category->setAvailable(true);

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }
    /*
    mutation {
        createCategory(category:{name: "Shanyi"}) {
          id
          name
        }
      }
      */
      

    public function update(Argument $args)
    {
        $category = $this->em->getRepository(Category::class)->find($args["id"]);
        if(!empty($category) && $category->getAvailable())
        {
            $category->setName($args["category"]["name"]);
            $this->em->flush();
            return $category;
        }
        return null;

    }
    /*
    mutation {
        updateCategory(category:{name: "Shanyi"}, id: 9) {
          id
          name
        }
      }
      */
      
    public function delete(Argument $args)
    {
        $category = $this->em->getRepository(Category::class)->find($args["id"]);
        if(!empty($category) && $category->getAvailable())
        {
            $category->setAvailable(false);
            $this->em->flush();
            return true;
        }
        return false;
    }
    /*
    mutation {
  deleteCategory(id: 9)
}
    */

    public static function getAliases(): array
    {
        return array(
            "create" => "createCategory",
            "update" => "updateCategory",
            "delete" => "deleteCategory"
            );
    }
}