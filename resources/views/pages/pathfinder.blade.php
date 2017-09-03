@extends('layouts.app')
@section('title')
	Pathfinder
@endsection
@section('content_header')
	pathfinder
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				Proprietary Variables
			</div>
			<div class="panel-body">
				<div id="input_form" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-3 control-label">Weighted Accts per Team:</label> 
						<span class="col-sm-2">
							<input id="weighted_accts_per_team" type="text" class="form-control"/>
						</span>				
					</div>				
					<div class="form-group">
						<label class="col-sm-3 control-label">Losses per Team per week:</label> 
						<span class="col-sm-2">
							<input id="losses_per_team_per_week" type="text" class="form-control"/>
						</span>				
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Skips per Team per week:</label> 
						<span class="col-sm-2">
							<input id="skips_per_team_per_week" type="text" class="form-control"/>
						</span>				
					</div>
				</div>				
			</div>
			
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				Pricing Levels
			</div>
			<div class="panel-body">
				<div id="input_form" class="form-horizontal">
					Price Level 1: <br>
					<div class="form-group">
						<label class="col-sm-3 control-label">AA:</label> 
						<span class="col-sm-2">
							<input id="sch_1_aa" type="text" class="form-control"/>
						</span>
						<span class="col-sm-3 no-padding">
							<label class="control-label">Price of an AA service item</label> 
						</span>						
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">A:</label> 
						<span class="col-sm-2">
							<input id="sch_1_a" type="text" class="form-control"/>
						</span>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">B:</label> 
						<span class="col-sm-2">
							<input id="sch_1_b" type="text" class="form-control"/>
						</span>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">C:</label> 
						<span class="col-sm-2">
							<input id="sch_1_c" type="text" class="form-control"/>
						</span>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				Projection
			</div>
			<div class="panel-body">
				<div id="input_form" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-3 control-label">Number of weeks:</label> 
						<span class="col-sm-2">
							<input id="number_of_weeks" type="text" class="form-control"/>
						</span>				
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Current Accounts:</label> 
						<span class="col-sm-2">
							<input id="current_weighted_accts" type="text" class="form-control"/>
						</span>				
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">(+/-) accounts:</label> 
						<span class="col-sm-2">
							<input id="account_change" type="text" class="form-control"/>
						</span>				
					</div>
					<button class="btn btn-primary" onclick="drawChart()">Run Algorithm</button>
				</div>				
			</div>
		</div>
	</div>
</div>
<div id="chart_div"></div>
@endsection
@section('popups')

@endsection
@push('scripts')
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

		$("#weighted_accts_per_team").val(50);
		$("#losses_per_team_per_week").val(1);
		$("#skips_per_team_per_week").val(3);

		$("#sch_1_aa").val(99);
		$("#sch_1_a").val(109);
		$("#sch_1_b").val(119);
		$("#sch_1_c").val(129);

		$("#number_of_weeks").val(50);
		$("#current_weighted_accts").val(500);
		$("#account_change").val(50);

		


    	function execute()
    	{
			var ca = parseInt($("#current_weighted_accts").val());
			var at = parseInt($("#weighted_accts_per_team").val());
			var pc = parseInt($("#account_change").val());
    		var t = parseInt($("#number_of_weeks").val());
    		var s = parseInt($("#skips_per_team_per_week").val());
    		var p1 = parseInt($("#sch_1_aa").val());
    		var p2 = parseInt($("#sch_1_a").val());
    		var p3 = parseInt($("#sch_1_b").val());
    		var p4 = parseInt($("#sch_1_c").val());
    		var p = p1+p2+p3+p4;

    		var total = 0;
    		var income_data = [];
    		var change = pc / t; //change per iteration

    		for(i = 0; i < t; i++){

    			ca += change;

		    	var iw = ((ca/2)-(s*(ca/at)))*((p/4)-2.5);

		    	total = total + iw;
		    	income_data.push(iw);
    		}
			
    	}


      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

		var ca = parseInt($("#current_weighted_accts").val());
		var at = parseInt($("#weighted_accts_per_team").val());
		var pc = parseInt($("#account_change").val());
		var t = parseInt($("#number_of_weeks").val());
		var s = parseInt($("#skips_per_team_per_week").val());
		var p1 = parseInt($("#sch_1_aa").val());
		var p2 = parseInt($("#sch_1_a").val());
		var p3 = parseInt($("#sch_1_b").val());
		var p4 = parseInt($("#sch_1_c").val());
		var p = p1+p2+p3+p4;

		var total = 0;
		var rows = [];
		var Δ = pc / t; //change per iteration
		// Create the data table.
    	var data = new google.visualization.DataTable();
		for(i = 0; i < t; i++){
			ca += Δ;
	    	var iw = ((ca/2)-(s*(ca/at)))*((p/4)-2.5);
	    	total += iw;
	    	rows.push([i,iw]);
		}

		var avg = Math.round(total / t);
    	var min = avg + 5000;
    	var max = avg - 5000;
    	var vxCount = (max-min)/1000;	
        
        data.addColumn('number', 'Week');
        data.addColumn('number','Income');
        data.addRows(rows);

        // Set chart options
        var options = {
	        chart: {
	          title: 'Box Office Earnings in First Two Weeks of Opening',
	          subtitle: 'in millions of dollars (USD)'
	        },
			height:500,
	        curveType: 'function',
	        legend: {position: 'bottom'},	
            vAxis: {
            	gridlines: {count: 10},
            	viewWindowMode: 'maximized',
            	viewWindow:{
            		min:min,
            		max:max,
            	},
            },
            hAxis: {
            	gridlines: {count: t}
            }
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

@endpush