<?php

namespace App\GraphQL\Mutation;

use App\Entity\Category;
use App\Entity\Game;
use App\GraphQL\Resolver\CategoryResolver;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class GameMutation implements MutationInterface, AliasedInterface
{
    private $em;
    private $categoryResolver;

    public function __construct(EntityManager $em, CategoryResolver $categoryResolver)
    {
        $this->em = $em;
        $this->categoryResolver = $categoryResolver;
    }

    public function create(Argument $args)
    {
        $game = new Game();
        $game->setName($args["game"]["name"]);
        $game->setCategory($this->em->getRepository(Category::class)->find($args["game"]["category"]));

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }
    /*
    mutation {
  createGame(game: {name: "FIFA21", category: 9}) {
    id
    name
    slug
    category {
      id
      name
      slug
    }
  }
}
    */
      

    public function update(Argument $args)
    {
        $game = $this->em->getRepository(Game::class)->find($args["id"]);

        if(!empty($args["game"]["name"]))
        {
            $game->setName($args["game"]["name"]);
        }

        if(!empty($args["game"]["category"]))
        {
            $game->setCategory($this->em->getRepository(Category::class)->find($args["game"]["category"]));
        }

        $this->em->flush();

        return $game;

    }
    /*
    mutation {
  updateGame(game: {name: "MMA2k21"}, id: 16) {
    id
    name
    slug
    category {
      id
      name
      slug
    }
  }
}

    */
      
    public function delete(Argument $args)
    {
        $game = $this->em->getRepository(Game::class)->find($args["id"]);

        $this->em->remove($game);

        $this->em->flush();

        return 1;

    }
    //Még nem megy.

    public static function getAliases(): array
    {
        return array(
            "create" => "createGame",
            "update" => "updateGame",
            "delete" => "deleteGame"
            );
    }
}