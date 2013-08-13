google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
  jQuery('.google-graph').each(function(){
    console.log( jQuery(this).data('measures'));
  });
  var data = google.visualization.arrayToDataTable(
    baby_measures_data.data
  );

  var options = {
    title: baby_measures_data.title
  };

  var chart = new google.visualization.LineChart(document.getElementById( baby_measures_data.id));
  chart.draw(data, options);
}