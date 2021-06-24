<?php declare(strict_types=1);

namespace Space48\SSO\Plugin\Magento\User;

use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\Spi\NotificatorInterface;
use Magento\User\Model\User;
use Space48\SSO\Model\UserManager;

class RestrictResetPasswordForSSOUsers
{
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(
        UserManager $userManager
    ) {
        $this->userManager = $userManager;
    }

    public function aroundSendForgotPassword(
        NotificatorInterface $subject,
        callable $proceed,
        UserInterface $user
    ): void {
        if ($user instanceof User && $this->userManager->isSSOUser($user)) {
            return;
        }

        $proceed($user);
    }
}
