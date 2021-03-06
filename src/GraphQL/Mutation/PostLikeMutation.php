<?php

namespace App\GraphQL\Mutation;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\PostLike;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;


class PostLikeMutation implements MutationInterface, AliasedInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function create(Argument $args)
    {
        
        $like = new PostLike();

        $like->setLiker($this->em->getRepository(User::class)->find($args["postLike"]["liker"]));
        $like->setPost($this->em->getRepository(Post::class)->find($args["postLike"]["post"]));

        $like->setAvailable(true);

        $this->em->persist($like);
        $this->em->flush();

        return $like;
    }

    public function delete(Argument $args)
    {
        $like = $this->em->getRepository(PostLike::class)->find($args["id"]);
        if(!empty($like))
        {
            $like->setAvailable(!($like->getAvailable()));
            $this->em->flush();
            if($like->getAvailable())
            {
                return $like;
            }
            else
            {
                return true;
            }
        }
        throw new \GraphQL\Error\UserError('You cannot do that!');
    }

    public function postLike(Argument $args)
    {
        $like = $this->em->getRepository(PostLike::class)->findOneBy(
            array(
                "liker" => $args["postLike"]["liker"],
                "post" => $args["postLike"]["post"]
            )
        );

        if(empty($like))
        {
            return $this->create($args);
        }
        else
        {
            $args["id"] = $like->getId();
            return $this->delete($args);
        }
    }
    /*
    mutation {
  PostLike(postLike: {liker: 1, post: 1}) {
    id
    liker {
      userName
    }
    post {
      title
      content
      poster {
        userName
      }
    }
  }
}
*/

    public static function getAliases(): array
    {
        return array(
            "postLike" => "PostLike"
        );
    }
}