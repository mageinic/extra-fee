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

namespace MageINIC\ExtraFee\Model;

use MageINIC\ExtraFee\Api\Data\ExtraFeeInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * MageINIC Extra Fee
 */
class ExtraFee extends AbstractModel implements ExtraFeeInterface
{
    public const CACHE_TAG = 'MageINIC_extrafee';

    /**
     * @var string
     */
    protected $cacheTag = self::CACHE_TAG;

    /**
     * Construct Method
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\ExtraFee::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Stores
     *
     * @return array|mixed|null
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * Retrieve extra fee id
     *
     * @return int
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Retrieve title
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return (string)$this->getData(self::TITLE);
    }

    /**
     * Retrieve payment method
     *
     * @return string
     */
    public function getPaymentmethod(): ?string
    {
        return (string)$this->getData(self::PAYMENTMETHOD);
    }

    /**
     * Retrieve extra fee
     *
     * @return int
     */
    public function getExtraFee(): ?int
    {
        return $this->getData(self::EXTRAFEE);
    }

    /**
     * Retrieve Max Amount
     *
     * @return int
     */
    public function getMaxAmount(): ?int
    {
        return $this->getData(self::MAXAMOUNT);
    }

    /**
     * Retrieve Min Amount
     *
     * @return int
     */
    public function getMinAmount(): ?int
    {
        return $this->getData(self::MINAMOUNT);
    }

    /**
     * Retrieve extra fee
     *
     * @return int
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Retrieve extra fee creation time
     *
     * @return string
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Retrieve extra fee update time
     *
     * @return string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Set Entity ID
     *
     * @param int $entityId
     * @return ExtraFeeInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Set extrafee title
     *
     * @param string $title
     * @return ExtraFeeInterface
     */
    public function setTitle(string $title): ExtraFeeInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set extra fee payment method
     *
     * @param string $paymentmethod
     * @return ExtraFeeInterface
     */
    public function setPaymentmethod($paymentmethod)
    {
        return $this->setData(self::PAYMENTMETHOD, $paymentmethod);
    }

    /**
     * Set Extra fee
     *
     * @param int $extra_fee
     * @return ExtraFeeInterface
     */
    public function setExtraFee(int $extra_fee): ExtraFeeInterface
    {
        return $this->setData(self::EXTRAFEE, $extra_fee);
    }

    /**
     * Set Max Amount
     *
     * @param int $max_amount
     * @return ExtraFeeInterface
     */
    public function setMaxAmount(int $max_amount): ExtraFeeInterface
    {
        return $this->setData(self::EXTRAFEE, $max_amount);
    }

    /**
     * Set Min Amount
     *
     * @param int $min_amount
     * @return ExtraFeeInterface
     */
    public function setMinAmount(int $min_amount): ExtraFeeInterface
    {
        return $this->setData(self::EXTRAFEE, $min_amount);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return ExtraFeeInterface
     */
    public function setStatus(string $status): ExtraFeeInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set created_at
     *
     * @param string $creationTime
     * @return ExtraFeeInterface
     */
    public function setCreatedAt(string $creationTime): ExtraFeeInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set updatedAt
     *
     * @param string $updateTime
     * @return ExtraFeeInterface
     */
    public function setUpdatedAt($updateTime): ExtraFeeInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
}
