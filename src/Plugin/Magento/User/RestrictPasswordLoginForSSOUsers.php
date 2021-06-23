<?php declare(strict_types=1);

namespace Space48\SSO\Plugin\Magento\User;

use Magento\User\Model\User;
use Space48\SSO\Model\UserManager;

class RestrictPasswordLoginForSSOUsers
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

    public function aroundVerifyIdentity(User $subject, callable $proceed, ...$args)
    {
        if ($this->userManager->isSSOUser($subject)) {
            return false;
        }

        return $proceed(...$args);
    }
}
