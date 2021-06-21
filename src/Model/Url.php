<?php declare(strict_types=1);

namespace Space48\SSO\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Store\App\Response\Redirect;

class Url
{
    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        Redirect $redirect,
        UrlInterface $url
    ) {

        $this->redirect = $redirect;
        $this->url = $url;
    }

    public function getBackendUrl(): string
    {
        return rtrim($this->url->getBaseUrl(), '/') . '/' . $this->url->getAreaFrontName() . '/';
    }

    public function getLoginUrl(): string
    {
        return $this->url->getUrl('space48_sso/login/start', [UrlInterface::SECRET_KEY_PARAM_NAME => '']);
    }

    public function getLoginCallbackUrl(): string
    {
        return $this->url->getUrl('sso/login/process', [UrlInterface::SECRET_KEY_PARAM_NAME => '']);
    }

    public function getReturnUrl(): string
    {
        $url = $this->redirect->getRefererUrl();

        if ($url === $this->url->getBaseUrl()) {
            /*
             * Magento requires an active backend session to determine
             * what should be the start page after logging in, and this
             * method is called before we've logged in. Therefore, send
             * the user to the login controller which will redirect to
             * the appropriate page.
             */
            $url = $this->url->getUrl('adminhtml/auth/login');
        }

        return $url;
    }
}
