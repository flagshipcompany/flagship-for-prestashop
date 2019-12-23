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

<div class="panel">
	<div class = "panel-heading"><i class="icon icon-archive"></i> Boxes (Units: {$units|escape:'htmlall':'UTF-8'})</div>
	<div class = "panel-body">
    	{$boxes|escape:'htmlall':'UTF-8'}
    </div>
</div>
<script>
$(document).ready(function(){
	var url = "{$module_dir|escape:'htmlall':'UTF-8'}deleteBox.php";
	$(".delete").click(function(){
	var id = $(this).parent().attr("id");

			$.ajax({
				url : url,
				type : 'POST',
				data : {
					box_id : id
				},

				success : function(response){
				  	$(".panel-body").html(response);
				}
			});

	});

});
</script>
