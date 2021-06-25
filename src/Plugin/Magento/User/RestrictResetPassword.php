<?php declare(strict_types=1);

namespace Space48\SSO\Plugin\Magento\User;

use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\Spi\NotificatorInterface;
use Magento\User\Model\User;
use Space48\SSO\Model\Config;
use Space48\SSO\Model\UserManager;

class RestrictResetPassword
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

    public function aroundSendForgotPassword(
        NotificatorInterface $subject,
        callable $proceed,
        UserInterface $user
    ): void {
        if (
            $user instanceof User
            && $this->config->isEnabled()
            && $this->userManager->isSSOUser($user)
        ) {
            return;
        }

        $proceed($user);
    }
}
