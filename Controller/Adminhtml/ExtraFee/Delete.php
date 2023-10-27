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

use Magento\Backend\App\Action;
use MageINIC\ExtraFee\Model\ExtraFeeFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Extra Fee Controller Delete
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_ExtraFee::delete';

    /**
     * @var ExtraFeeFactory
     */
    private ExtraFeeFactory $extraFeeFactory;

    /**
     * @param Action\Context $context
     * @param ExtraFeeFactory $extraFeeFactory
     */
    public function __construct(
        Action\Context $context,
        ExtraFeeFactory $extraFeeFactory
    ) {
        parent::__construct($context);
        $this->extraFeeFactory = $extraFeeFactory;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->extraFeeFactory->create();
        $model->load($id);
        if ($model->getEntityId()) {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Record is deleted successfully!'));
            $this->_redirect('*/*/index');
        } else {
            $this->messageManager->addErrorMessage(__('Record  not found.'));
            $this->_redirect('*/*/index');
        }
    }
}
