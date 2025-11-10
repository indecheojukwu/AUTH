<?php 

namespace App\Security;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @extends EntityUserProvider<UserInterface>
 */
class OAuthProvider implements OAuthAwareUserProviderInterface, AccountConnectorInterface
{

    private $manager;
    private $properties;
    private string $class;
    private ObjectManager $em;
    private ?ObjectRepository $repository = null;

    public function __construct(ManagerRegistry $registry, string $class, $properties) {
        $this->properties = $properties;
        $this->class = $class;
        $this->manager = $registry->getManager();
        $this->em = $registry->getManager();
    }

    private function findUser(array $criteria): ?UserInterface {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository($this->class);
        }

        return $this->repository->findOneBy($criteria);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {

        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        $resourceOwnerName = $response->getResourceOwner()->getName();

        if(!isset($this->properties[$resourceOwnerName])){
            throw new \RuntimeException("No property defined for entity for resource " . $resourceOwnerName);
        }

        // unique interger
        $username = $response->getUserIdentifier();

        //check whether the user exists with email
        $user_email = $response->getEmail();
        $existing_user = $this->manager->getRepository(User::class)->findOneBy(['email' => $user_email]);

        if($existing_user){

            $user = $existing_user;
        }else{

            $user = $this->findUser(array($this->getProperty($response) => $username));

            if (null === $user) {

                $person = new Person();
                $person->setName($response->getFirstName() . " " . $response->getLastName());
                $this->manager->persist($person);
                $this->manager->flush();

                // create new user here
                $user = new User();
                $user->$setter_id($username);
                $user->$setter_token($response->getAccessToken());
                $user->setPerson($person);
                $user->setEmail($response->getEmail());
                $user->setGoogleProfilePicUrl($response->getProfilePicture());
                $user->setPassword(md5(uniqid('', true)));
                $this->manager->persist($user);
                $this->manager->flush();

                return $user;
            }
        }

        $user->$setter_token($response->getAccessToken());
        $user->setGoogleProfilePicUrl($response->getProfilePicture());
        $user->$setter_id($username);
        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response) {

        if(!$user instanceof User){
            throw new UnsupportedUserException("Expected an instance of App\Model\User, but got '%s' .", get_class($user));
        }

        $property = $this->getProperty($response);
        $username = $response->getUserIdentifier();

        if (null !== $previousUser = $this->registry->getRepository(User::class)->findOneBy(array($property => $username))) {
            $this->disconnect($previousUser, $response);
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set'. ucfirst($serviceName) . 'AccessToken';

        $user->$setter($response->getAccessToken());

        $this->updateUser($user, $response);
    }

    protected function getProperty(UserResponseInterface $response) {

        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }

    public function disconnect(UserInterface $user, UserResponseInterface $response) {

        $property = $this->getProperty($response);
        $accessor = PropertyAccess::createPropertyAccessor();

        $accessor->setValue($user, $property, null);

        $this->updateUser($user, $response);
    }

    private function updateUser(UserInterface $user, UserResponseInterface $response) {
        $user->setEmail($response->getEmail());

        $this->manager->persist($user);
        $this->manager->flush();
    }

}
