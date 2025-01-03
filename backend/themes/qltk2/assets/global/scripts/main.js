/**
 * TRÔNG TRẺ PRO by HungLuongHien on 6/23/2016.
 */

function getMessage(str) {
    return str.replace('Internal Server Error (#500):', '');
}
function getError(messenge){
        toastr.warning(messenge);
        toastr.options.closeDuration=500;
    return false;
}
function pieChartBrokenSlices($obj) {
    am4core.ready(function () {

// Themes begin
        am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
        var chart = am4core.create($obj, am4charts.PieChart);

// Set data
        var selected;
        var types = [
            {
                type: "Fossil Energy",
                percent: 70,
                color: chart.colors.getIndex(0),
                subs: [
                    {
                        type: "Oil",
                        percent: 15
                    },
                    {
                        type: "Coal",
                        percent: 35
                    },
                    {
                        type: "Nuclear",
                        percent: 20
                    }
                ]
            },
            {
                type: "Green Energy",
                percent: 30,
                color: chart.colors.getIndex(1),
                subs: [
                    {
                        type: "Hydro",
                        percent: 15
                    },
                    {
                        type: "Wind",
                        percent: 10
                    },
                    {
                        type: "Other",
                        percent: 5
                    }
                ]
            }];

// Add data
        chart.data = generateChartData();

// Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "percent";
        pieSeries.dataFields.category = "type";
        pieSeries.slices.template.propertyFields.fill = "color";
        pieSeries.slices.template.propertyFields.isActive = "pulled";
        pieSeries.slices.template.strokeWidth = 0;

        function generateChartData() {
            var chartData = [];
            for (var i = 0; i < types.length; i++) {
                if (i == selected) {
                    for (var x = 0; x < types[i].subs.length; x++) {
                        chartData.push({
                            type: types[i].subs[x].type,
                            percent: types[i].subs[x].percent,
                            color: types[i].color,
                            pulled: true
                        });
                    }
                } else {
                    chartData.push({
                        type: types[i].type,
                        percent: types[i].percent,
                        color: types[i].color,
                        id: i
                    });
                }
            }
            return chartData;
        }

        pieSeries.slices.template.events.on("hit", function (event) {
            if (event.target.dataItem.dataContext.id != undefined) {
                selected = event.target.dataItem.dataContext.id;
            } else {
                selected = undefined;
            }
            chart.data = generateChartData();
        });

    }); // end am4core.ready()
}

function portAjax(controller_action, dataInput) {
    $.ajax({
        url: 'index.php?r=' + controller_action,
        data: dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            Metronic.blockUI();
        },
        success: function (data) {
            if (data.status == 0) {
                toastr.warning(data.content);
                toastr.options.closeDuration = 500;
                return false;
            } else {
                toastr.success(data.content);
                toastr.options.closeDuration = 500;
                if ($("#crud-datatable-pjax").val() !== undefined) {
                    $.pjax.reload({container: "#crud-datatable-pjax"})
                }
            }

        },
        complete: function () {
            Metronic.unblockUI();
        },
        error: function (r1, r2) {
            $.alert(r1.responseText)
        }
    })
}

function portAjax1(controller_action, dataInput, callSuccess = () => {
}) {
    $.ajax({
        url: 'index.php?r=' + controller_action,
        data: dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            Metronic.blockUI();
        },
        success: function (data) {
            if (data.status == 0) {
                toastr.warning(data.content);
                toastr.options.closeDuration = 500;
                return false;
            } else {
                if (data.content !== undefined) {
                    {
                        toastr.success(data.content);
                        toastr.options.closeDuration = 500;
                    }
                }
                callSuccess(data);
            }

        },
        complete: function () {
            Metronic.unblockUI();
        },
        error: function (r1, r2) {
            $.alert(r1.responseText)
        }
    })
}

function portAjax2(controller_action, dataInput, callSuccess = () => {
}) {
    $.ajax({
        url: 'index.php?r=' + controller_action,
        data: dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            Metronic.blockUI();
            $("#modal-id").css('z-index', 1);

        },
        success: function (data) {
            if (data.content !== undefined) {
                {
                    toastr.success(data.content);
                    toastr.options.closeDuration = 500;
                }
            }
            callSuccess(data);
        },
        complete: function () {
            Metronic.unblockUI();
            $("#modal-id").css('z-index', 10050);
        },
        error: function (r1, r2) {
            toastr.warning(getMessage(r1.responseText));
            toastr.options.closeDuration = 500;
        }
    })
}
function portAjax3(controller_action, dataInput, callSuccess = () => {
}) {
    $.ajax({
        url: 'index.php?r=' + controller_action,
        data: dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            Metronic.blockUI();
            $("#modal-id").css('z-index', 1);

        },
        success: function (data) {
            callSuccess(data);
        },
        complete: function () {
            Metronic.unblockUI();
            $("#modal-id").css('z-index', 10050);
        },
        error: function (r1, r2) {
            toastr.warning(getMessage(r1.responseText));
            toastr.options.closeDuration = 500;
        }
    })
}

function createTypeHead(target, action, callbackAfterSelect) {
    $(target).typeahead({
        source: function (query, process) {
            var states = [];
            return $.get('index.php?r=autocomplete/' + action, {query: query}, function (data) {
                $.each(data, function (i, state) {
                    states.push(state.name);
                });
                return process(states);
            }, 'json');
        },
        afterSelect: function (item) {
            if (typeof callbackAfterSelect != 'undefined')
                callbackAfterSelect(item);
            /*$.ajax({
             url: 'index.php?r=khachhang/getdiachi',
             data: {name: item},
             type: 'post',1
             dataType: 'json',
             success: function (data) {
             $("#diachikhachhang").val(data);
             }
             })*/
        }
    });
}

function setDatePicker() {
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy',
        language: 'vi',
        todayBtn: true,
        todayHighlight: true,
    });
}

function uniqId() {
    return Math.round(new Date().getTime() + (Math.random() * 100));
}

function chartHightLight(obj, make_series, content, from, to, color) {
    am5.ready(function () {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new(obj);


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
            am5themes_Animated.new(root)
        ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            maxTooltipDistance: 0,
            pinchZoomX: true
        }));

        function generateDatas($data) {
            var data = [];
            $day = new Date(from);
            while ($day.getTime() <= new Date(to)) {
                $day.setHours(0, 0, 0, 0);
                data.push({
                    date: $day.getTime(),
                    value: $data[$day.toISOString().split('T')[0]] == undefined ? 0 : parseInt($data[$day.toISOString().split('T')[0]])
                });
                am5.time.add($day, "day", 1);
            }
            return data;
        }


// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
        var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
            maxDeviation: 0.2,
            baseInterval: {
                timeUnit: "day",
                count: 1
            },
            renderer: am5xy.AxisRendererX.new(root, {}),
            tooltip: am5.Tooltip.new(root, {})
        }));

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, {})
        }));


// Add series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
        $.each(make_series, function (category, varr) {
            var series = chart.series.push(am5xy.LineSeries.new(root, {
                name: category,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                legendValueText: "{valueY}",
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: category + ": {valueY}"
                }),
                stroke: am5.color(color[varr])
            }));

            date = new Date();
            date.setHours(0, 0, 0, 0);
            value = 0;

            var data = generateDatas(content[varr]);
            series.data.setAll(data);
            series.strokes.template.setAll({
                strokeWidth: 2,
            });

            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            series.appear();

        });


// Add cursor
// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
            behavior: "none"
        }));
        cursor.lineY.set("visible", false);


// Add scrollbar
// https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
        chart.set("scrollbarX", am5.Scrollbar.new(root, {
            orientation: "horizontal"
        }));

        chart.set("scrollbarY", am5.Scrollbar.new(root, {
            orientation: "vertical"
        }));


// Add legend
// https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
        var legend = chart.rightAxesContainer.children.push(am5.Legend.new(root, {
            width: 200,
            paddingLeft: 15,
            height: am5.percent(100)
        }));

// When legend item container is hovered, dim all the series except the hovered one
        legend.itemContainers.template.events.on("pointerover", function (e) {
            var itemContainer = e.target;

            // As series list is data of a legend, dataContext is series
            var series = itemContainer.dataItem.dataContext;

            chart.series.each(function (chartSeries) {
                if (chartSeries != series) {
                    chartSeries.strokes.template.setAll({
                        strokeOpacity: 0.15,
                    });
                } else {
                    chartSeries.strokes.template.setAll({
                        strokeWidth: 3
                    });
                }
            })
        })

// When legend item container is unhovered, make all series as they are
        legend.itemContainers.template.events.on("pointerout", function (e) {
            var itemContainer = e.target;
            var series = itemContainer.dataItem.dataContext;

            chart.series.each(function (chartSeries) {
                chartSeries.strokes.template.setAll({
                    strokeOpacity: 1,
                    strokeWidth: 2,
                });
            });
        })

        legend.itemContainers.template.set("width", am5.p100);
        legend.valueLabels.template.setAll({
            width: am5.p100,
            textAlign: "right"
        });

// It's is important to set legend data after all the events are set on template, otherwise events won't be copied
        legend.data.setAll(chart.series.values);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
        chart.appear(1000, 100);

    }); // end am5.ready()
}

function SaveObjectUploadFile($url_controller_action, $dataInput, callbackSuccess, columnClass) {
    if (typeof columnClass == "undefined")
        columnClass = 's';
    // data = new FormData($(modalForm)[0]);
    $.dialog({
        columnClass: columnClass,
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=' + $url_controller_action,
                type: 'post',
                dataType: 'json',
                data: $dataInput,
                // async: false,
                contentType: false,
                // cache: false,
                processData: false
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                return false;
            });
        }
    })
}
function SaveObjectUploadFile1($url_controller_action, $dataInput, callbackSuccess, columnClass) {
    if (typeof columnClass == "undefined")
        columnClass = 's';
    // data = new FormData($(modalForm)[0]);
    $.dialog({
        columnClass: columnClass,
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=' + $url_controller_action,
                type: 'post',
                dataType: 'json',
                data: $dataInput,
                // async: false,
                contentType: false,
                // cache: false,
                processData: false
            }).success(function (data) {
                toastr.success(data.content);
                toastr.options.closeDuration = 500;
                callbackSuccess(data);
            }).error(function (r1, r2) {
                toastr.success(getMessage(r1.responseText));
                toastr.options.closeDuration = 500;
                return false;
            });
        }
    })
}

function chartClusteredColumnChart(obj, content, label, make_series) {
    am5.ready(function () {

        // Create root element
        // https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new(obj);


        // Set themes
        // https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
            am5themes_Animated.new(root)
        ]);


        // Create chart
        // https://www.amcharts.com/docs/v5/charts/xy-chart/
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "zoomX",
            layout: root.verticalLayout
        }));


        // Add legend
        // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
        var legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            })
        );

        var data = content


        // Create axes
        // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: label,
            renderer: am5xy.AxisRendererX.new(root, {
                cellStartLocation: 0.1,
                cellEndLocation: 0.9
            }),
            tooltip: am5.Tooltip.new(root, {})
        }));

        xAxis.data.setAll(data);

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, {})
        }));


        // Add series
        // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
        function makeSeries(name, fieldName) {
            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: name,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: fieldName,
                categoryXField: label
            }));

            series.columns.template.setAll({
                tooltipText: "{name}, {categoryX}:{valueY}",
                width: am5.percent(90),
                tooltipY: 0
            });

            series.data.setAll(data);

            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            series.appear();

            series.bullets.push(function () {
                return am5.Bullet.new(root, {
                    locationY: 0,
                    sprite: am5.Label.new(root, {
                        text: "{valueY}",
                        fill: root.interfaceColors.get("alternativeText"),
                        centerY: 0,
                        centerX: am5.p50,
                        populateText: true
                    })
                });
            });

            legend.data.push(series);
        }

        $.each(make_series, function (category, varr) {
            makeSeries(category, varr);
        });


        // Make stuff animate on load
        // https://www.amcharts.com/docs/v5/concepts/animations/
        chart.appear(1000, 100);

    }); // end am5.ready()
}

function chartBarV4(obj, content, category, value) {
    am5.ready(function () {

        // Create root element
        // https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new(obj);


        // Set themes
        // https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
            am5themes_Animated.new(root)
        ]);


        // Create chart
        // https://www.amcharts.com/docs/v5/charts/xy-chart/
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true
        }));

        // Add cursor
        // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
        cursor.lineY.set("visible", false);


        // Create axes
        // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
        var xRenderer = am5xy.AxisRendererX.new(root, {minGridDistance: 30});
        xRenderer.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            maxDeviation: 0.3,
            categoryField: category,
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {})
        }));

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root, {})
        }));


        // Create series
        // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: "Series 1",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: value,
            sequencedInterpolation: true,
            categoryXField: category,
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY}"
            })
        }));

        series.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5});
        series.columns.template.adapters.add("fill", function (fill, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });

        series.columns.template.adapters.add("stroke", function (stroke, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });

        series.columns.template.events.on("click", function (ev) {
            console.log("clicked on ", ev.target);
        }, this);
        // Set data
        var data = content;

        xAxis.data.setAll(data);
        series.data.setAll(data);


        // Make stuff animate on load
        // https://www.amcharts.com/docs/v5/concepts/animations/
        series.appear(1000);
        chart.appear(1000, 100);

    }); // end am5.ready()
}

function chartRealTimeDataSorting(obj, content, label, value, onClick = () => {
}) {
    am5.ready(function () {

        // Create root element
        // https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new(obj);


        // Set themes
        // https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
            am5themes_Animated.new(root)
        ]);


        // Create chart
        // https://www.amcharts.com/docs/v5/charts/xy-chart/
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "none",
            wheelY: "none"
        }));

        // We don't want zoom-out button to appear while animating, so we hide it
        chart.zoomOutButton.set("forceHidden", true);


        // Create axes
        // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
        var xRenderer = am5xy.AxisRendererX.new(root, {
            minGridDistance: 30
        });
        xRenderer.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: 0,
            paddingRight: 30
        });
        xRenderer.grid.template.set("visible", false);

        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            maxDeviation: 0.3,
            categoryField: label,
            renderer: xRenderer
        }));

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            maxDeviation: 0.3,
            min: 0,
            renderer: am5xy.AxisRendererY.new(root, {})
        }));


        // Add series
        // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: "Series 1",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: value,
            categoryXField: label
        }));

        // Rounded corners for columns
        series.columns.template.setAll({
            cornerRadiusTL: 5,
            cornerRadiusTR: 5
        });

        // Make each column to be of a different color
        series.columns.template.adapters.add("fill", function (fill, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });

        series.columns.template.adapters.add("stroke", function (stroke, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });
        series.columns.template.events.on("click", function (ev) {
            onClick(ev.target.dataItem.dataContext)
        }, this);
        // Add Label bullet
        series.bullets.push(function () {
            return am5.Bullet.new(root, {
                locationY: 1,
                sprite: am5.Label.new(root, {
                    text: "{valueYWorking.formatNumber('#.')}",
                    fill: root.interfaceColors.get("alternativeText"),
                    centerY: 0,
                    centerX: am5.p50,
                    populateText: true
                })
            });
        });


        // Set data
        var data = content

        xAxis.data.setAll(data);
        series.data.setAll(data);

        // update data with random values each 1.5 sec
        setInterval(function () {
            updateData();
        }, 1500)

        function updateData() {
            am5.array.each(series.dataItems, function (dataItem) {
                // var value = dataItem.get("valueY") + Math.round(Math.random() * 300 - 150);
                // if (value < 0) {
                //     value = 10;
                // }
                // // both valueY and workingValueY should be changed, we only animate workingValueY
                // dataItem.set("valueY", value);
                // dataItem.animate({
                //     key: "valueYWorking",
                //     to: value,
                //     duration: 600,
                //     easing: am5.ease.out(am5.ease.cubic)
                // });
            })

            sortCategoryAxis();
        }


        // Get series item by category
        function getSeriesItem(category) {
            for (var i = 0; i < series.dataItems.length; i++) {
                var dataItem = series.dataItems[i];
                if (dataItem.get("categoryX") == category) {
                    return dataItem;
                }
            }
        }


        // Axis sorting
        function sortCategoryAxis() {

            // Sort by value
            series.dataItems.sort(function (x, y) {
                return y.get("valueY") - x.get("valueY"); // descending
                //return y.get("valueY") - x.get("valueY"); // ascending
            })

            // Go through each axis item
            am5.array.each(xAxis.dataItems, function (dataItem) {
                // get corresponding series item
                var seriesDataItem = getSeriesItem(dataItem.get("category"));

                if (seriesDataItem) {
                    // get index of series data item
                    var index = series.dataItems.indexOf(seriesDataItem);
                    // calculate delta position
                    var deltaPosition = (index - dataItem.get("index", 0)) / series.dataItems.length;
                    // set index to be the same as series data item index
                    dataItem.set("index", index);
                    // set deltaPosition instanlty
                    dataItem.set("deltaPosition", -deltaPosition);
                    // animate delta position to 0
                    dataItem.animate({
                        key: "deltaPosition",
                        to: 0,
                        duration: 1000,
                        easing: am5.ease.out(am5.ease.cubic)
                    })
                }
            });

            // Sort axis items by index.
            // This changes the order instantly, but as deltaPosition is set,
            // they keep in the same places and then animate to true positions.
            xAxis.dataItems.sort(function (x, y) {
                return x.get("index") - y.get("index");
            });
        }


        // Make stuff animate on load
        // https://www.amcharts.com/docs/v5/concepts/animations/
        series.appear(1000);
        chart.appear(1000, 100);

    }); // end am5.ready()
}

function chartStackedColumnChart(obj, content, label, header, onClick = () => {
}) {
    am5.ready(function () {

        // Create root element
        // https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new(obj);


        // Set themes
        // https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
            am5themes_Animated.new(root)
        ]);


        // Create chart
        // https://www.amcharts.com/docs/v5/charts/xy-chart/
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "zoomX",
            layout: root.verticalLayout
        }));

        // Add scrollbar
        // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
        chart.set("scrollbarX", am5.Scrollbar.new(root, {
            orientation: "horizontal"
        }));

        var data = content

        // Create axes
        // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: header,
            renderer: am5xy.AxisRendererX.new(root, {}),
            tooltip: am5.Tooltip.new(root, {})
        }));

        xAxis.data.setAll(data);

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            min: 0,
            renderer: am5xy.AxisRendererY.new(root, {})
        }));


        // Add legend
        // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
        var legend = chart.children.push(am5.Legend.new(root, {
            centerX: am5.p50,
            x: am5.p50
        }));


        // Add series
        // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
        function makeSeries(name, fieldName) {
            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: name,
                stacked: true,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: fieldName,
                categoryXField: header
            }));

            series.columns.template.setAll({
                tooltipText: "{name}, {categoryX}: {valueY}",
                tooltipY: am5.percent(10)
            });
            series.columns.template.events.on("click", function (ev) {
                onClick({name: name, category: ev.target.dataItem.dataContext, header: header})
            }, this);
            series.data.setAll(data);

            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            series.appear();

            series.bullets.push(function () {
                return am5.Bullet.new(root, {
                    sprite: am5.Label.new(root, {
                        text: "{valueY}",
                        fill: root.interfaceColors.get("alternativeText"),
                        centerY: am5.p50,
                        centerX: am5.p50,
                        populateText: true
                    })
                });
            });

            legend.data.push(series);
        }

        $.each(label, function (category, varr) {
            makeSeries(category, varr);
        });
        // Make stuff animate on load
        // https://www.amcharts.com/docs/v5/concepts/animations/
        chart.appear(1000, 100);

    }); // end am5.ready()
}

function SaveObject($url_controller_action, $dataInput, callbackSuccess, columnClass) {
    if (typeof columnClass == "undefined")
        columnClass = 's';

    $.dialog({
        columnClass: columnClass,
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=' + $url_controller_action,
                type: 'post',
                dataType: 'json',
                data: $dataInput,
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                return false;
            });
        }
    })
}

function SaveObjectTrangThai($url_controller_action, $dataInput, callbackSuccess, columnClass) {
    if (typeof columnClass == "undefined")
        columnClass = 's';

    $.dialog({
        columnClass: columnClass,
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=' + $url_controller_action,
                type: 'post',
                dataType: 'json',
                data: $dataInput,
            }).success(function (data) {
                callbackSuccess(data);
            }).error(function (r1, r2) {
                return false;
            });
        }
    })
}

function loadForm($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadform',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadForm4($dataInput, $size = 'm', callbackSuccess, callbackSave, callbackClose) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadform',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
                jQuery('#user-ngay_sinh').datepicker($.extend({}, $.datepicker.regional['vi'], {
                    "changeMonth": true,
                    "yearRange": "1972:2022",
                    "changeYear": true,
                    "dateFormat": "dd\/mm\/yy"
                }));
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại',
                action: function () {
                    if (typeof callbackClose != "undefined") return callbackClose();
                }
            }
        }
    });
}

function loadFormModel4($dataInput, $size = 'modal-full', $obj, btnSave = () => {
}, btnClose = () => {
}) {

    $.ajax({
        url: 'index.php?r=site/load-form-modal',
        data: $dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            $.blockUI();
        },
        success: function (data) {
            $(".modal").remove()
            $(".modal-backdrop").remove()
            $($obj).html(data)
            $(".modal-dialog").addClass($size);
            jQuery('.date').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));
            $("#modal-id").modal('show');
            jQuery('#user-ngay_sinh').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));

        },
        complete: function () {
            $.unblockUI();
        },
        error: function (r1, r2) {
            $.alert(r1.responseText)
        }
    })
    $(document).on("click", $obj + ' .modal-footer .btn-primary', function () {
        btnSave();
    })
    $(document).on("click", $obj + ' .modal-footer .btn-default', function () {
        btnClose();
    })
}

function loadFormModel($dataInput, $size = 'modal-full', $obj, btnSave = () => {
}, btnClose = () => {
}) {

    $.ajax({
        url: 'index.php?r=site/load-form-modal',
        data: $dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            $.blockUI();
            $("#modal-id").css('z-index', 1);
        },
        success: function (data) {
            $(".modal").remove()
            $(".modal-backdrop").remove()
            $($obj).html(data)
            $(".modal-dialog").addClass($size);
            jQuery('.date').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));
            $("#modal-id").modal('show');
            jQuery('#user-ngay_sinh').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));

        },
        complete: function () {
            $.unblockUI();
            $("#modal-id").css('z-index', 10050);

        },
        error: function (r1, r2) {
            $.alert(r1.responseText)
        }
    })
    $(document).on("click", $obj + ' .modal-footer .btn-primary', function () {
        btnSave();
    })
    $(document).on("click", $obj + ' .modal-footer .btn-default', function () {
        btnClose();
    })
}

function loadFormModel1($dataInput, $size = 'modal-full', $obj, btnSave = () => {
}, btnClose = () => {
}) {

    $.ajax({
        url: 'index.php?r=site/load-form-modal',
        data: $dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            $.blockUI();
        },
        success: function (data) {
            $(".modal").remove()
            $(".modal-backdrop").remove()
            $($obj).html(data)
            $(".modal-dialog").addClass($size);
            $("#modal-id").modal('show');
            $($obj + ' .modal-footer .btn-primary').remove();
            jQuery('#user-ngay_sinh').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));
            jQuery('.date').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));

        },
        complete: function () {
            $.unblockUI();
        },
        error: function (r1, r2) {
            $.alert(r1.responseText)
        }
    })

    $(document).on("click", $obj + ' .modal-footer .btn-default', function () {
        btnClose();
    })
}
function loadFormModel4($dataInput, $size = 'modal-full', $obj, btnSave = () => {
}, btnClose = () => {
}) {

    $.ajax({
        url: 'index.php?r=site/load-form-modal',
        data: $dataInput,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            $.blockUI();
        },
        success: function (data) {
            $(".modal").remove()
            $(".modal-backdrop").remove()
            $($obj).html(data)
            $(".modal-dialog").addClass($size);
            $("#modal-id").modal('show');
            $($obj + ' .modal-footer .btn-primary').remove();
            $($obj + ' .modal-footer .btn').remove();
            jQuery('#user-ngay_sinh').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));
            jQuery('.date').datepicker($.extend({}, $.datepicker.regional['vi'], {
                "changeMonth": true,
                "yearRange": "1972:2022",
                "changeYear": true,
                "dateFormat": "dd\/mm\/yy"
            }));

        },
        complete: function () {
            $.unblockUI();
        },
        error: function (r1, r2) {
            $.alert(r1.responseText)
        }
    })

    $(document).on("click", $obj + ' .modal-footer .btn-default', function () {
        btnClose();
    })
}

function chartBarV2(obj, content, value, category) {
    am5.ready(function () {

        // Create root element
        // https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new(obj);


        // Set themes
        // https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
            am5themes_Animated.new(root)
        ]);


        // Create chart
        // https://www.amcharts.com/docs/v5/charts/xy-chart/
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true
        }));

        // Add cursor
        // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
        cursor.lineY.set("visible", false);


        // Create axes
        // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
        var xRenderer = am5xy.AxisRendererX.new(root, {minGridDistance: 30});
        xRenderer.labels.template.setAll({
            rotation: 0,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            maxDeviation: 0.3,
            categoryField: category,
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {})
        }));

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root, {})
        }));


        // Create series
        // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: "Series 1",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: value,
            sequencedInterpolation: true,
            categoryXField: category,
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY}"
            })
        }));

        series.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5});
        series.columns.template.adapters.add("fill", function (fill, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });

        series.columns.template.adapters.add("stroke", function (stroke, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });


        // Set data
        var data = content

        xAxis.data.setAll(data);
        series.data.setAll(data);


        // Make stuff animate on load
        // https://www.amcharts.com/docs/v5/concepts/animations/
        series.appear(1000);
        chart.appear(1000, 100);

    }); // end am5.ready()
}

function chartPiev2(obj, content, category, value) {
    am4core.ready(function () {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create(obj, am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        // chart.legend = new am4charts.Legend();

        chart.data = content
        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = value;
        series.dataFields.category = category;

        series.ticks.template.disabled = true;
        series.alignLabels = false;
        series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
        series.labels.template.radius = am4core.percent(-40);
        series.labels.template.fill = am4core.color("white");

        series.labels.template.adapter.add("radius", function (radius, target) {
            if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
                return 0;
            }
            return radius;
        });

        series.labels.template.adapter.add("fill", function (color, target) {
            if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
                return am4core.color("#000");
            }
            return color;
        });
        // Add a legend
        chart.legend = new am4charts.Legend();

    }); // end am4core.ready()$(document).ready(function (){
}

function chartPieV3(obj, content, category, value) {
    am4core.ready(function () {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create(obj, am4charts.PieChart);

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = value;
        pieSeries.dataFields.category = category;

        // Let's cut a hole in our Pie chart the size of 30% the radius
        chart.innerRadius = am4core.percent(30);

        // Put a thick white border around each Slice
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;
        pieSeries.slices.template
            // change the cursor on hover to make it apparent the object can be interacted with
            .cursorOverStyle = [
            {
                "property": "cursor",
                "value": "pointer"
            }
        ];

        pieSeries.ticks.template.disabled = true;
        pieSeries.alignLabels = false;
        pieSeries.labels.template.text = "{value.percent.formatNumber('#.0')}%";
        pieSeries.labels.template.radius = am4core.percent(-40);
        pieSeries.labels.template.fill = am4core.color("white");

        pieSeries.labels.template.adapter.add("radius", function (radius, target) {
            if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
                return 0;
            }
            return radius;
        });

        pieSeries.labels.template.adapter.add("fill", function (color, target) {
            if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
                return am4core.color("#000");
            }
            return color;
        });

        // Create a base filter effect (as if it's not there) for the hover to return to
        var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
        shadow.opacity = 0;

        // Create hover state
        var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

        // Slightly shift the shadow and make it more prominent on hover
        var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
        hoverShadow.opacity = 0.7;
        hoverShadow.blur = 5;

        // Add a legend
        chart.legend = new am4charts.Legend();

        chart.data = content

    }); // end am4core.ready()
}

function loadForm1($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadform',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadForm2($dataInput, $size = 'm', callbackSuccess, callbackSave, textBtnAccept = '<i class="fa fa-save"></i> Lưu lại') {

    $.confirm({

        content: function () {

            var self = this;

            return $.ajax({

                url: 'index.php?r=site/loadform',

                data: $dataInput,

                type: 'post',

                dataType: 'json'

            }).success(function (data) {

                self.setContent(data.content);

                self.setTitle(data.title);

                self.setType('blue');

                callbackSuccess(data);

            }).error(function (r1, r2) {

                self.setContent(getMessage(r1.responseText));

                self.setTitle('Thông báo');

                self.setType('red');

                self.$$btnSave.prop('disabled', true);

                return false;

            });

        },

        columnClass: $size,

        buttons: {

            btnSave: {

                text: textBtnAccept,

                btnClass: 'btn-primary',

                action: function () {

                    if (typeof callbackSave != "undefined") return callbackSave();

                }

            },

            btnClose: {

                text: '<i class="fa fa-close"></i> Đóng lại'

            }

        }

    });

}

function loadFormNguoiPhuTrach($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadformnguoiphutrach',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadFormTrangThai($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadformtrangthai',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadFormThuPhi($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadformthuphi',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadFormFromUrl($dataInput, $controller_action, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=' + $controller_action,
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadFormThayTheNguoiPhuTrach($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadformthaythenguoiphutrach',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function loadFormKinhDoanhHoiVien($dataInput, $size = 'm', callbackSuccess, callbackSave) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=site/loadformkinhdoanhhoivien',
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                self.$$btnSave.prop('disabled', true);
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnSave: {
                text: '<i class="fa fa-save"></i> Lưu lại',
                btnClass: 'btn-primary',
                action: function () {
                    if (typeof callbackSave != "undefined") return callbackSave();
                }
            },
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function taiFileExcel($controller_action, $data) {
    $.ajax({
        url: 'index.php?r=' + $controller_action,
        data: $data,
        dataType: 'json',
        type: 'post',
        beforeSend: function () {
            $('.thongbao').html('');
            Metronic.blockUI();
        },
        success: function (data) {
            $.dialog({
                title: data.title,
                content: data.link_file,
            });
        },
        complete: function () {
            Metronic.unblockUI();
        },
        error: function (r1, r2) {
            $('.thongbao').html(r1.responseText);
        }
    });
}

function makeid(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function viewData($controller_action, $dataInput, $size, callbackSuccess) {
    $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'index.php?r=' + $controller_action,
                data: $dataInput,
                type: 'post',
                dataType: 'json'
            }).success(function (data) {
                self.setContent(data.content);
                self.setTitle(data.title);
                self.setType('blue');
                if (typeof callbackSuccess != "undefined")
                    callbackSuccess(data);
            }).error(function (r1, r2) {
                self.setContent(getMessage(r1.responseText));
                self.setTitle('Thông báo');
                self.setType('red');
                return false;
            });
        },
        columnClass: $size,
        buttons: {
            btnClose: {
                text: '<i class="fa fa-close"></i> Đóng lại'
            }
        }
    });
}

function showAlert($message) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": function () {
            // if($link != '')
            //     window.location = $link
        },
        "showDuration": "10000",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    toastr['info']($message, 'Thông báo');
}

function tinhDiemMoiCaNhan($idNhanVienPhongBan) {
    var $tongDiem = 0;
    $("tr.tr-nhan-vien-" + $idNhanVienPhongBan).each(function () {
        var $myParentTr = $(this).parent().parent().parent().parent();
        var $myCheckBox = $myParentTr.find('.checkChonCVQuy ');
        if ($myCheckBox.is(":checked")) {
            var $myInputDiem = $(this).find('td.td-diem-nhan-vien input').val();
            var $thucHien = $(this).find('td.td-thuc-hien select').val();
            $myInputDiem = ($myInputDiem == '' ? 0 : parseFloat($myInputDiem));
            if ($thucHien == 1)
                $tongDiem += $myInputDiem;
        }

    });
    $("#diem-nvien-" + $idNhanVienPhongBan).text($tongDiem);
}

function tinhTongDiemDonVi() {
    var $tongDiem = 0;
    $(".diem-so-don-vi input").each(function () {
        var $diemSo = $(this).val();
        $tongDiem += ($diemSo == '' ? 0 : parseFloat($diemSo));
    });
    $("span#tong-diem").text($tongDiem);
}

jQuery(document).ready(function () {
    Metronic.init(); // init metronic core components
    Layout.init(); // init current layout
    QuickSidebar.init(); // init quick sidebar
    // $(".tien-te").maskMoney({thousands:",", allowZero:true, /*suffix: " Tỷ",*/precision:3});
    // Hiển thị thông báo các công việc đã hoàn thành nhưng chưa duyệt
    $(document).on('click', 'ul li a.hover-initialized', function (e) {
        e.preventDefault();

        var $parent = $(this).parent();
        if ($parent.find('ul').length > 0) {
            $parent.addClass('open');
            $(this).attr('aria-expanded', 'true');
        }
    });
});
