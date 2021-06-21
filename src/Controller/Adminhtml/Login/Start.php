<?php declare(strict_types=1);

namespace Space48\SSO\Controller\Adminhtml\Login;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Space48\SSO\Service\Login;

class Start implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /**
     * @var Login
     */
    private $loginService;

    /**
     * @var ForwardFactory
     */
    private $forwardFactory;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    public function __construct(
        Login $loginService,
        ForwardFactory $forwardFactory,
        RedirectFactory $redirectFactory
    ) {
        $this->loginService = $loginService;
        $this->forwardFactory = $forwardFactory;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute()
    {
        if (!$this->loginService->isAvailable()) {
            return $this->forwardFactory
                ->create()
                ->forward('noroute');
        }

        $loginUrl = $this->loginService->initLogin();

        return $this->redirectFactory
            ->create()
            ->setUrl($loginUrl);
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
}
