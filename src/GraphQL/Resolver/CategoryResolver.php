<?php

namespace App\GraphQL\Resolver;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Constraints\Length;

class CategoryResolver implements ResolverInterface, AliasedInterface
{
    private $em;
    private $paginator;

    public function __construct(EntityManager $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
    }

    public function resolve(Argument $args)
    {
        return $this->em->getRepository(Category::class)->find($args["id"]);
    }
    /*
    {
  category(id: 2) {
    id
    name
    slug
  }
}

    */

    public function list(Argument $args)
    {
        $categories = array();
        $where = array();
        $column = "id";
        $order = "ASC";

        if(!empty($args["name"]))
        {
            $where["name"] = $args["name"];
        }

        if(!empty($args["column"]))
        {
            if(substr($args["column"], 0, 1) == '-')
            {
                $column = substr($args["column"], 1);
                $order = "DESC";
            }
            else
            {
                $column = $args["column"];
            }
        }
        
        $categories = $this->paginator->paginate(
            $this->em->getRepository(Category::class)->findBy(
                $where,
                array($column => $order)
            ),
            $args["page"],
            $args["limit"]
        );
        
        return [
            "categories" => $categories,
            "total" =>$categories->getTotalItemCount()
        ];
    }
    /*
    {
  allCategories(limit: 10, page: 1) {
    categories {
      id
      name
      slug
    }
    total
  }
}

    */

    public static function getAliases(): array
    {
        return array(
            "resolve" => "Category",
            "list" => "allCategories",
        );
    }

}