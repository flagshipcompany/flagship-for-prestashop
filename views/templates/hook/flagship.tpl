{*
* FlagShip Courier Solutions
*
* NOTICE OF LICENSE
*
* This source file is subject to The MIT License
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@flagshipcompany.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author FlagShip Courier Solutions <support@flagshipcompany.com>
*  @copyright  FlagShip Courier Solutions
*  @license    https://opensource.org/licenses/MIT
*  
*}
<script type="text/javascript">
	var orderId = "{$orderId|escape:'htmlall':'UTF-8'}";
	var shipmentId = "{$shipmentFlag|escape:'htmlall':'UTF-8'}";
</script>

{if $shipmentFlag}
	<a class="btn btn-default send_to_flagship" id="update_shipment">Update Shipment</a>
	<span class="success">FlagShip Shipment: </span><a href="{$SMARTSHIP_WEB_URL|escape:'htmlall':'UTF-8'}/shipping/{$shipmentFlag|escape:'htmlall':'UTF-8'}/convert" target="_blank" class="shipmentLink">{$shipmentFlag}</a>
{else}
	<a href="#" class="btn btn-default send_to_flagship" id="send_to_flagship"><i class="icon-truck"></i>Send To FlagShip</a>
{/if}
<div class="response"><img src="{$base_url}img/loader.gif" alt="Loading..."id="loading-image"/>
</div>
<script>
	
	var url = "{$module_dir|escape:'htmlall':'UTF-8'}shipping.php";
	var action = 'prepare';
	var msg = 'FlagShip Shipment: ';

	$(document).ajaxStart(function(){
		$("#loading-image").show();
	});
	$(document).ajaxStop(function(){
		$("#loading-image").hide();
	});

	$(document).ready(function(){
		$("#loading-image").hide();
		$(".send_to_flagship").click(function(e){
			if($(this).attr('id') == 'update_shipment'){
				action = 'update';
			}
	
			e.preventDefault();
			
			$.ajax({
			  url : url,
			  type : 'POST',
			  data : {
			  	order_id : orderId,
			  	shipment_id : shipmentId,
			  	action : action
			  },
			  	
			  success : function(response){
			  	$(".response").html(response);
			  	$("#send_to_flagship").hide();
			  	return 0;
			  }
			});
		});
	});
</script>