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

namespace MageINIC\ExtraFee\Api;

/**
 * Repository Interface
 */
interface ExtraFeeRepositoryInterface
{
    /**
     * Save extra fee.
     *
     * @param \MageINIC\ExtraFee\Api\Data\ExtraFeeInterface $extraFee
     * @return \MageINIC\ExtraFee\Api\Data\ExtraFeeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\ExtraFeeInterface $extraFee): Data\ExtraFeeInterface;

    /**
     * Retrieve extra fee.
     *
     * @param int $entityId
     * @return \MageINIC\ExtraFee\Api\Data\ExtraFeeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\ExtraFeeInterface;

    /**
     * Retrieve extra fee matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageINIC\ExtraFee\Api\Data\ExtraFeeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete extra fee.
     *
     * @param \MageINIC\ExtraFee\Api\Data\ExtraFeeInterface $extrafee
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ExtraFeeInterface $extrafee): bool;

    /**
     * Delete extra fee by ID.
     *
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId): bool;
}
