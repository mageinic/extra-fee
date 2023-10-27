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

define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils',
        'mage/url'
    ],
    function ($,Component,quote,totals,priceUtils,url) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'MageINIC_ExtraFee/checkout/summary/extrafee'
            },
            totals: quote.getTotals(),

            /**
             * @returns {boolean}
             */
            isModuleEnable: function () {
                var serviceUrl = url.build('extrafee/extrafee/storeconfig');
                var status = this.getEnableDisableValue(serviceUrl);
                var extraFeeSegment = totals.getSegment('extra_fee');
                var extrafee = extraFeeSegment ? extraFeeSegment.value : 0;
                if(status == 1 && extrafee != 0) {
                    return true;
                }else {
                    return false;
                }
            },

            /**
             * @param serviceUrl
             * @returns {null}
             */
            getEnableDisableValue: function (serviceUrl) {
                var result = null;
                $.ajax({
                    async: false,
                    url: serviceUrl,
                    method: 'GET',
                    data: { 'request': "", 'target': 'arrange_url', 'method': 'method_target' },
                    success: function (data) {
                        result = data;
                    }
                });
                return result;
            },

            getExtraFeeiscountTotal : function () {
                var price = 0;
                    if (this.totals()) {
                        price = totals.getSegment('extra_fee').value;
                    }
                return this.getFormattedPrice(price);
            }
        });
    }
);
