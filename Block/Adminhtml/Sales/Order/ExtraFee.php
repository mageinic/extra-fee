<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_ExtraFee
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\ExtraFee\Block\Adminhtml\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;

/**
 * Extra Fee Block
 */
class ExtraFee extends Template
{
    /**
     * @var Order
     */
    protected Order $_order;

    /**
     * @var DataObject
     */
    protected $_source;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if we need display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary(): bool
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return DataObject
     */
    public function getSource(): DataObject
    {
        return $this->_source;
    }

    /**
     * Get Store
     *
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->_order->getStore();
    }

    /**
     * Get Order
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->_order;
    }

    /**
     * Label Properties
     *
     * @return array
     */
    public function getLabelProperties(): array
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Value Properties
     *
     * @return array
     */
    public function getValueProperties(): array
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        if ($this->_source->getExtraFee() != 0) {
            $fee = new DataObject(
                [
                    'code' => 'extra_fee',
                    'value' => $this->_source->getExtraFee(),
                    'label' => 'Extra Fee',
                ]
            );
            $parent->addTotal($fee, 'extra_fee');
        }
        return $this;
    }
}
