<div class="panel">
	<div class = "panel-heading"><i class="icon icon-archive"></i> Boxes (Units: {$units})</div>
	<div class = "panel-body">
    	{$boxes}
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
