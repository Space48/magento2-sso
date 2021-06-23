<?php declare(strict_types=1);

namespace Space48\SSO\Plugin\Magento\User;

use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\User\Block\User\Edit\Tab\Main;

class AddSSOFieldsToUserEditForm
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function beforeGetFormHtml(Main $subject): void
    {
        $form = $subject->getForm();
        if (!$form instanceof Form) {
            return;
        }

        $fieldset = $form->getElement('base_fieldset');
        if (!$fieldset instanceof Fieldset) {
            return;
        }

        $user = $this->registry->registry('permissions_user');

        $fieldset->addField('is_sso', 'select', [
            'name' => 'is_sso',
            'label' => __('Authenticates with Single Sign-On'),
            'id' => 'is_sso',
            'class' => 'input-select',
            'options' => ['0' => __('No'), '1' => __('Yes')],
            'value' => ($user instanceof DataObject) ? $user->getData('is_sso') : '0',
        ]);

        $subject->setForm($form);
    }
}
