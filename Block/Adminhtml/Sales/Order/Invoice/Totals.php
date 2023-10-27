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

namespace MageINIC\ExtraFee\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Directory\Model\Currency;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Sales\Model\Order;

/**
 * Invoice Totals
 */
class Totals extends Template
{
    /**
     * @var Currency
     */
    protected Currency $_currency;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param Context $context
     * @param Currency $currency
     * @param CollectionFactory $collection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Currency $currency,
        CollectionFactory $collection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_currency = $currency;
        $this->collection = $collection;
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getInvoice(): Order
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get Source
     *
     * @return mixed
     */
    public function getSource(): mixed
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();
        if ($this->getInvoice()->getExtraFee() != 0) {
            $total = new DataObject(
                [
                    'code' => 'extra_fee',
                    'value' => $this->getInvoice()->getExtraFee(),
                    'label' => 'Extra Fee',
                ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }
        return $this;
    }
}
