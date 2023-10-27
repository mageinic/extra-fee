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
use MageINIC\ExtraFee\Api\ExtraFeeRepositoryInterface;
use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee\Collection;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\DataObjectHelper;
use MageINIC\ExtraFee\Api\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee as Resource;
use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee\CollectionFactory as ExtraFeeCollection;

class ExtraFeeRepository implements ExtraFeeRepositoryInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var ExtraFeeFactory
     */
    private $extraFeeFactory;

    /**
     * @var Data\ExtraFeeSearchResultsInterfaceFactory
     */
    private $extraFeeSearchResultsInterfaceFactory;

    /**
     * @var ExtraFeeCollection
     */
    private $extraFeeCollection;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Data\ExtraFeeInterfaceFactory
     */
    private $extraFeeInterfaceFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param Data\ExtraFeeInterfaceFactory $extraFeeInterfaceFactory
     * @param ExtraFeeCollection $extraFeeCollection
     * @param Data\ExtraFeeSearchResultsInterfaceFactory $extraFeeSearchResultsInterfaceFactory
     * @param StoreManagerInterface $storeManager
     * @param Resource $resource
     * @param ExtraFeeFactory $extraFeeFactory
     */
    public function __construct(
        DataObjectProcessor                        $dataObjectProcessor,
        DataObjectHelper                           $dataObjectHelper,
        Data\ExtraFeeInterfaceFactory              $extraFeeInterfaceFactory,
        ExtraFeeCollection                         $extraFeeCollection,
        Data\ExtraFeeSearchResultsInterfaceFactory $extraFeeSearchResultsInterfaceFactory,
        StoreManagerInterface                      $storeManager,
        Resource                                   $resource,
        ExtraFeeFactory                            $extraFeeFactory
    ) {
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->extraFeeFactory = $extraFeeFactory;
        $this->extraFeeSearchResultsInterfaceFactory = $extraFeeSearchResultsInterfaceFactory;
        $this->extraFeeCollection = $extraFeeCollection;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->extraFeeInterfaceFactory = $extraFeeInterfaceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save ExtraFee data
     *
     * @param ExtraFeeInterface $extraFee
     * @return ExtraFee
     * @throws CouldNotSaveException
     */
    public function save(ExtraFeeInterface $extraFee): Data\ExtraFeeInterface
    {
        if (empty($extraFee->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $extraFee->setStoreId($storeId);
        }
        try {
            $this->resource->save($extraFee);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the ExtraFee: %1',
                $exception->getMessage()
            ));
        }
        return $extraFee;
    }

    /**
     * Load ExtraFee data by given ExtraFee Identity
     *
     * @param int $entityId
     * @return extrafee
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): Data\ExtraFeeInterface
    {
        $extraFee = $this->extraFeeFactory->create();
        $this->resource->load($extraFee, $entityId);
        if (!$extraFee->getId()) {
            throw new NoSuchEntityException(__('ExtraFee with id "%1" does not exist.', $entityId));
        }
        return $extraFee;
    }

    /**
     * Load ExtraFee data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $criteria
     * @return Collection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->extraFeeSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->extraFeeCollection->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $extraFee = [];
        /** @var ExtraFee $extraFeeModel */
        foreach ($collection as $extraFeeModel) {
            $extraFeeData = $this->extraFeeInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $extraFeeData,
                $extraFeeModel->getData(),
                ExtraFeeInterface::class
            );
            $extraFee[] = $this->dataObjectProcessor->buildOutputDataArray(
                $extraFeeData,
                ExtraFeeInterface::class
            );
        }
        $searchResults->setItems($extraFee);
        return $searchResults;
    }

    /**
     * Delete ExtraFee
     *
     * @param ExtraFeeInterface $extraFee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ExtraFeeInterface $extraFee): bool
    {
        try {
            $this->resource->delete($extraFee);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete ExtraFee by given ExtraFee Identity
     *
     * @param string $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }
}
