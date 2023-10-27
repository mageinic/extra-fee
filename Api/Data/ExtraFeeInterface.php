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

namespace MageINIC\ExtraFee\Api\Data;

/**
 * Interface for ExtraFeeInterface
 */
interface ExtraFeeInterface
{
    public const ENTITY_ID = 'entity_id';
    public const TITLE = 'title';
    public const PAYMENTMETHOD = 'paymentmethod';
    public const EXTRAFEE = 'extra_fee';
    public const MAXAMOUNT = 'max_amount';
    public const MINAMOUNT = 'min_amount';
    public const STATUS = 'status';
    public const CREATION_TIME = 'created_at';
    public const UPDATE_TIME = 'updated_at';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Get payment method
     *
     * @return string|null
     */
    public function getPaymentmethod(): ?string;

    /**
     * Get extra_fee
     *
     * @return int|null
     */
    public function getExtraFee(): ?int;

    /**
     * Get extra_fee
     *
     * @return int|null
     */
    public function getMaxAmount(): ?int;

    /**
     * Get extra_fee
     *
     * @return int|null
     */
    public function getMinAmount(): ?int;

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;
    /**
     * Set entity ID
     *
     * @param int $entity_id
     * @return ExtraFeeInterface
     */
    public function setEntityId($entity_id);

    /**
     * Set title
     *
     * @param string $title
     * @return ExtraFeeInterface
     */
    public function setTitle(string $title): ExtraFeeInterface;

    /**
     * Set payment method
     *
     * @param string $paymentmethod
     * @return ExtraFeeInterface
     */
    public function setPaymentmethod($paymentmethod);

    /**
     * Set extra_fee
     *
     * @param int $extra_fee
     * @return ExtraFeeInterface
     */
    public function setExtraFee(int $extra_fee): ExtraFeeInterface;

    /**
     * Set max_amount
     *
     * @param int $max_amount
     * @return ExtraFeeInterface
     */
    public function setMaxAmount(int $max_amount): ExtraFeeInterface;

    /**
     * Set min_amount
     *
     * @param int $min_amount
     * @return ExtraFeeInterface
     */
    public function setMinAmount(int $min_amount): ExtraFeeInterface;

    /**
     * Set status
     *
     * @param string $status
     * @return ExtraFeeInterface
     */
    public function setStatus(string $status): ExtraFeeInterface;

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return ExtraFeeInterface
     */
    public function setCreatedAt(string $creationTime): ExtraFeeInterface;

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return ExtraFeeInterface
     */
    public function setUpdatedAt($updateTime): ExtraFeeInterface;
}
