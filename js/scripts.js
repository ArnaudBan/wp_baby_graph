google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
  jQuery('.abwbg-google-graph').each(function(){

    var $slug =  jQuery(this).data('measures');

    // Date format
    var measures = baby_measures_data[$slug].data;
    var date;
    for( var key in measures ){
      date = measures[key][0].split(',');
      measures[key][0] =  new Date( date[0], ( date[1] - 1 ), date[2] );
    }

    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn('date', baby_measures_data.date);
    dataTable.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});

    var lines = baby_measures_data[$slug].lines_info;
    var colors = [];

    for( var i in lines ){
      dataTable.addColumn('number', lines[i].name);
      colors.push( lines[i].color );
    }
    dataTable.addRows( measures );

    var data = new google.visualization.DataView(dataTable);

    var options = {
      title: baby_measures_data[$slug].title,
      pointSize: 5,
      curveType: "function",
      focusTarget: 'category',
      colors: colors,
      tooltip: {isHtml: true},
      vAxis: {
        format:'#,## ' + baby_measures_data[$slug].unit
      }
    };

    var chart = new google.visualization.LineChart(jQuery(this)[0]);
    chart.draw(data, options);
  });
}