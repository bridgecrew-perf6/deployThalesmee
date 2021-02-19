var include;
jQuery.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
    // DataTables 1.10 compatibility - if 1.10 then `versionCheck` exists.
    // 1.10's API has ajax reloading built in, so we use those abilities
    // directly.
    if ( jQuery.fn.dataTable.versionCheck ) {
        var api = new jQuery.fn.dataTable.Api( oSettings );
 
        if ( sNewSource ) {
            api.ajax.url( sNewSource ).load( fnCallback, !bStandingRedraw );
        }
        else {
            api.ajax.reload( fnCallback, !bStandingRedraw );
        }
        return;
    }
 
    if ( sNewSource !== undefined && sNewSource !== null ) {
        oSettings.sAjaxSource = sNewSource;
    }
 
    // Server-side processing should just call fnDraw
    if ( oSettings.oFeatures.bServerSide ) {
        this.fnDraw();
        return;
    }
 
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];
 
    this.oApi._fnServerParams( oSettings, aData );
 
    oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );
 
        /* Got the data - add it to the table */
        var aData =  (oSettings.sAjaxDataProp !== "") ?
            that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;
 
        for ( var i=0 ; i<aData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, aData[i] );
        }
 
        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
 
        that.fnDraw();
 
        if ( bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.oApi._fnCalculateEnd( oSettings );
            that.fnDraw( false );
        }
 
        that.oApi._fnProcessingDisplay( oSettings, false );
 
        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback !== null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
};

function majStatutInstru(numInstru,table)
{
	
	jQuery.ajax({
		url:"./ajaxStatutMetro.php",
		type:"GET",
		data: 'numInstru='+numInstru,
		
		success: function(data){
			if(data!=0) //si erreur
				alert(data);
			else
			{
				
				table.fnReloadAjax();
			}
		},
		error: function(){
			alert("Erreur ajax");
		}
	});
}

function gererMetro(numInstru,mess,table)
{
	$("#messMetro").html(mess);
	$("#dialog").dialog({ 
		autoOpen: false,
		height: 180,
		width: 360,
		modal: true,
		buttons: {
			"Valider": function() {
				majStatutInstru(numInstru,table);
				$( this ).dialog( "close" );
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	
	$('#dialog').dialog('open');
}

function initListMetroFutCalib(labo)
{
	
	$('#dialog').hide();
	var table=$('#tri').dataTable( {
		"bServerSide": true,
		"sAjaxSource": "../server_side/"+labo+"/servSide_listInstruMetroFutCalib_"+labo+".php",
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			$(nRow).css('cursor', 'pointer');
			
			$(nRow).on('click', function () {
				gererMetro(aData[3],"Voulez-vous passez l'instrument en calibration ?",table);
			});
		}
	} ).columnFilter();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");
}

function initListMetroCalib(labo)
{
	$('#dialog').hide();
	var table=$('#tri').dataTable( {
		"bServerSide": true,
		"sAjaxSource": "../server_side/"+labo+"/servSide_listInstruMetroCalib_"+labo+".php",
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			$(nRow).css('cursor', 'pointer');
			
			$(nRow).on('click', function () {
				gererMetro(aData[3],"La calibration est terminée ?",table);
			});
		}
	} ).columnFilter();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");
}

