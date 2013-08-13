google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
  var data = google.visualization.arrayToDataTable(
    baby_measures_data.data
  );

  var options = {
    title: baby_measures_data.title
  };

  var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
  chart.draw(data, options);
}