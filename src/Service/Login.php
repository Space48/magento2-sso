<?php declare(strict_types=1);

namespace Space48\SSO\Service;

use Magento\Framework\App\Response\RedirectInterface;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\ValidationError;
use Space48\SSO\Exception\ServiceException;
use Space48\SSO\Exception\UserException;
use Space48\SSO\Model\AuthFactory;
use Space48\SSO\Model\Config;
use Space48\SSO\Model\Storage;
use Space48\SSO\Model\Url;
use Space48\SSO\Model\UserManager;

class Login
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var AuthFactory
     */
    private $authFactory;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(
        Config $config,
        AuthFactory $authFactory,
        Storage $storage,
        Url $url,
        UserManager $userManager
    ) {

        $this->config = $config;
        $this->authFactory = $authFactory;
        $this->storage = $storage;
        $this->url = $url;
        $this->userManager = $userManager;
    }

    public function isAvailable(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * @return string Identity provider login redirect URL.
     *
     * @throws ServiceException
     */
    public function initLogin(): string
    {
        $auth = $this->authFactory->getInstance();

        try {
            $redirectUrl = $auth->login(
                $this->url->getReturnUrl(),
                [],
                false,
                false,
                true
            );
        } catch (\Exception $e) {
            throw new ServiceException(__(
                'Failed to initialise a login request: %error',
                ['error' => $e->getMessage()]
            ), $e);
        }

        $this->storage->setLastRequestId($auth->getLastRequestID());

        return $redirectUrl;
    }

    /**
     * @return string Post login redirect URL.
     *
     * @throws ServiceException
     * @throws UserException
     */
    public function processLoginResponse(): string
    {
        $auth = $this->authFactory->getInstance();
        $requestId = $this->storage->getLastRequestId(true);

        try {
            $auth->processResponse($requestId);

            if ($auth->getLastErrorException()) {
                throw $auth->getLastErrorException();
            }
        } catch (ValidationError $e) {
            if ($e->getCode() === ValidationError::ASSERTION_EXPIRED) {
                throw new UserException(__('Provided single sign-on response is expired.'), $e);
            }
            throw new ServiceException(__(
                'Failed to validate SSO login response: %error',
                ['error' => $e->getMessage()]
            ), $e);
        } catch (\Exception $e) {
            throw new ServiceException(__(
                'Failed to process SSO login response: %error',
                ['error' => $e->getMessage()]
            ), $e);
        }

        if (!$auth->isAuthenticated()) {
            throw new UserException(__('Single sign-on authentication failed.'));
        }

        $user = $this->userManager->upsertUser(
            $auth->getNameId(),
            $this->getRequiredAttribute($auth, $this->config->getEmailAttributeName()),
            $this->getRequiredAttribute($auth, $this->config->getFirstNameAttributeName()),
            $this->getRequiredAttribute($auth, $this->config->getLastNameAttributeName()),
            $this->getMagentoRoleName($auth)
        );

        $this->userManager->login($user);

        return $auth->redirectTo('', [], true);
    }

    private function getMagentoRoleName(Auth $auth): string
    {
        if ($this->config->hasStaticMagentoRoleName()) {
            return $this->config->getStaticMagentoRoleName();
        }

        return $this->getRequiredAttribute($auth, $this->config->geRoleAttributeName());
    }

    /**
     * @throws UserException
     */
    private function getRequiredAttribute(Auth $auth, string $attributeName): string
    {
        $value = $auth->getAttribute($attributeName);

        if (!is_array($value)) {
            throw new UserException(__(
                'Required attribute "%attr" was not provided by the Identity Provider.',
                ['attr' => $attributeName]
            ));
        }

        return (string)current($value);
    }
}
