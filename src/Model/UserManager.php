<?php declare(strict_types=1);

namespace Space48\SSO\Model;

use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory as RoleCollectionFactory;
use Magento\Backend\Model\Auth\StorageInterface as AuthStorageInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Security\Model\AdminSessionsManager;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use Space48\SSO\Exception\ServiceException;
use Space48\SSO\Exception\UserException;

class UserManager
{
    private const PASSWORD_LENGTH = 32;
    private const SSO_FLAG_KEY = 'is_sso';

    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var UserResource
     */
    private $userResource;

    /**
     * @var RoleCollectionFactory
     */
    private $roleCollectionFactory;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var AuthStorageInterface
     */
    private $authStorage;

    /**
     * @var AdminSessionsManager
     */
    private $securityManager;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        UserFactory $userFactory,
        UserResource $userResource,
        RoleCollectionFactory $roleCollectionFactory,
        Random $random,
        AuthStorageInterface $authStorage,
        AdminSessionsManager $securityManager,
        ManagerInterface $eventManager
    ) {
        $this->userFactory = $userFactory;
        $this->userResource = $userResource;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->random = $random;
        $this->authStorage = $authStorage;
        $this->securityManager = $securityManager;
        $this->eventManager = $eventManager;
    }

    public function isSSOUser(User $user): bool
    {
        return (int)$user->getData(self::SSO_FLAG_KEY) === 1;
    }

    public function isCurrentUserSSO(): bool
    {
        $user = $this->authStorage->getUser();

        return $user instanceof User && $this->isSSOUser($user);
    }

    /**
     * @throws ServiceException
     * @throws UserException
     */
    public function upsertUser(string $username, string $email, string $firstname, string $lastname, string $role): User
    {
        $roleId = $this->getRoleId($role);
        if (!$roleId) {
            throw new UserException(__('Role "%role" does not exist. Please contact the store administrator.',
                ['role' => $role]));
        }

        $user = $this->userFactory->create();
        $user
            ->setUserName($username)
            ->setEmail($email);

        $id = $this->userResource->userExists($user)['user_id'] ?? null;
        if ($id) {
            $this->userResource->load($user, $id);
        }

        $user
            ->setUserName($username)
            ->setEmail($email)
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setData('is_sso', 1)
            ->setRoleId($roleId);

        if (!$user->getPassword()) {
            try {
                $user->setPassword($this->random->getRandomString(self::PASSWORD_LENGTH));
            } catch (LocalizedException $e) {
                throw new ServiceException(__('Failed generating user password hash.'), $e);
            }
        }

        try {
            $this->userResource->save($user);

            $id = $user->getId();
            $user = $this->userFactory->create();
            $this->userResource->load($user, $id);
        } catch (\Exception $e) {
            throw new ServiceException(__('Failed to create or update the user.'), $e);
        }

        return $user;
    }

    /**
     * @throws UserException
     */
    public function login(User $user): void
    {
        if ((int)$user->getIsActive() !== 1) {
            throw new UserException(__('User account is inactive. Please contact the store administrator.'));
        }

        $this->authStorage->setUser($user);
        $this->authStorage->processLogin();
        $this->userResource->recordLogin($user);
        $this->securityManager->processLogin();

        if (!$this->authStorage->getUser()) {
            throw new UserException(__('Sign in process failed - your account may be disabled temporarily. Please contact the store administrator.'));
        }

        $this->eventManager->dispatch(
            'backend_auth_user_login_success',
            ['user' => $user]
        );
    }

    private function getRoleId(string $roleName): ?int
    {
        $id = $this->roleCollectionFactory->create()
            ->setRolesFilter()
            ->addFilter('role_name', $roleName)
            ->setPageSize(1)
            ->getFirstItem()
            ->getId();

        return $id ? (int)$id : null;
    }
}
