var nbCertif = 1;

function ajouterDuplication() {
	$("#duplication").append('\
		<div class="jumbotron">\
			<div class="row">\
				<div class="col-md-4">\
					<div class="info"><label>Numéro de l\'instrument </label><input id="num" name="num[]" title="Numéro de l\'instrument" type="text" class="form-control" placeholder="Numéro de l\'instrument" required autofocus /></div>\
				</div>\
				<div class="col-md-4">\
					<div class="info"><label>Numéro de série</label><input id="serie" name="serie[]" title="Numéro de série" type="text" class="form-control" placeholder="Numéro de série" required /></div>\
				</div>\
				<div class="col-md-4">\
					<div class="info"><label>Certificat d\'étalonnage : </label><input title="Certificat d\'étalonnage"  class="form-control certif" style="height:auto;" type="file" name="certif'+nbCertif+'"/></div>\
				</div>\
			</div>\
			<div class="text-center"><div class="btn btn-danger btn-lg" onclick="supprimer(this)">Supprimer</div></div>\
		</div>');
	nbCertif ++;
}

function supprimer(elt){
	nbCertif = 0;
	$(elt).parent().parent().remove();
	$(".certif").each(function(){
		$(this).attr("name", "certif"+nbCertif);
		nbCertif++;
	})
}