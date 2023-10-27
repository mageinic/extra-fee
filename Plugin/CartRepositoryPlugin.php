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

namespace MageINIC\ExtraFee\Plugin;

use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee\CollectionFactory;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/*
 * Plugin For Cart details
 */
class CartRepositoryPlugin
{
    /**
     * @var CartExtensionFactory
     */
    protected CartExtensionFactory $extensionFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $extraFeeCollectionFactory;

    /**
     * @param CartExtensionFactory $extensionFactory
     * @param CollectionFactory $extraFeeCollectionFactory
     */
    public function __construct(
        CartExtensionFactory $extensionFactory,
        CollectionFactory $extraFeeCollectionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
        $this->extraFeeCollectionFactory = $extraFeeCollectionFactory;
    }

    /**
     * After Get Method
     *
     * @param CartRepositoryInterface $subject
     * @param CartInterface $quote
     * @return CartInterface
     */
    public function afterGet(CartRepositoryInterface $subject, CartInterface $quote)
    {
        $selectedPaymentMethod = $quote->getPayment()->getMethod();
        if (!$selectedPaymentMethod) {
            return $quote;
        }

        $extraFeeCollections = $this->extraFeeCollectionFactory->create();
        $extraFeeCollections->addFieldToFilter('status', 1);

        foreach ($extraFeeCollections as $extraFee) {
            $paymentMethod = $extraFee->getPaymentMethod();
            if ($paymentMethod === $selectedPaymentMethod) {
                $customAttribute = $extraFee->getExtraFee();
                $extensionAttributes = $quote->getExtensionAttributes();
                $extensionAttributes = $extensionAttributes ?: $this->extensionFactory->create();
                $extensionAttributes->setData('extra_fee', $customAttribute);
                $quote->setExtensionAttributes($extensionAttributes);
                return $quote;
            }
        }

        return $quote;
    }
}
