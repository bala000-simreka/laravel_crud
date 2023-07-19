function sheet_to_csv_cb(ws, cb, opts, batch = 1000) {
    XLSX.stream.set_readable(() => ({
        __done: false,
        // this function will be assigned by the SheetJS stream methods
        _read: function() {
            this.__done = true;
        },
        // this function is called by the stream methods
        push: function(d) {
            if (!this.__done) cb(d);
            if (d == null) this.__done = true;
        },
        resume: function pump() {
            for (var i = 0; i < batch && !this.__done; ++i) this._read();
            if (!this.__done) setTimeout(pump.bind(this), 0);
        }
    }));
    return XLSX.stream.to_html(ws, opts);
}

/* this callback will run once the main context sends a message */
self.addEventListener('message', async (e) => {
    importScripts(e.data.xlsxscript);
    try {
        postMessage({
            state: "fetching " + e.data.url
        });
        /* Fetch file */
        const res = await fetch(e.data.url);
        const ab = await res.arrayBuffer();

        /* Parse file */
        postMessage({
            state: "parsing"
        });
        const wb = XLSX.read(ab, {
            dense: true
        });
        const ws = wb.Sheets[wb.SheetNames[0]];

        /* Generate CSV rows */
        postMessage({
            state: "csv"
        });
        const strm = sheet_to_csv_cb(ws, (csv) => {
            if (csv != null) postMessage({
                csv
            });
            else postMessage({
                state: "done"
            });
        });
        strm.resume();
    } catch (e) {
        /* Pass the error message back */
        postMessage({
            error: String(e.message || e)
        });
    }
}, false);