// Date Range Picker
var start_date = ''
var end_date = ''
var $j = jQuery.noConflict();
// $j("#datepicker").datepicker();
$j('#filter_date').daterangepicker({
        "showDropdowns": true,
        // "maxYear": 3,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "parentEl": "Parent_Element",
        "startDate": "07/01/2022",
        "endDate": new Date(),
        "minDate": "05/01/2022",
        "maxDate": new Date(),
        todayHighlight: true
    }, function(start, end, label) {
    start_date = start.format('YYYY-MM-DD')
    end_date = end.format('YYYY-MM-DD')
    //   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
});

document.getElementById("dis_name").addEventListener('change', function(){
    console.log(document.getElementById("dis_name").value)

    timebased_TF()
    filterData_byDisName("team_load_count_dis_filter")
    
})

var options = {
    maintainAspectRatio: false,
    responsive: true,
    title: title,
    tooltips: tooltip,
    legend: lagend,
    layout: layout,
    scales: scales

}

var i_LC_labels = []
var i_LC_data = []
var i_TP_data = []
var i_AP_data = []
var i_APL_data = []
var i_TL = 0
var i_TP = 0
var i_AP = 0
var i_APL = 0

// Filter data through Ajax
function filterData_byDisName(actionType){
    var i_TL = 0
    var i_TP = 0
    var i_AP = 0
    var i_APL = 0
    i_LC_labels = []
    i_LC_data = []
    i_TP_data = []
    i_AP_data = []
    i_APL_data = []

    var dis_name = document.getElementById("dis_name").value

    var se_date = ""
    if(end_date !== ''){
        var se_date = '&startDate=' + start_date + '&endDate=' + end_date;
    }

    const charts = document.querySelectorAll('#i_avg_perload, #i_load_count, #i_total_profit, #i_avg_profit');
    charts.forEach(chart => {
        // chart.style.display = 'none';
    });

    var single_chart = document.querySelectorAll('.single-chart')
    single_chart.forEach(single_chart => {
        // single_chart.style.height = '250px';
    });

    var loader = document.querySelectorAll('.chart_loader')
    loader.forEach(loader => {
        loader.style.display = 'block';
    });


    $.ajax({
        method: "POST",
        url: "./Assets/backendfiles/charts/charts.php?action_type=" + actionType + se_date,
        data: {
            dispatcher: dis_name,
        },
        success: function(data) {
            setTimeout(function(){
                charts.forEach(chart => {
                    chart.style.display = 'block';
                });

                loader.forEach(loader => {
                    loader.style.display = 'none';
                });
                data = JSON.parse(data)
                for(var i=0; i<data.length; i++){
                    i_LC_labels.push(data[i].label);
                    i_LC_data.push(data[i].LC_data);
                    i_TP_data.push(data[i].TP_data);
                    i_AP_data.push(data[i].AP_data);
                    i_APL_data.push(data[i].APL_data);

                    i_TL += data[i].TL
                    i_TP += data[i].TP
                    i_AP += data[i].AP
                    i_APL += data[i].APL
                }

                document.getElementById("c_TL").textContent = 'Total: ' + i_TL
                document.getElementById("c_TP").textContent = 'Total: $' + i_TP
                document.getElementById("c_AP").textContent = 'Total: $' + i_AP
                document.getElementById("c_APL").textContent = 'Total: $' + i_APL
                document.getElementById("TL").textContent = i_TL
                document.getElementById("TP").textContent = '$ ' + i_TP
                document.getElementById("AP").textContent = '$ ' + i_AP
                document.getElementById("APL").textContent = '$ ' + i_APL

                // Individual Load Count
                i_load_count_chart.chart.config.data.datasets[0].data = i_LC_data
                i_load_count_chart.chart.config.data.labels = i_LC_labels
                i_load_count_chart.update()


                // Individual Total Profit
                i_total_profit_chart.chart.config.data.datasets[0].data = i_TP_data
                i_total_profit_chart.chart.config.data.labels = i_LC_labels
                i_total_profit_chart.update()

                // Individual Average Profit
                i_avg_profit_chart.chart.config.data.datasets[0].data = i_AP_data
                i_avg_profit_chart.chart.config.data.labels = i_LC_labels
                i_avg_profit_chart.update()

                // Individual Average per Load
                i_avg_perload_chart.chart.config.data.datasets[0].data = i_APL_data
                i_avg_perload_chart.chart.config.data.labels = i_LC_labels
                i_avg_perload_chart.update()
            }, 1000);
        }
    });
}

// Filter data through Ajax
function timebased_TF(){
    var dis_name = document.getElementById("dis_name").value

    // var loader = document.querySelectorAll('.chart_loader')
    // loader.forEach(loader => {
    //     loader.style.display = 'block';
    // });


    $.ajax({
        method: "POST",
        url: "./Assets/backendfiles/charts/charts.php?action_type=time_base_TF",
        data: {
            dispatcher: dis_name,
        },
        success: function(data) {
            setTimeout(function(){
                // loader.forEach(loader => {
                //     loader.style.display = 'none';
                // });

                data = JSON.parse(data)
                var content = ''
                $.each(data, function(i) {
                    content += '<tr><td>' + data[i].month + '</td>'
                    content += '<td>' + data[i].TL + '</td>'
                    content += '<td>' + data[i].TP + '</td>'
                    content += '<td>' + data[i].AP + '</td>'
                    content += '<td>' + data[i].ADP + '</td></tr>'
                });

                $('#timebased_table tbody').html(content)
            }, 1000);
        }
    });
}


