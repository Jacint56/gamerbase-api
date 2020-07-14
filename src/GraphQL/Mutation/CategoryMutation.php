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

        $this->em->persist($category);
        $this->em->flush();

        return 
            ["name" => $args['category']['name']]
        ;
    }

    public static function getAliases(): array
    {
        return array(
            "create" => "createCategory",
        );
    }
}