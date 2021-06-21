<?php declare(strict_types=1);

namespace Space48\SSO\Model;

use Space48\SSO\Exception\ServiceException;

class AuthFactory
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \OneLogin\Saml2\AuthFactory
     */
    private $authFactory;

    /**
     * @var \OneLogin\Saml2\Auth
     */
    private $instance;

    public function __construct(
        Config $config,
        \OneLogin\Saml2\AuthFactory $authFactory
    ) {

        $this->config = $config;
        $this->authFactory = $authFactory;
    }

    /**
     * @throws ServiceException
     */
    public function getInstance(): \OneLogin\Saml2\Auth
    {
        if (!isset($this->instance)) {
            try {
                $this->instance = $this->authFactory->create([
                    'settings' => $this->config->getSAMLSettings(),
                ]);
            } catch (\Exception $e) {
                throw new ServiceException(__('Failed to initialise SSO instance: %error', ['error' => $e->getMessage()]), $e);
            }
        }

        return $this->instance;
    }
}
