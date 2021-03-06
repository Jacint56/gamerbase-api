<?php

namespace App\GraphQL\Mutation;

use App\Entity\Room;
use App\Entity\Game;
use App\GraphQL\Resolver\GameResolver;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Exception;

class RoomMutation implements MutationInterface, AliasedInterface
{
    private $em;

    public function __construct(EntityManager $em, GameResolver $gameResolver)
    {
        $this->em = $em;
    }

    public function create(Argument $args)
    {
        $room = new Room();
        $room->setGame($this->em->getRepository(Game::class)->find($args["room"]["game"]));
        $room->setIsPrivate($args["room"]["isPrivate"]);
        $room->setName($args["room"]["name"]);

        $room->setAvailable(true);

        $this->em->persist($room);
        $this->em->flush();

        $filePath = "rooms/" . $room->getId() . ".txt";
        $stdout = fopen($filePath, "w");
        fwrite($stdout, "\n");

        return $room;
    }
    /*
    mutation {
  createRoom(room: {game: 3, isPrivate: true, name: "Random Szoba név"}) {
    id
    slug
    game {
      id
      name
      category {
        id
        name
      }
    }
  }
}
*/
      

    public function update(Argument $args)
    {
        $room = $this->em->getRepository(Room::class)->find($args["id"]);
        if(!empty($room) && $room->getAvailable())
        {
            if(!empty($args["room"]["game"]))
            {
                $room->setGame($this->em->getRepository(Game::class)->find($args["room"]["game"]));
            }

            if(!empty($args["room"]["name"]))
            {
                $room->setName($args["room"]["name"]);
            }
            if(isset($args["room"]["isPrivate"]))
            {
                $room->setIsPrivate($args["room"]["isPrivate"]);
            }

            $this->em->flush();

            return $room;
        }
        throw new \GraphQL\Error\Error('This room does not exist!');

    }
    /*
    mutation {
  updateRoom(id: 1, room: {name: "Miháj Sumáher", game: 8}) {
    name
    id
    slug
    game {
      name
    }
  }
}
*/
      
    public function delete(Argument $args)
    {
        $room = $this->em->getRepository(Room::class)->find($args["id"]);
        if(!empty($room) && $room->getAvailable())
        {
            $room->setAvailable(false);
            $this->em->flush();
            return true;
        }
        throw new \GraphQL\Error\UserError('This room does not exist!');
    }
    /*
    mutation {
  deleteRoom(id: 9)
}
*/

    public function write(Argument $args)
    {
        $room = $this->em->getRepository(Room::class)->find($args["id"]);
        if (empty($room) || !($room->getAvailable())) {
            throw new \GraphQL\Error\UserError('This room doesn\'t exist!');
            exit();
        }

        $message = 
            $args["writer"] .
            "\n" .
            $args["date"] .
            "\n" .
            $args["message"] .
            "\n" . "\n";

        $filePath = "rooms/" . $room->getId() . ".txt";

        $chat = "";
        try{
            $myfile = fopen($filePath, "r");
            $chat = fread($myfile,filesize($filePath));
            if($chat == "\n"){
                $chat = "";
            }
        }
        catch(Exception $err){
            throw new \GraphQL\Error\UserError("Can't read room conversation!");
            exit();
        }
        finally{}

        $chat = $message . $chat;;
        $stdout = fopen($filePath, "w");
        fwrite($stdout, $chat);

        return true;
    }

    public static function getAliases(): array
    {
        return array(
            "create" => "createRoom",
            "update" => "updateRoom",
            "delete" => "deleteRoom",
            "write" => "writeRoom"
        );
    }
}