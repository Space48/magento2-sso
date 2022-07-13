<?php declare(strict_types=1);

namespace Space48\SSO\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use OneLogin\Saml2\Constants;

class Config
{
    private const XML_PATH_ENABLED = 'admin/space48_sso/enabled';
    private const XML_PATH_SP_ENTITY_ID = 'admin/space48_sso/sp/entity_id';
    private const XML_PATH_SP_STATIC_MAGENTO_ROLE_NAME = 'admin/space48_sso/sp/static_magento_role_name';
    private const XML_PATH_IDP_ENTITY_ID = 'admin/space48_sso/idp/entity_id';
    private const XML_PATH_IDP_SIGN_ON_URL = 'admin/space48_sso/idp/sign_on_url';
    private const XML_PATH_IDP_PUBLIC_CERTIFICATE = 'admin/space48_sso/idp/public_certificate';
    private const XML_PATH_IDP_ATTRIBUTE_FIRSTNAME = 'admin/space48_sso/idp/attribute_firstname';
    private const XML_PATH_IDP_ATTRIBUTE_LASTNAME = 'admin/space48_sso/idp/attribute_lastname';
    private const XML_PATH_IDP_ATTRIBUTE_EMAIL = 'admin/space48_sso/idp/attribute_email';
    private const XML_PATH_IDP_ATTRIBUTE_ROLE = 'admin/space48_sso/idp/attribute_role';

    /**
     * @var Url
     */
    private $url;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(
        Url $url,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->url = $url;
        $this->config = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->config->isSetFlag(self::XML_PATH_ENABLED);
    }

    public function getFirstNameAttributeName(): string
    {
        return $this->config->getValue(self::XML_PATH_IDP_ATTRIBUTE_FIRSTNAME) ?? 'firstname';
    }

    public function getLastNameAttributeName(): string
    {
        return $this->config->getValue(self::XML_PATH_IDP_ATTRIBUTE_LASTNAME) ?? 'lastname';
    }

    public function getEmailAttributeName(): string
    {
        return $this->config->getValue(self::XML_PATH_IDP_ATTRIBUTE_EMAIL) ?? 'email';
    }

    public function geRoleAttributeName(): string
    {
        return $this->config->getValue(self::XML_PATH_IDP_ATTRIBUTE_ROLE) ?? 'role';
    }

    public function hasStaticMagentoRoleName(): bool
    {
        return $this->config->getValue(self::XML_PATH_SP_STATIC_MAGENTO_ROLE_NAME) !== null;
    }

    public function getStaticMagentoRoleName(): string
    {
        return $this->config->getValue(self::XML_PATH_SP_STATIC_MAGENTO_ROLE_NAME);
    }

    public function getSAMLSettings(): array
    {
        return [
            'strict' => true,
            'debug' => false,
            'baseurl' => $this->url->getBackendUrl(),
            'security' => [
                'requestedAuthnContext' => false,
            ],
            'sp' => [
                'entityId' => $this->config->getValue(self::XML_PATH_SP_ENTITY_ID) ?? $this->url->getBackendUrl(),
                'assertionConsumerService' => [
                    'url' => $this->url->getLoginCallbackUrl(),
                    'binding' => Constants::BINDING_HTTP_POST,
                ],
                'attributeConsumingService' => [
                    'serviceName' => 'Magento Backend',
                    'requestedAttributes' => [
                        [
                            'name' => 'email',
                            'isRequired' => true,
                        ],
                        [
                            'name' => 'firstname',
                            'isRequired' => true,
                        ],
                        [
                            'name' => 'lastname',
                            'isRequired' => true,
                        ],
                        [
                            'name' => 'role',
                            'isRequired' => true,
                        ],
                    ],
                ],
                'NameIDFormat' => Constants::NAMEID_PERSISTENT,
            ],
            'idp' => [
                'entityId' => $this->config->getValue(self::XML_PATH_IDP_ENTITY_ID),
                'singleSignOnService' => [
                    'url' => $this->config->getValue(self::XML_PATH_IDP_SIGN_ON_URL),
                    'binding' => Constants::BINDING_HTTP_REDIRECT,
                ],
                'x509cert' => $this->config->getValue(self::XML_PATH_IDP_PUBLIC_CERTIFICATE),
            ],
        ];
    }
}
