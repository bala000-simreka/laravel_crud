function plotGraph(graphtype, data, containerId = ''){
    var trace = {};
    var layout = {};

    if(data.customLayout){
        layout.height = data.customLayout.height;
        layout.width = data.customLayout.width;
    }

    var xdata = data.x;
    trace.x = xdata;
    trace.type = graphtype;
    layout.xaxis = {title: data.xtitle};
    layout.xaxis.automargin = true;
    //layout.xaxis.autorange = false;
    var xmin = Math.min(...xdata);
    var xmax = Math.max(...xdata);
    layout.xaxis.range = [xmin, xmax];
    var nticksVal = (xmin === xmax)?1:8;
    layout.xaxis.nticks = nticksVal;
    if (graphtype === 'bar' || graphtype === 'scatter') {
        var ydata = data.y;
        trace.y = ydata;
        layout.yaxis = {title: data.ytitle};
        layout.yaxis.range = [Math.min(...ydata), Math.max(...ydata)];
    } 
    if (graphtype === 'scatter') {
        trace.mode = 'markers'
    }

    layout.title = (data.customNotitle) ? '' : graphtype.charAt(0).toUpperCase() + graphtype.slice(1) + ' Graph';

    var chartData = [trace];

    if(data.defaultConfig){
        Plotly.newPlot(containerId, chartData, layout, data.defaultConfig);
    } else {
        Plotly.newPlot(containerId, chartData, layout);
    }
}