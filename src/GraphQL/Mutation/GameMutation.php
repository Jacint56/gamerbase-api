<?php

namespace App\GraphQL\Mutation;

use App\Entity\Category;
use App\Entity\Game;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class GameMutation implements MutationInterface, AliasedInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function create(Argument $args)
    {
        if (!empty($this->em->getRepository(Game::class)->findBy(Array(
            "name" => $args["game"]["name"]
        ))))
        {
            throw new \GraphQL\Error\Error('This game exists!');
            exit();
        }
        else {
            $game = new Game();
            $game->setName($args["game"]["name"]);
            $game->setCategory($this->em->getRepository(Category::class)->find($args["game"]["category"]));
            $game->setAvailable(true);
        
            $this->em->persist($game);
            $this->em->flush();

            return $game;
        }
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
        $data = $this->em->getRepository(Game::class)->findOneBy(Array(
            "name" => $args["game"]["name"]
        ));
        if(!empty($game) && $game->getAvailable())
        {
            if(!empty($args["game"]["name"]))
            {
                if (!empty($data) && $data->getId() != $game->getId())
                {
                    if(
                        ($this->em->getRepository(Game::class)->findOneBy(array("name" => $args["game"]["name"])))->getId()
                        ==
                        $game->getId()
                    )
                    {
                        $game->setName($args["game"]["name"]);
                    }
                    else
                    {
                        throw new \GraphQL\Error\Error('This game exists!');
                        exit();
                    }
                }
                else
                {
                    $game->setName($args["game"]["name"]);
                }
            }

            if(!empty($args["game"]["category"]))
            {
                $game->setCategory($this->em->getRepository(Category::class)->find($args["game"]["category"]));
            }

            $this->em->flush();

            return $game;
        }
        throw new \GraphQL\Error\Error('This game is unavailable!');

    }


    /*
mutation {
  updateGame(id: 9, game: {name: "The Crew 2"}) {
    id
    name
    slug
    category {
      name
    }
  }
}


    */

    public function delete(Argument $args)
    {
        $game = $this->em->getRepository(Game::class)->find($args["id"]);
        if(!empty($game) && $game->getAvailable())
        {
            $game->setAvailable(false);
            $this->em->flush();
            return true;
        }
        throw new \GraphQL\Error\Error('This game does not exist!');
    }
    /*
    mutation {
  deleteGame(id: 16)
}
    */

    public static function getAliases(): array
    {
        return array(
            "create" => "createGame",
            "update" => "updateGame",
            "delete" => "deleteGame"
            );
    }
}