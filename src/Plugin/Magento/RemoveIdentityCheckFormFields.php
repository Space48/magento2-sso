<?php declare(strict_types=1);

namespace Space48\SSO\Plugin\Magento;

use Magento\Backend\Block\Widget\Form as FormBlock;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Space48\SSO\Model\Config;
use Space48\SSO\Model\UserManager;

class RemoveIdentityCheckFormFields
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

    public function beforeGetFormHtml(FormBlock $subject): void
    {
        if (!$this->config->isEnabled()
            || !$this->userManager->isCurrentUserSSO()) {
            return;
        }

        $form = $subject->getForm();
        if (!$form instanceof Form) {
            return;
        }

        $fieldset = $form->getElement('current_user_verification_fieldset');
        if (!$fieldset instanceof Form\Element\Fieldset) {
            return;
        }

        $form->removeField($fieldset->getId());
        $form->getElements()->remove($fieldset->getId());

        foreach ($fieldset->getElements() as $element) {
            /** @var AbstractElement $element */
            $form->removeField($element->getId());
            $form->addField(
                $element->getId(),
                'hidden',
                [
                    'name' => $element->getData('name'),
                    'value' => 'placeholder',
                ],
            );
        }

        $subject->setForm($form);
    }
}
