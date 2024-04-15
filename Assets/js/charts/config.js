// Custom Tooltip
var tooltip = {
    callbacks: {
        title: function (tooltipItem, data) {
            return data['labels'][tooltipItem[0]['index']];
        },
        label: function (tooltipItem, data) {
            return data['datasets'][0]['data'][tooltipItem['index']];
        },
        // afterLabel: function (tooltipItem, data) {
        //     var dataset = data['datasets'][0];
        //     var percent = Math.round((dataset['data'][tooltipItem['index']] * 100))
        //     return '(' + percent + '%)';
        // }
    },
    backgroundColor: '#FFF',
    titleFontSize: 16,
    titleFontColor: '#3a3a3a',
    bodyFontColor: '#6a6a6a',
    bodyFontSize: 14,
    borderColor: 'rgba(254, 206, 0, 1)',
    borderWidth: 1,
    xPadding: 10,
    yPadding: 10,
    displayColors: false
}

var tooltipv3 = {
    backgroundColor: '#fff',
    borderColor: 'hsl(209, 100%, 50%)',
    borderWidth: 1,
    titleColor: 'black',
    titleAlign: 'center',
    displayColors: true,
    boxWidth: 0,
    boxHeight: 0,
    bodyAlign: 'center',
    usePointStyle: true,
    callbacks: {
        labelTextColor: function(context){
            return myChart.data.datasets.borderColor;
        },
        labelPointStyl: function(context){
            return {
                pointStyle: 'star',
                rotation: 0
            }
        }
    }
}

// Layout
var layout = {
    padding: {
        left: 10,
        right: 10,
        top: 10,
        bottom: 10
    }
}

var scalesStackedV3 = {
    yAxes: [{
        ticks: {
            // beginAtZero: true,
            display: true,
            size: 10,
        },
        gridLines: {
            display: false
        }
    }],
    xAxes: [{
        gridLines: {
            display: false
        },
        barPercentage: 0.4
    }],
    x: {
        stacked: true,
    },
    y: {
        stacked: true,
    }
}

var style = getComputedStyle(document.body);
var fontColor = style.getPropertyValue('--font');

// Scales
var scales = {
    yAxes: [{
        ticks: {
            beginAtZero: true,
            display: true,
            size: 10,
            fontColor: fontColor,
        },
        gridLines: {
            display: false
        }
    }],
    xAxes: [{
        gridLines: {
            display: false
        },
        ticks: {
            // beginAtZero: true,
            display: true,
            // size: 10,
            fontColor: fontColor,
        },
    }],
}

// Header Option
var headeroption = {
    maintainAspectRatio: false,
    responsive: true,
    title: title,
    tooltips: tooltip,
    legend: lagend,
    layout: layout,
    scales: scales

}

var stacked_scales = {
    yAxes: [{
        stacked: true,
        ticks: {
            beginAtZero: true,
            display: true,
            size: 10,
            beginAtZero: true,
            fontColor: fontColor,
        },
        gridLines: {
            display: false
        }
    }],
    xAxes: [{
        stacked: true,
        gridLines: {
            display: false
        },
        barPercentage: 0.4,
        ticks: {
          beginAtZero: true,
          fontColor: fontColor,
        }
    }],
}

// Lagend
var lagend = {
    display: false,
    fontColor: 'red',
    position: 'top', // top, left, bottom, right
    fullWidth: false,
    // labels: {
    //     boxWidth: 10,
    //     fontSize: 10,
    //     fontStyle: 'normal',
    //     fontColor: '#dadada',
    //     fontFamily: '',
    //     padding: 0,
    //     usePointStyle: true,
    // },
    labels: {
        fontColor: "blue",
        fontSize: 18
    }
}

// Title
var title = {
    text: 'This is title',
    display: false,
    position: 'top', // top, right, bottom, left
    fontSize: 20,
    fontFamily: '',
    fontColor: 'red',
    fontStyle: 'bold',
    padding: 10,

}

// Plugin
var plugin = {
    legend: {
        display: false,
        labels: {

            // This more specific font property overrides the global property
            font: {
                size: 8,

            }
        }
    }
}

// Donught Plugin
var d_plugin = {
    legend: {
        display: false,
        labels: {

            // This more specific font property overrides the global property
            font: {
                size: 8,

            }
        }
    }
}


// Dongught Scales VS
var d_scalesv3 = {
    x: {
        grid: {
            display: false,
        },
        ticks: {
            color: "#aaaa",
            font: {
                size: 8
            }
        },
        display: false,
    },
    y: {
        grid: {
            display: false
        },
        ticks: {
            color: "#aaaa",
            font: {
                size: 8
            }
        },
        display: false,
    },
}

// Scales V3
var scalesv3 = {
    x: {
        grid: {
            display: false,
        },
        ticks: {
            color: "#aaaa",
            font: {
                size: 8
            }
        }
    },
    y: {
        grid: {
            display: false
        },
        ticks: {
            color: "#aaaa",
            font: {
                size: 8
            }
        }
    },
}

// Background Colors
var bg1 = [
    "#F94144",
    "#F3722C",
    "#F8961E",
    "#F9844A",
    "#F9C74F",
    "#90BE6D",
    "#43AA8B",
    "#4D908E",
    "#577590",
    "#277DA1",
    "#005F73",
    "#0A9396",
    "#94D2BD",
    "#E9D8A6",
    "#EE9B00",
    "#CA6702",
    "#BB3E03",
    "#AE2012",
    "#9B2226",
]


// Display No data message on empty charts
Chart.plugins.register({
    afterDraw: function(chart) {
        if (chart.data.datasets[0].data.every(item => item === 0) && (chart.canvas.id !== "goal_left_to_achieve")) {
            let ctx = chart.chart.ctx;
            let width = chart.chart.width;
            let height = chart.chart.height;

            chart.clear();
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('No data to display', width / 2, height / 2);
            ctx.restore();
        }
    }
});