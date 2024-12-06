<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@buckaroo.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@buckaroo.nl for more information.
 *
 * @copyright Copyright (c) Buckaroo B.V.
 * @license   https://tldrlegal.com/license/mit-license
 */
declare(strict_types=1);

namespace Buckaroo\Magento2SecondChance\Model\Config\Source\Email;

use Magento\Framework\DataObject;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Registry;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Email\Model\Template\Config;

class Template extends DataObject implements ArrayInterface
{
    /**
     * @var Registry
     */
    private $_coreRegistry;

    /**
     * @var Config
     */
    private $_emailConfig;

    /**
     * @var CollectionFactory
     */
    protected $_templatesFactory;

    /**
     * @param Registry          $coreRegistry
     * @param CollectionFactory $templatesFactory
     * @param Config            $emailConfig
     * @param array             $data
     */
    public function __construct(
        Registry $coreRegistry,
        CollectionFactory $templatesFactory,
        Config $emailConfig,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_coreRegistry = $coreRegistry;
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
    }

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->_coreRegistry->registry('config_system_email_template');
        if (!$collection) {
            $collection = $this->_templatesFactory->create();
            $collection->load();
            $this->_coreRegistry->register('config_system_email_template', $collection);
        }

        $options = $collection->toOptionArray();
        $templateId = explode('/', $this->getPath());
        $templateId = end($templateId);

        $templateLabel = $this->_emailConfig->getTemplateLabel($templateId);
        $templateLabel = __('%1 (Default)', $templateLabel);
        array_unshift($options, ['value' => $templateId, 'label' => $templateLabel]);

        array_walk($options, static function (&$item) {
            $item['__disableTmpl'] = true;
        });

        return $options;
    }
}
