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

namespace MageINIC\ExtraFee\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use MageINIC\ExtraFee\Api\Data\ExtraFeeInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Store;

/**
 * MageINIC ExtraFee Resource Model
 */
class ExtraFee extends AbstractDb
{
    /**
     * @var null
     */
    protected $_store = null;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param mixed $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Construct Method
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('extra_fee_table', 'entity_id');
    }

    /**
     * Get Connection
     *
     * @return false|AdapterInterface
     * @throws \Exception
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(ExtraFeeInterface::class)->getEntityConnection();
    }

    /**
     * Get ModelName Id
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param mixed $field
     * @return false|float|int|mixed|string
     * @throws LocalizedException
     */
    private function getModelnameId(AbstractModel $object, $value, $field = null): mixed
    {
        $entityMetadata = $this->metadataPool->getMetadata(ExtraFeeInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'entity_id';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $pageId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $pageId = count($result) ? $result[0] : false;
        }
        return $pageId;
    }

    /**
     * Load Method
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param mixed $field
     * @return $this|ExtraFee
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $pageId = $this->getModelnameId($object, $value, $field);
        if ($pageId) {
            $this->entityManager->load($object, $pageId);
        }
        return $this;
    }

    /**
     * Load Select
     *
     * @param mixed $field
     * @param mixed $value
     * @param mixed $object
     * @return Select
     * @throws LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(ExtraFeeInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = [Store::DEFAULT_STORE_ID, (int)$object->getStoreId(),];
            $select->join(['extrafee_store' => $this->getTable('extrafee_store')], $this->getMainTable()
                . '.' . $linkField . ' = extrafee_store.' . $linkField, [])
                ->where('is_active = ?', 1)
                ->where('extrafee_store.store_id IN (?)', $storeIds)
                ->order('extrafee_store.store_id DESC')
                ->limit(1);
        }
        return $select;
    }

    /**
     * Lookup Store Ids
     *
     * @param mixed $pageId
     * @return array
     * @throws LocalizedException
     */
    public function lookupStoreIds($pageId): array
    {
        $connection = $this->getConnection();
        $entityMetadata = $this->metadataPool->getMetadata(ExtraFeeInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['cps' => $this->getTable('extrafee_store')], 'store_id')
            ->join(['cp' => $this->getMainTable()], 'cps.' . $linkField . ' = cp.' . $linkField, [])
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :store_id');

        return $connection->fetchCol($select, ['store_id' => (int)$pageId]);
    }

    /**
     * Set Store
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Get Store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    /**
     * Save Method
     *
     * @param AbstractModel $object
     * @return $this|ExtraFee
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Delete Method
     *
     * @param AbstractModel $object
     * @return $this|ExtraFee
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
