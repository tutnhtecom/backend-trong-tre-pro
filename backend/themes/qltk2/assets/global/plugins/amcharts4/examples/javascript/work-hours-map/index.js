am4core.useTheme(am4themes_animated);

var container = am4core.create("chartdiv", am4core.Container); 
container.layout = "vertical";
container.width = am4core.percent(100);
container.height = am4core.percent(100);

var mapChart = container.createChild(am4maps.MapChart);

var button = mapChart.createChild(am4core.Button);
button.align = "right";
button.marginTop = 40;
button.marginRight = 40;
button.valign = "top";
button.label.text = "Show Globe";

button.events.on("hit", function(){
    if(button.label.text == "Show Globe"){
      button.label.text = "Show Map";
      mapChart.projection = new am4maps.projections.Orthographic;     
      
    }
    else{
      button.label.text = "Show Globe";
      mapChart.projection = new am4maps.projections.Miller;  
    }
})

try {
  mapChart.geodata = am4geodata_continentsLow;
}
catch (e) {
  mapChart.raiseCriticalError(new Error("Map geodata could not be loaded. Please download the latest amcharts geodata and extract its contents into the same directory as your amCharts files."));
}

mapChart.fontSize = 11;
mapChart.projection = new am4maps.projections.Miller;
mapChart.panBehavior = "rotateLongLat"
// prevent dragging
mapChart.seriesContainer.draggable = false;
mapChart.seriesContainer.resizable = false;
// prevent zooming
mapChart.minZoomLevel = 1;
// countries
var countriesSeries = mapChart.series.push(new am4maps.MapPolygonSeries());
countriesSeries.useGeodata = true;
countriesSeries.mapPolygons.template.fill = am4core.color("#47c78a");
countriesSeries.mapPolygons.template.stroke = am4core.color("#47c78a");

var colorSet = new am4core.ColorSet();
var polygonTemplate = countriesSeries.mapPolygons.template;

// night series
var nightSeries = mapChart.series.push(new am4maps.MapPolygonSeries());
nightSeries.ignoreBounds = true;
var night = nightSeries.mapPolygons.create();
night.fill = am4core.color("#000000");
night.fillOpacity = 0.35;
night.interactionsEnabled = false;
night.stroke = am4core.color("#000000");
night.strokeOpacity = 0;

/*
var night2 = nightSeries.mapPolygons.create();
night2.fill = am4core.color("#000000");
night2.fillOpacity = 0.4;
night2.interactionsEnabled = false;
night2.stroke = am4core.color("#000000");
night2.strokeOpacity = 0;
*/

// images series
var imagesSeries = mapChart.series.push(new am4maps.MapImageSeries())
var tooltip = imagesSeries.tooltip;
tooltip.label.padding(15, 15, 15, 15);
tooltip.background.cornerRadius = 25;

// sun
var sun = imagesSeries.mapImages.create();
var suncircle = sun.createChild(am4core.Circle);
suncircle.radius = 10;
suncircle.fill = am4core.color("#ffba00");
suncircle.strokeOpacity = 0;
sun.filters.push(new am4core.BlurFilter());

// graticule
var graticuleSeires = mapChart.series.push(new am4maps.GraticuleSeries());
graticuleSeires.mapLines.template.stroke = am4core.color("#ffffff");
graticuleSeires.fitExtent = false;

// add slider to chart container in order not to occupy space
var slider = container.createChild(am4core.Slider);
slider.start = 0.5;
slider.padding(0, 30, 0, 80);
slider.background.padding(0, 30, 0, 80);
slider.marginBottom = 15;
slider.events.on("rangechanged", function () {
  updateDateNight(new Date().getTime() + (slider.start - 0.5) * 1000 * 60 * 60 * 24 * 2 * 2);
})

var lineSeries = mapChart.series.push(new am4maps.MapLineSeries())
lineSeries.mapLines.template.strokeDasharray = "4,4";
lineSeries.mapLines.template.stroke = am4core.color("#ffffff");
var nineLine = lineSeries.mapLines.create();
var fiveLine = lineSeries.mapLines.create();

var nineImage = imagesSeries.mapImages.create();
var nineLabel = nineImage.createChild(am4core.Label);
nineLabel.fill = am4core.color("#ffffff");
nineLabel.text = "~9 AM";
nineLabel.paddingLeft = 10;
nineLabel.verticalCenter = "middle";

var fiveImage = imagesSeries.mapImages.create();
var fiveLabel = fiveImage.createChild(am4core.Label);
fiveLabel.fill = am4core.color("#ffffff");
fiveLabel.text = "~5 PM";
fiveLabel.paddingRight = 10;
fiveLabel.horizontalCenter = "right";
fiveLabel.verticalCenter = "middle";



function updateDateNight(time) {
  var sunPosition = solarPosition(time);
  sun.latitude = sunPosition.latitude;
  sun.longitude = sunPosition.longitude;
  sun.deepInvalidate();

  //night.multiPolygon = am4maps.getCircle(sunPosition.longitude + 180, -sunPosition.latitude, 91);
  //night2.multiPolygon = am4maps.getCircle(sunPosition.longitude + 180, -sunPosition.latitude, 89);

  var nineLongitude = sunPosition.longitude - 15 * 3; // 3 hours from 12 to 9
  var fiveLongitude = sunPosition.longitude + 15 * 5; // 5 hours from 12 to 17

  var max = 89.999;

  var multipolygon = [];
  for(var i = nineLongitude; i > fiveLongitude - 360; i = i - 10){
      var longitude  = i;
      if(longitude > 180){
        longitude -= 360;
      }
      multipolygon.push([[[i - 10, -max], [i - 10, 0], [i - 10, max], [i, max], [i, 0], [i, -max]]]);
  }
  night.multiPolygon = multipolygon;

  nineLine.multiLine = [[[nineLongitude, max],[nineLongitude, -max]]];
  fiveLine.multiLine = [[[fiveLongitude, max],[fiveLongitude, -max]]];

  fiveImage.longitude = fiveLongitude;
  fiveImage.latitude = sun.latitude;

  nineImage.longitude = nineLongitude;
  nineImage.latitude = sun.latitude;  
}


// all sun position calculation is taken from: http://bl.ocks.org/mbostock/4597134
var offset = new Date().getTimezoneOffset() * 60 * 1000;

function solarPosition(time) {
  var centuries = (time - Date.UTC(2000, 0, 1, 12)) / 864e5 / 36525, // since J2000
    longitude = (am4core.time.round(new Date(time), "day", 1).getTime() - time - offset) / 864e5 * 360 - 180;

  return am4maps.geo.normalizePoint({ longitude: longitude - equationOfTime(centuries) * am4core.math.DEGREES, latitude: solarDeclination(centuries) * am4core.math.DEGREES });
};


// Equations based on NOAA’s Solar Calculator; all angles in Amam4charts.math.RADIANS.
// http://www.esrl.noaa.gov/gmd/grad/solcalc/

function equationOfTime(centuries) {
  var e = eccentricityEarthOrbit(centuries),
    m = solarGeometricMeanAnomaly(centuries),
    l = solarGeometricMeanLongitude(centuries),
    y = Math.tan(obliquityCorrection(centuries) / 2);

  y *= y;
  return y * Math.sin(2 * l)
    - 2 * e * Math.sin(m)
    + 4 * e * y * Math.sin(m) * Math.cos(2 * l)
    - 0.5 * y * y * Math.sin(4 * l)
    - 1.25 * e * e * Math.sin(2 * m);
}

function solarDeclination(centuries) {
  return Math.asin(Math.sin(obliquityCorrection(centuries)) * Math.sin(solarApparentLongitude(centuries)));
}

function solarApparentLongitude(centuries) {
  return solarTrueLongitude(centuries) - (0.00569 + 0.00478 * Math.sin((125.04 - 1934.136 * centuries) * am4core.math.RADIANS)) * am4core.math.RADIANS;
}

function solarTrueLongitude(centuries) {
  return solarGeometricMeanLongitude(centuries) + solarEquationOfCenter(centuries);
}

function solarGeometricMeanAnomaly(centuries) {
  return (357.52911 + centuries * (35999.05029 - 0.0001537 * centuries)) * am4core.math.RADIANS;
}

function solarGeometricMeanLongitude(centuries) {
  var l = (280.46646 + centuries * (36000.76983 + centuries * 0.0003032)) % 360;
  return (l < 0 ? l + 360 : l) / 180 * Math.PI;
}

function solarEquationOfCenter(centuries) {
  var m = solarGeometricMeanAnomaly(centuries);
  return (Math.sin(m) * (1.914602 - centuries * (0.004817 + 0.000014 * centuries))
    + Math.sin(m + m) * (0.019993 - 0.000101 * centuries)
    + Math.sin(m + m + m) * 0.000289) * am4core.math.RADIANS;
}

function obliquityCorrection(centuries) {
  return meanObliquityOfEcliptic(centuries) + 0.00256 * Math.cos((125.04 - 1934.136 * centuries) * am4core.math.RADIANS) * am4core.math.RADIANS;
}

function meanObliquityOfEcliptic(centuries) {
  return (23 + (26 + (21.448 - centuries * (46.8150 + centuries * (0.00059 - centuries * 0.001813))) / 60) / 60) * am4core.math.RADIANS;
}

function eccentricityEarthOrbit(centuries) {
  return 0.016708634 - centuries * (0.000042037 + 0.0000001267 * centuries);
}
