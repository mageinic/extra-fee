<?php declare(strict_types=1);
namespace MageINIC\ExtraFee\Model\Sales\Pdf;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;

/**
 * PDF Extra Fee
 */
class ExtraFee extends DefaultTotal
{
    /**
     * Get array of arrays with totals information for display in PDF
     *
     * @return array
     */
    public function getTotalsForDisplay(): array
    {
        $extraFee = $this->getOrder()->getExtraFee();
        if ($extraFee === null) {
            return [];
        }
        $amountInclTax = $this->getOrder()->formatPriceTxt($extraFee);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        return [
            [
                'amount' => $this->getAmountPrefix() . $amountInclTax,
                'label' => __('Extra Fee') . ':',
                'font_size' => $fontSize,
            ]
        ];
    }
}
