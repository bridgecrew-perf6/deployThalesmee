$('#typeC').change(function(){
	
	$('#axeX').val("");
	$('#sensiX').val("");
	$('#axeY').val("");
	$('#sensiY').val("");
	$('#axeZ').val("");
	$('#sensiZ').val("");
	$('#axeZs').val("");
	$('#sensiZs').val("");
	$('.cm').val("");
	$('#bobine').val("");
	update();
});
	
function disable (){
	
	
	$(".dis").attr("disabled", true);
}

function update (){
	
	if ($('#typeC option:selected').text() == "VIB Mono Axe" || $('#typeC option:selected').text()== "Couples"|| $('#typeC option:selected').text()== "Pilote"|| $('#typeC option:selected').text()== "Cellule force mono axe"|| $('#typeC option:selected').text()== "Microphonie" || $('#typeC option:selected').text()== "Choc mono axe"){
		
		console.log("Cellule");
		$('#axeX').show();
		$('#sensiX').show();
		$('#axeY').hide();
		$('#sensiY').hide();
		$('#axeZ').hide();
		$('#sensiZ').hide();
		$('#axeZs').hide();
		$('#sensiZs').hide();
		$('#bobine').hide();
		$('#type').hide();
		$("#axeX").attr('required', '');
		$("#sensiX").attr('required', '');
		$("#typeC").attr('required', '');
		$("#unite").attr('required', '');
		$("#dSensi").attr('required', '');
		$("#type").removeAttr('required');
		$('.cm').hide();
		$(".cm").removeAttr('required');
		
	}else if (($('#typeC option:selected').text() == "Bobine_Courant") || ($('#typeC option:selected').text() == "Bobine_Tension")){
		
		$('#bobine').show();
		$('#type').show();
		$('#axeX').hide();
		$('#sensiX').hide();
		$('#axeY').hide();
		$('#sensiY').hide();
		$('#axeZ').hide();
		$('#sensiZ').hide();
		$('#axeZs').hide();
		$('#sensiZs').hide();
		$("#type").attr('required', '');
		$("#axeX").removeAttr('required');
		$("#sensiX").removeAttr('required');
		$("#typeC").removeAttr('required');
		$("#unite").removeAttr('required');
		$("#dSensi").removeAttr('required');
		$("#axeY").removeAttr('required');
		$("#sensiY").removeAttr('required');
		$("#axeZ").removeAttr('required');
		$("#sensiZ").removeAttr('required');
		$('.cm').hide();
		$(".cm").removeAttr('required');


	}else if (($('#typeC option:selected').text() == "Couplemètre")){
			
		$('#axeX').hide();
		$('#sensiX').hide();
		$('#axeY').hide();
		$('#sensiY').hide();
		$('#axeZ').hide();
		$('#sensiZ').hide();
		$('#axeZs').hide();
		$('#sensiZs').hide();
		$('#bobine').hide();
		$('#type').hide();
		$("#axeX").removeAttr('required', '');
		$("#sensiX").removeAttr('required');
		$("#typeC").removeAttr('required');
		$("#unite").attr('required', '');
		$("#dSensi").removeAttr('required');
		$("#axeY").removeAttr('required');
		$("#sensiY").removeAttr('required');
		$("#axeZ").removeAttr('required');
		$("#sensiZ").removeAttr('required');
		$("#type").removeAttr('required');
		$('.cm').show();
		$(".cm").attr('required', '');
		
	}else{

		$('#axeX').show();
		$('#sensiX').show();
		$('#axeY').show();
		$('#sensiY').show();
		$('#axeZ').show();
		$('#sensiZ').show();
		$('#axeZs').show();
		$('#sensiZs').show();
		$('#bobine').hide();
		$('#type').hide();
		$("#axeX").attr('required', '');
		$("#sensiX").attr('required', '');
		$("#typeC").attr('required', '');
		$("#unite").attr('required', '');
		$("#dSensi").attr('required', '');
		$("#type").removeAttr('required');
		$('.cm').hide();
		$(".cm").removeAttr('required');
	}
}

