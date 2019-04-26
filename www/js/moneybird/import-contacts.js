/**
 * 
 */



$(document).ready(function() {
	
	$('#start-import').click(moneybird_start_import);
	
	
});

var mb_importer = null;
function moneybird_start_import() {
	$('.intro-message').remove();
	
	$('.ajax-update').append('<img class="loading-symbol" src="'+appSettings.base_href+'images/ajax-loader-big.gif'+'" />');
	
	mb_importer = new MoneybirdImporter();
	mb_importer.startImport();
}


function MoneybirdImporter() {
	
	this.pageNo = 1;
	this.importCount = 0;
	this.modifiedCount = 0;
	
	
	this.startImport = function() {
		$('.ajax-update').append('<div class="" style="margin-top: 1.5em;">Bezig met het synchroniseren van contacten... <span class="counter">0</span> geïmporteerd</div>');
		
		this.next();
	};
	
	this.next = function() {
		
		
		$.ajax({
			url: appUrl('/?m=moneybird&c=importContacts'),
			type: 'POST',
			data: {
				a: 'import',
				pageNo: this.pageNo
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					if (data.importCount > 0) {
						this.importCount += data.importCount;
						this.modifiedCount += data.modifiedCount;
						
						this.pageNo++;
						
						$('.ajax-update .counter').text( this.importCount );
						
						this.next();
					} else {
						$('.ajax-update').text( 'Importeren voltooid. Aantal gesynchroniseerde contacten: ' + this.importCount + ', aantal gewijzigd: ' + this.modifiedCount );
					}
				} else if (data.error) {
					showAlert('Fout', 'Er is een fout opgetreden: ' + data.error);
					
					$('.ajax-update').text('Import gestopt, fout opgetreden: ' + data.error);
					return;
				} else {
					showAlert('Fout', 'Er is een fout opgetreden, import gestopt');
					
					$('.ajax-update').text('Import gestopt, fout opgetreden: ' + data);
				}
				
			}.bind(this)
		});
		
	};
	
}


