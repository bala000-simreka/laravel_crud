self.addEventListener('message', async (e) => {    
    try {
        importScripts(e.data.xlsxscript);
        
        const res = await fetch(e.data.url);
        const ab = await res.arrayBuffer();

        const wb = XLSX.read(ab, {dense: true});
        const ws = wb.Sheets[wb.SheetNames[0]];
        var html = XLSX.utils.sheet_to_json(ws,{header:1});

        html.splice(1,1); //remove Units row from html

        var headers = [],
        headerHtml = '<tr id="header"><td></td>',
        graphHtml = '<tr><td></td>';
        
        for(var x = 0;x<html[0].length;x++){
            headers[x] = html[0][x];
            headerHtml += '<td>'+html[0][x]+'</td>';
            graphHtml += '<td id="graph_'+x+'"><img height="70px" heiht="70px" src="'+e.data.loader+'"></td>';
        }
        headerHtml += '</tr>';
        graphHtml += '</tr>';

        var graphData = [];
        for(var j=0; j<html[0].length; j++){
            graphData[j] = [];
        }
        for(var i=2; i<html.length; i++){
            for(var j=0; j<html[i].length; j++){
                graphData[j].push(html[i][j]);
            }
        }

        html.splice(0,1); //remove header row from html

        var clusterizedData = html.map(function(row,cnt) {
            var index = cnt + 1;
            add_index = '<td>'+index+'</td>';
            return "<tr>" +add_index+
                row.map(function(val) {
                    return '<td>' + val + '</td>';
                }).join(" ") +
            "</tr>"
        });
        
        postMessage({state: 'done', html:html, graphData:graphData, clusterizedData:clusterizedData, headers:headers, headerHtml:headerHtml, graphHtml:graphHtml});
    } catch (e) {
        /* Pass the error message back */
        postMessage({
            error: String(e.message || e)
        });
    }
}, false);