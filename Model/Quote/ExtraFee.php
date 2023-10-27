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

namespace MageINIC\ExtraFee\Model\Quote;

use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee\CollectionFactory;
use MageINIC\ExtraFee\Model\ResourceModel\ExtraFeeStore\CollectionFactory as ExtraFeeStoreCollection;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Store\Model\StoreManagerInterface;
use MageINIC\ExtraFee\Helper\Data;

/**
 * MageINIC Class ExtraFee
 */
class ExtraFee extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    protected PriceCurrencyInterface $_priceCurrency;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ExtraFeeStoreCollection
     */
    private ExtraFeeStoreCollection $extraFeeStoreCollection;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * ExtraFee constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param CheckoutSession $checkoutSession
     * @param ExtraFeeStoreCollection $extraFeeStoreCollection
     * @param Data $helper
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface        $storeManager,
        CollectionFactory                                 $collectionFactory,
        CheckoutSession                                   $checkoutSession,
        ExtraFeeStoreCollection                           $extraFeeStoreCollection,
        Data $helper
    ) {
        $this->_priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->extraFeeStoreCollection = $extraFeeStoreCollection;
        $this->helper = $helper;
    }

    /**
     * Collect Method
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this|ExtraFee
     * @throws NoSuchEntityException
     */
    public function collect(
        Quote                          $quote,
        ShippingAssignmentInterface    $shippingAssignment,
        Total                          $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $subtotal = $quote->getSubtotal();
        $enable = $this->helper->isEnabled();
        $paymentMethod = $quote->getPayment()->getMethod();
        if ($enable) {
            if (!empty($paymentMethod)) {
                $collections = $this->collectionFactory->create();
                $collections->addFieldToSelect('*');
                $collections->addFieldToFilter('paymentmethod', ['like' => '%'.$paymentMethod.'%']);
                $status = 0;
                $entityId = 0;
                foreach ($collections as $collection) {
                    $status = $collection->getStatus();
                    $entityId = $collection->getEntityId();
                    $maxAmount = $collection->getMaxAmount();
                    $minAmount = $collection->getMinAmount();
                }
                $storecollections = $this->extraFeeStoreCollection->create();
                $storecollections->addFieldToSelect('store_id');
                $storecollections->addFieldToFilter('entity_id', ['eq' => $entityId]);
                $extraFeeStoreId = [];
                foreach ($storecollections as $storecollection) {
                    $extraFeeStoreId[] = $storecollection->getStoreId();
                }
                $currentStoreId = $this->storeManager->getStore()->getId();
                if ((!empty($status)) && ($minAmount <= $subtotal && $maxAmount >= $subtotal)) {
                    if ($payMethods = $collections->getData()) {
                        $extraFees = 0;
                        foreach ($payMethods as $payMethod) {
                            $results = array_intersect(explode(
                                ",",
                                $payMethod['paymentmethod']
                            ), explode(' ', $paymentMethod));
                            if ($results) {
                                if (in_array($currentStoreId, $extraFeeStoreId)) {
                                    $extraFees = $payMethod['extra_fee'];
                                }
                            }
                        }
                        $finalExtraFee = $extraFees;
                        $total->setTotalAmount('extra_fee', $finalExtraFee);
                        $total->setBaseTotalAmount('extra_fee', $finalExtraFee);
                        $total->setExtraFee($finalExtraFee);
                        $total->setBaseExtraFee($finalExtraFee);
                        $quote->setData('extra_fee', $finalExtraFee);
                        $quote->setData('testing', $finalExtraFee);
                    }
                }
            } else {
                $total->setTotalAmount('extra_fee', 0);
                $total->setBaseTotalAmount('extra_fee', 0);
                $total->setExtraFee(0);
                $total->setBaseExtraFee(0);
            }
        }
        return $this;
    }

    /**
     * Fetch Method
     *
     * @param Quote $quote
     * @param Total $total
     * @return array|void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function fetch(Quote $quote, Total $total)
    {
        $subtotal = $quote->getSubtotal();
        $paymentMethod = $this->checkoutSession->getQuote()->getPayment()->getMethod();
        $enable = $this->helper->isEnabled();
        if ($enable) {
            if (!empty($paymentMethod)) {
                $collections = $this->collectionFactory->create();
                $collections->addFieldToSelect('*');
                $collections->addFieldToFilter('paymentmethod', ['like' => '%'.$paymentMethod.'%']);
                $status = 0;
                $entityId = 0;
                foreach ($collections as $collection) {
                    $status = $collection->getStatus();
                    $entityId = $collection->getEntityId();
                    $maxAmount = $collection->getMaxAmount();
                    $minAmount = $collection->getMinAmount();
                }
                $storecollections = $this->extraFeeStoreCollection->create();
                $storecollections->addFieldToSelect('store_id');
                $storecollections->addFieldToFilter('entity_id', ['eq' => $entityId]);
                $extraFeeStoreId = [];
                foreach ($storecollections as $storecollection) {
                    $extraFeeStoreId[] = $storecollection->getStoreId();
                }
                $currentStoreId = $this->storeManager->getStore()->getId();
                if ((!empty($status)) && ($minAmount <= $subtotal && $maxAmount >= $subtotal)) {
                    if ($payMethods = $collections->getData()) {
                        $extraFees = 0;
                        foreach ($payMethods as $payMethod) {
                            $results = array_intersect(
                                explode(",", $payMethod['paymentmethod']),
                                explode(' ', $paymentMethod)
                            );
                            if ($results) {
                                if (in_array($currentStoreId, $extraFeeStoreId)) {
                                    $extraFees = $payMethod['extra_fee'];
                                }
                            }
                        }
                        return ['code' => 'extra_fee', 'title' => $this->getLabel(), 'value' => $extraFees];
                    }
                } else {
                    return ['code' => 'extra_fee', 'title' => $this->getLabel(), 'value' => 0];
                }
            }
        }
    }

    /**
     * Get Label
     *
     * @return Phrase|string
     */
    public function getLabel(): Phrase|string
    {
        return __('Extraaa Fee');
    }
}
