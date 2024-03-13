BX.namespace('BX.Sale.PersonalOrderComponent');

(function() {
	BX.Sale.PersonalOrderComponent.PersonalOrderDetail = {
		init : function(params)
		{
			var listShipmentWrapper = document.getElementsByClassName('sale-personal-order-detail-block-shipment');
			var listPaymentWrapper = document.getElementsByClassName('sale-personal-order-detail-block-payment');

			Array.prototype.forEach.call(listShipmentWrapper, function(shipmentWrapper)
			{
				var detailShipmentBlock = shipmentWrapper.getElementsByClassName('sale-personal-order-detail-block-shipment-information')[0];
				var showInformation = shipmentWrapper.getElementsByClassName('sale-personal-order-detail-block-shipment-expand')[0];
				var hideInformation = shipmentWrapper.getElementsByClassName('sale-personal-order-detail-block-shipment-collapse')[0];

				BX.bindDelegate(shipmentWrapper, 'click', { 'class': 'sale-personal-order-detail-block-shipment-expand' }, BX.proxy(function()
				{
					showInformation.style.display = 'none';
					hideInformation.style.display = 'inline-block';
					detailShipmentBlock.style.display = 'block';
				}, this));
				BX.bindDelegate(shipmentWrapper, 'click', { 'class': 'sale-personal-order-detail-block-shipment-collapse' }, BX.proxy(function()
				{
					showInformation.style.display = 'inline-block';
					hideInformation.style.display = 'none';
					detailShipmentBlock.style.display = 'none';
				}, this));
			});

			Array.prototype.forEach.call(listPaymentWrapper, function(paymentWrapper)
			{
				var rowPayment = paymentWrapper.getElementsByClassName('sale-personal-order-detail-block-payment-common')[0];

				BX.bindDelegate(paymentWrapper, 'click', { 'class': 'sale-personal-order-detail-block-payment-switch' }, BX.proxy(function()
				{
					BX.toggleClass(paymentWrapper, 'sale-personal-order-detail-block-payment-active');
				}, this));

				BX.bindDelegate(paymentWrapper, 'click', { 'class': 'sale-personal-order-detail-block-payment-change' }, BX.proxy(function(event)
				{
					event.preventDefault();

					var btn = rowPayment.parentNode.getElementsByClassName('sale-personal-order-detail-block-payment-buttons')[0];
					var linkReturn = rowPayment.parentNode.getElementsByClassName('sale-personal-order-detail-block-payment-cancel')[0];

					BX.ajax(
						{
							method: 'POST',
							dataType: 'html',
							url: params.url,
							data:
							{
								sessid: BX.bitrix_sessid(),
								orderData: params.paymentList[event.target.id],
                                templateName: params.templateName
							},
							onsuccess: BX.proxy(function(result)
							{
								rowPayment.innerHTML = result;
                                BX.removeClass(paymentWrapper, 'sale-personal-order-detail-payment-item-active');
								if (btn)
								{
									btn.parentNode.removeChild(btn);
								}
								linkReturn.style.display = "";
								BX.bind(linkReturn, 'click', function()
								{
									window.location.reload();
								},this);
							},this),
							onfailure: BX.proxy(function()
							{
								return this;
							}, this)
						}, this
					);

				}, this));
			});
		}
	};
})();
