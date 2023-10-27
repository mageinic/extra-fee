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

namespace MageINIC\ExtraFee\Controller\Adminhtml\ExtraFee;

use MageINIC\ExtraFee\Model\ExtraFee;
use Magento\Backend\Model\Session;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\ExtraFee\Model\ExtraFeeFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageINIC\ExtraFee\Model\ResourceModel\ExtraFee\CollectionFactory;
use MageINIC\ExtraFee\Api\ExtraFeeRepositoryInterface;

/**
 * Extra Fee Save
 */
class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_ExtraFee::save';

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var Session
     */
    protected Session $adminsession;

    /**
     * @var ExtraFeeFactory
     */
    private ExtraFeeFactory $extraFeeFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ExtraFeeRepositoryInterface
     */
    private ExtraFeeRepositoryInterface $extraFeeRepository;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param ExtraFeeRepositoryInterface $extraFeeRepository
     * @param Action\Context $context
     * @param ExtraFeeFactory $extraFeeFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $adminsession
     * @param DataPersistorInterface $dataPersistor
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ExtraFeeRepositoryInterface $extraFeeRepository,
        Action\Context              $context,
        ExtraFeeFactory             $extraFeeFactory,
        StoreManagerInterface       $storeManager,
        Session                     $adminsession,
        DataPersistorInterface      $dataPersistor,
        CollectionFactory           $collectionFactory
    ) {
        parent::__construct($context);
        $this->adminsession = $adminsession;
        $this->dataPersistor = $dataPersistor;
        $this->extraFeeFactory = $extraFeeFactory;
        $this->storeManager = $storeManager;
        $this->extraFeeRepository = $extraFeeRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save blog record action
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $post = $this->getRequest()->getPostValue();
        if ($post) {
            $validate = $this->validatePaymentMethod($post);
            if (empty($post['entity_id']) && !$validate) {
                $post['entity_id'] = null;
            }
            /** @var ExtraFee $model */
            $model = $this->extraFeeFactory->create();
            $id = $this->getRequest()->getParam('entity_id');
            if (!$validate) {
                if ($id) {
                    try {
                        $model = $this->extraFeeRepository->getById($id);
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage(__('This ExtraFee no longer exists.'));
                        return $resultRedirect->setPath('*/*/');
                    }
                }
                $post['updated_at'] = date('Y-m-d H:i:s');
                $payment = implode(',', $post['paymentmethod']);
                $post['paymentmethod'] = $payment;
                $model->setData($post);
                try {
                    $this->extraFeeRepository->save($model);
                    $this->messageManager->addSuccess(__('The Extrafee has been saved.'));
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect
                            ->setPath('*/*/edit', ['id' => $model->getEntityId(), '_current' => true]);
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager
                        ->addExceptionMessage($e, __('Something went wrong while saving the Extrafee.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('ExtraFee already existed'));
                return $resultRedirect->setPath('*/*/index', ['entity_id' => $id]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Validate Payment Method
     *
     * @param mixed $post
     * @return bool
     */
    private function validatePaymentMethod(mixed $post): bool
    {
        $result = false;
        $collections = $this->collectionFactory->create();
        $payment = implode(",", $post['paymentmethod']);
        if ($post['entity_id']) {
            if (isset($post['paymentmethod']) && !empty($post['paymentmethod'])) {
                $postPaymentMethods = $post['paymentmethod'];
                if (count($postPaymentMethods)) {
                    foreach ($postPaymentMethods as $method) {
                        $collection = $collections->addFieldToFilter(
                            'paymentmethod',
                            ['like' => '%' . $method . '%']
                        );
                        if ($collection->getSize()) {
                            foreach ($collection->getData() as $item) {
                                if ($item['paymentmethod'] == $payment) {
                                    $data = $this->checkPaymentMethod($post);
                                    if ($data) {
                                        return true;
                                    }
                                } else {
                                    $data = $this->checkPaymentMethod($post);
                                    if ($data) {
                                        return true;
                                    }
                                }
                            }
                        } else {
                            $data = $this->checkPaymentMethod($post);
                            if ($data) {
                                return true;
                            }
                        }
                    }
                }
            }
        } else {
            $collections->addFieldToSelect('paymentmethod');
            if ($collections->getSize()) {
                foreach ($collections as $collection) {
                    $results = array_intersect(explode(
                        ",",
                        $collection->getPaymentMethod()
                    ), explode(",", $payment));
                    if ($results) {
                        return true;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Check Payment Method
     *
     * @param $post
     * @return bool
     */
    private function checkPaymentMethod($post): bool
    {
        $collections = $this->collectionFactory->create();
        $collections->addFieldToSelect('paymentmethod');
        $collections->addFieldToFilter('entity_id', ['neq' => $post['entity_id']]);
        if ($collections->getSize()) {
            foreach ($collections as $collection) {
                $results = array_intersect(explode(
                    ",",
                    $collection->getPaymentmethod()
                ), $post['paymentmethod']);
                if ($results) {
                    return true;
                }
            }
        }
        return false;
    }
}
