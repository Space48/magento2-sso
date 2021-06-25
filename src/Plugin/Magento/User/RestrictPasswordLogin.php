<?php declare(strict_types=1);

namespace Space48\SSO\Plugin\Magento\User;

use Magento\User\Model\User;
use Space48\SSO\Model\Config;
use Space48\SSO\Model\UserManager;

class RestrictPasswordLogin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(
        Config $config,
        UserManager $userManager
    ) {
        $this->config = $config;
        $this->userManager = $userManager;
    }

    public function aroundVerifyIdentity(User $subject, callable $proceed, ...$args)
    {
        if (
            $this->config->isEnabled()
            && $this->userManager->isSSOUser($subject)
        ) {
            return false;
        }

        return $proceed(...$args);
    }
}
