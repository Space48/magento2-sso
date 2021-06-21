<?php declare(strict_types=1);

namespace Space48\SSO\Model;

class Storage
{
    private const KEY_LAST_REQUEST_ID = 'sso_last_request_id';
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    public function __construct(
        \Magento\Backend\Model\Session $session
    ) {
        $this->session = $session;
    }

    public function setLastRequestId(string $id): void
    {
        $this->session->setData(self::KEY_LAST_REQUEST_ID, $id);
    }

    public function getLastRequestId(bool $clear = true): ?string
    {
        return $this->session->getData(self::KEY_LAST_REQUEST_ID, $clear);
    }
}
