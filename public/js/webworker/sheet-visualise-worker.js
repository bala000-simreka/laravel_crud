self.addEventListener('message', async (e) => {    
    try {
        if(e.data.type == 'get_html'){
            importScripts(e.data.xlsxscript);
            var axis = [];
            const res = await fetch(e.data.url);
            const ab = await res.arrayBuffer();

            const wb = XLSX.read(ab, {
                dense: true
            });
            const ws = wb.Sheets[wb.SheetNames[0]];
            var html = XLSX.utils.sheet_to_json(ws,{header:1});
            postMessage({state: "done", html: html, wb: wb});
        }
        if(e.data.type == 'get_graph_data'){
            var xaxis = [];
            var yaxis = [];
            var zaxis = [];
            for(var i=0; i<e.data.html.length; i++){
                xaxis[i] = e.data.html[i][e.data.xaxis];
            }
            if(e.data.gftype != 'histogram'){
                for(var i=0; i<e.data.html.length; i++){
                    yaxis[i] = e.data.html[i][e.data.yaxis];
                }
            }
            postMessage({state: "done", xaxis:xaxis, yaxis:yaxis, zaxis:zaxis});
        }
    } catch (e) {
        /* Pass the error message back */
        postMessage({
            error: String(e.message || e)
        });
    }
}, false);