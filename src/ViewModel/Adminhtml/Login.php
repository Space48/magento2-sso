<?php declare(strict_types=1);

namespace Space48\SSO\ViewModel\Adminhtml;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Space48\SSO\Model\Url;

class Login implements ArgumentInterface
{
    /**
     * @var \Space48\SSO\Service\Login
     */
    private $loginService;

    /**
     * @var Url
     */
    private $url;

    public function __construct(
        \Space48\SSO\Service\Login $loginService,
        Url $url
    ) {
        $this->loginService = $loginService;
        $this->url = $url;
    }

    public function isEnabled(): bool
    {
        return $this->loginService->isAvailable();
    }

    public function getLoginUrl(): string
    {
        return $this->url->getLoginUrl();
    }
}
