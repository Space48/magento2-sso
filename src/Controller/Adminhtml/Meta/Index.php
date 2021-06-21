<?php declare(strict_types=1);

namespace Space48\SSO\Controller\Adminhtml\Meta;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Space48\SSO\Service\Metadata;

class Index implements HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * @var Metadata
     */
    private $metadataService;

    /**
     * @var ForwardFactory
     */
    private $forwardFactory;

    /**
     * @var RawFactory
     */
    private $rawResultFactory;

    public function __construct(
        Metadata $metadataService,
        ForwardFactory $forwardFactory,
        RawFactory $rawResultFactory
    ) {
        $this->metadataService = $metadataService;
        $this->forwardFactory = $forwardFactory;
        $this->rawResultFactory = $rawResultFactory;
    }

    public function execute()
    {
        if (!$this->metadataService->isAvailable()) {
            return $this->forwardFactory
                ->create()
                ->forward('noroute');
        }

        $metadata = $this->metadataService->getMetadata();

        return $this->rawResultFactory
            ->create()
            ->setHeader('Content-Type', 'text/xml')
            ->setContents($metadata);
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
