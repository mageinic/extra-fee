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

namespace MageINIC\ExtraFee\Model\ResourceModel\ExtraFee\Relation\Store;

use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * ExtraFee Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ExtraFee
     */
    private ExtraFee $extraFee;

    /**
     * Construct Method
     *
     * @param ExtraFee $extraFee
     */
    public function __construct(
        ExtraFee $extraFee
    ) {
        $this->extraFee = $extraFee;
    }

    /**
     * Execute Method
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = []): object
    {
        if ($entity->getId()) {
            $stores = $this->extraFee->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
