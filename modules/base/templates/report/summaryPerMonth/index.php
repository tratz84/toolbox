
<script src="<?= BASE_HREF ?>lib/chart.js/Chart.min.js"></script>


<h1>Maandoverzichten</h1>

<br/>

<div>
    Periode
    <select name="start">
    	<?php for($x=0; $x < count($periods); $x++) : ?>
    	<option value="<?= esc_attr($periods[$x]['month']) ?>" <?= ($x==12?'selected=selected':'') ?>><?= esc_html($periods[$x]['label']) ?></option>
    	<?php endfor; ?>
    </select>
    
    <select name="end">
    	<?php for($x=0; $x < count($periods); $x++) : ?>
    	<option value="<?= esc_attr($periods[$x]['month']) ?>"><?= esc_html($periods[$x]['label']) ?></option>
    	<?php endfor; ?>
    </select>
</div>

<br/>

<div>
    <div style="width: 350px; float: left;">
    	<h3>Bron 1</h3>
    	<ul>
    		<li><label><input type="radio" name="ds1" value="" checked=checked /> Geen</label></li>
    		<?php foreach($datasources as $ds) : ?>
    		<li>
    			<label>
        			<input type="radio" name="ds1" value="<?= esc_attr($ds['url']) ?>" />
        			<?= esc_html($ds['label']) ?>
    			</label>
    		</li>
    		<?php endforeach; ?>
    	</ul>
    </div>
    
    <div>
    	<h3>Bron 2</h3>
    	<ul>
    		<li><label><input type="radio" name="ds2" value="" checked=checked /> Geen</label></li>
    		<?php foreach($datasources as $ds) : ?>
    		<li>
    			<label>
        			<input type="radio" name="ds2" value="<?= esc_attr($ds['url']) ?>" />
        			<?= esc_html($ds['label']) ?>
    			</label>
    		</li>
    		<?php endforeach; ?>
    	</ul>
    </div>
    
    <div class="clear"></div>
</div>

<div style="width: 800px; height: 400px;">
	<canvas id="chart-container" ></canvas>
</div>


<script>

$(document).ready(function() {
	$('[name=start], [name=end], [name=ds1], [name=ds2]').change(function() {
		updateGraph();
	});
});


var currentDataFetcher = null;
function updateGraph() {
	var start = $('[name=start]').val();
	var end   = $('[name=end]').val();

	var ymStart = parseInt( start.replace(/-/, '') );
	var ymEnd = parseInt( end.replace(/-/, '') );
	
	if (ymStart > ymEnd) {
		showAlert('Error', 'Invalid start/end period given');
		return;
	}
	
	var ds1 = $('[name=ds1]:checked').val();
	var ds2 = $('[name=ds2]:checked').val();
	
	// TODO: fetch data
	if (currentDataFetcher) {
		currentDataFetcher.abort();
	}

	currentDataFetcher = new DataFetcher();
	if (ds1 && ds1 != '') {
		currentDataFetcher.addUrl( appUrl(ds1) + '&start=' + start + '&end=' + end );
	}
	if (ds2 && ds2 != '') {
		currentDataFetcher.addUrl( appUrl(ds2) + '&start=' + start + '&end=' + end );
	}

	currentDataFetcher.setCallbackFinish(function() {
		renderGraph( this.responses );
	});

	currentDataFetcher.fetch();
}


var currentChart = null;
function renderGraph(datasets) {
	var c = document.getElementById('chart-container');
	var ctx = c.getContext('2d');

	// determine labels
	var labels = [];
	for(var x=0; x < datasets[0]['data'].length; x++) {
		labels.push( datasets[0]['data'][x]['month'] );
	}

	// build datasets
	var chart_datasets = [];
	for(var x=0; x < datasets.length; x++) {
		var ds = datasets[x];
		var chart_ds = {};
		
		chart_ds.label = ds['label'] ? ds['label'] : 'DS: ' + (x+1)
		chart_ds.data = [];
		chart_ds.yAxisID = 'y-axis-' + (x+1);
		chart_ds.fill = false;
		for(var y=0; y < ds['data'].length; y++) {
			chart_ds.data.push( ds['data'][y]['amount'] );
		}

		if (x == 0) {
			chart_ds.borderColor = '#ff6e6e';
			chart_ds.backgroundColor = '#ff6e6e';
		}
		else if (x == 1) {
			chart_ds.borderColor = '#3880ff';
			chart_ds.backgroundColor = '#3880ff';
		}
		
		chart_datasets.push( chart_ds );
	}
	console.log(chart_datasets);


	if (currentChart != null) {
		currentChart.destroy();
	}
	
	currentChart = new Chart(ctx, {
	    type: 'line',
	    data: {
	        datasets: chart_datasets,
	        labels: labels
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    suggestedMin: 50,
	                    suggestedMax: 100
	                }
	            }]
	        },
			scales: {
				yAxes: [{
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'left',
					id: 'y-axis-1',
				}, {
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'right',
					id: 'y-axis-2',

					// grid line settings
					gridLines: {
						drawOnChartArea: false, // only want the grid lines for one axis to show up
					},
				}],
			}
	    }
	});
		
}




function DataFetcher() {

	var urls = [];

	this.ajx = null;
	
	this.loadUrlPos = 0;
	this.responses = [];
	this.callbackFinish = null;
	this.isAborted = false;

	this.addUrl = function(url) { urls.push( url ); }; 

	this.setCallbackFinish = function(func) { this.callbackFinish = func; }
	this.abort = function() {
		this.isAborted = true;
		this.ajx.abort();
	};

	this.fetch = function() {
		console.log('gogo => ' + urls[ this.loadUrlPos ]);
		
		this.ajx = $.ajax({
			type: 'POST',
			url: urls[ this.loadUrlPos ],
			success: function(data, textStatus, xhr) {
				// request aborted?
				if (this.isAborted)
					return;
				
				// save response
				this.responses.push( data );
				
				// load next url?
				this.loadUrlPos++;
				if (this.loadUrlPos < urls.length) {
					this.fetch();
				}
				// done
				else {
					this.callbackFinish.bind(this)();
				}
			}.bind(this),
			error: function(xhr, textStatus, errorThrown) {
				alert('Error occured: ' + xhr.responseText);
			}
		});
		
		
	};

	
}




</script>





