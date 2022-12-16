class Queue {
    constructor(callback) {
        this.clear();
        if(typeof callback == 'function') {
            this.callback = callback;
        }
    }
    enqueue(element) { // add element to the queue
        this.items.push(element);
        this.initSize = this.size();
    }
    dequeue() { // remove element from the queue
        if(this.items.length > 0) {
            return this.items.shift();
        }
    }
    peek() { // view the last element
        return this.items[this.items.length - 1];
    }
    isEmpty() { // check if the queue is empty
        if(this.callback) {
            this.callback(this.size(), this.initSize);
        }
        return this.items.length == 0;
    }
    size() { // the size of the queue
        return this.items.length;
    }
    clear() { // empty the queue
        this.items = [];
    }
};
const app = {
    manufacturers: 'HP,IBM,Advent,Apple,Brother,Canon,Compaq,Dell,Epson,Fargo,iHome,Kodak,Kyocera,' +
        'Lexmark,OKI,Polaroid,Panasonic,Pantum,Philips,Ricoh,Pitney Bowes,Samsung,Sharp,Utax,Xerox',
    init: () => {
        if(typeof $ !== 'function' || typeof axios !== 'function') {
            return setTimeout(app.init, 100);
        }
        app.lastAction = 'init';
        app.manufacturers = app.manufacturers.split(',');
        app.api = axios.create({
            headers: { "content-type": "application/x-www-form-urlencoded" },
            baseURL: window.location.origin + "/api",
        });
        app.tablesorter('#printers');
        $('#printers>tbody>tr').click(app.rowClick);
        // Autofocus in modal
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('[autofocus]').focus();
        });
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        const validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                } else { // Valid -> check handlers by form id:
                    app.submitListeners[form.id] && app.submitListeners[form.id](event);
                }
                form.classList.add('was-validated');
            }, false);
        });
        $('#addPrintrsTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
            $(this).find('[autofocus]').focus();
        });
        $('#printers').trigger('update').trigger("appendCache").trigger("applyWidgets");
    },
    rowClick: (e) => {
        if(app.queue) {
            return app.alert("Please wait until the end of the import procedure.");
        }
        let t = e.target;
        while (t.tagName !== 'TR') t = t.parentElement;
        app.LastPrinter = {
            id:t.dataset.id,
            name:t.dataset.name,
            coverage:parseInt(t.children[2].innerText),
            manufacturer:t.children[0].innerText,
            model:t.children[1].innerText
        };
        $('#info-manufacturer').val(app.LastPrinter.manufacturer);
        $('#info-model').val(app.LastPrinter.model);
        $('#info-coverage').val(app.LastPrinter.coverage);
        app.lastAction = 'fetch printer details';
        app.api.get("/printer/"+app.LastPrinter.id)
            .then((reply) => {
                let html = '';
                app.lastReply = reply.data;
                $('#detailsModalLabel').html(reply.data.name);
                Array.from(reply.data.parts).forEach((r) => {
                    html += '<tr><td>' + r.name + '</td><td>' +
                        r.type + '</td><td>' +
                        r.color + '</td><td>' +
                        r.yield + '</td><td>' +
                        r.price + '</td></tr>';
                });
                $('#printerPartsBody').html(html);
                app.tablesorter('#printerParts');
                $('#detailsModal').modal('show');
            }).catch(app.catchError).finally(app.finally);
    },
    getModel: (name) => {
        const brand = app.getBrand(name), n = name.toString()
            .replace(/[^a-z0-9 -]+/gi, " ")
            .trim().substring(brand.length).trim();
        // @@Know how: remove words "colour", "laser", "printer" in Dell models
        if(brand == 'Dell') return n.replace(/(colour|laser|printer) /i,'');
        // @@Know how: remove postfix DWF in Epson models
        if(brand == 'Epson') return n.replace(/DWF$/i,'');
        // @@Know how: remove prefix ADS- in Brother models
        if(brand == 'Brother') return n.replace(/^ADS-/i,'').replace(/^MFC- /, 'MFC-');
        return n;
    },
    getBrand: (name) => {
        const found = app.manufacturers.filter((brand) => {
            const re = new RegExp('^'+brand, 'i');
            return !!name.trim().match(re);
        });
        return found.length ? found[0] : '';
    },
    addPrinterRow: (name, id, coverage) => {
        const brand = app.getBrand(name), model = app.getModel(name), n = brand + ' ' + model;
        const $row = $('<tr data-name="'+n+'" data-id="'+id+'"><td>'+brand+'</td><td>'+model+'</td><td>'+(coverage ?? '5')+'%</td></tr>');
        const callback = () => $('#printers>tbody>tr[data-id="'+id+'"]').click(app.rowClick);
        $( '#printers' ).find('tbody').append($row).trigger('addRows', [$row, true, callback]);
    },
    updatePrinterRow: () => {
        if(!app.LastPrinter) return;
        const row = $('#printers>tbody>tr[data-id="'+app.LastPrinter.id+'"]');
        if(row.length<1) return;
        row.children()[0].innerText = app.LastPrinter.manufacturer;
        row.children()[1].innerText = app.LastPrinter.model;
        row.children()[2].innerText = app.LastPrinter.coverage + '%';
        row[0].dataset.name = app.LastPrinter.name;
        $('#printers').trigger('update').trigger("appendCache").trigger("applyWidgets");
    },
    deletePrinterRow: () => {
        if(!app.LastPrinter) return;
        $('#printers>tbody>tr[data-id="'+app.LastPrinter.id+'"]').remove();
        $('#printers').trigger('update').trigger("appendCache").trigger("applyWidgets");
    },
    addPrinter: (name) => {
        const brand = app.getBrand(name), model = app.getModel(name), n = brand + ' ' + model;
        const bad = (msg) => { app.alert(msg); app.finally(); }
        $('#add-printer-modal').modal('hide');
        app.lastAction = 'add printer';
        if(!brand) return bad("Failed to add "+n+"<br>Unknown printer manufacturer.");
        if(!model) return bad("Failed to add "+n+"<br>Unknown printer model.");
        if(model.length>50) return bad("Failed to add "+n+"<br>Model name is too long.");
        if($("#printers>tbody>tr[data-name='"+n+"']").length) return bad("The printer "+n+" is already in list.");
        app.api.post('/printers', {term:n})
            .then((r) => app.addPrinterRow(r.data.name, r.data.id))
            .catch(app.catchError)
            .finally(app.finally);
    },
    updatePrinter: () => {
        if(!app.LastPrinter) return;
        app.lastAction = 'update printer';
        app.api.post('/printer/' + app.LastPrinter.id, app.LastPrinter)
            .then(app.updatePrinterRow)
            .catch(app.catchError)
            .finally(app.finally);
    },
    deletePrinter: () => {
        if(!app.LastPrinter) return;
        $('#detailsModal').modal('hide');
        app.lastAction = 'delete printer';
        app.api.delete('/printer/' + app.LastPrinter.id)
            .then(app.deletePrinterRow)
            .catch(app.catchError)
            .finally(app.finally)
    },
    catchError: (error) => {
        let txt = 'Failed to '+app.lastAction.trim();
        if(app.LastPrinter) txt = txt.replace(/printer/, 'printer "'+app.LastPrinter.name+'"');
        console.log(txt, {error});
        const msg = txt + (error.message ? '<br>' +error.message : '') +
            (error.response && error.response.data.message ? ' -- ' + error.response.data.message :
                (error.response && error.response.statusText? ' -- ' + error.response.statusText : ''));
        app.alert(msg);
    },
    finally: () => {
        if(app.lastAction == 'update printer') $('#detailsModal').modal('hide');
        if(app.lastAction == 'delete printer') app.LastPrinter = false;
        if(app.lastAction == 'add printer' && app.queue) {
            if(!app.queue.isEmpty()) {
                // Small delay to prevent "429 Too Many Requests" error
                return setTimeout(app.qNext, 100);
            }
            document.getElementById('plus').classList.remove('disapear'); // Show "Plus" button
            app.progress(100);
            delete app.queue; // Destroy queue
        }
    },
    qNext: () => app.addPrinter(app.queue.dequeue()), // Next printer from Bulk import
    qCounter: (current, full) => { // Callback for queue
        const percent = Math.round(10000 - current * 10000 / full) / 100;
        app.progress(percent);
    },
    submitListeners: {
        "update-printer-form": function(event) {
            const form = event.target;
            event.preventDefault();
            console.dir(form);
            if (event.target[0].value == app.LastPrinter.manufacturer &&
                event.target[1].value == app.LastPrinter.model &&
                parseInt(event.target[2].value) == parseInt(app.LastPrinter.coverage)) {
                return app.alert("Nothing to update here.");
            }
            app.LastPrinter.manufacturer = event.target[0].value;
            app.LastPrinter.model = event.target[1].value;
            app.LastPrinter.coverage = event.target[2].value;
            app.LastPrinter.name = app.LastPrinter.manufacturer + ' ' + app.LastPrinter.model;
            app.updatePrinter();
        },
        "add-printer-form": function(event) {
            const val = event.target[0].value.toString().trim();
            event.preventDefault();
            if(val) app.addPrinter(val);
        },
        "add-printers-form": function(event) {
            const val = event.target[0].value.toString().trim();
            event.preventDefault();
            let lst = val.split('\n');
            if(lst.length<1) return;
            document.getElementById('plus').classList.add('disapear'); // Hide "Plus" button
            app.progress(0);
            app.queue = new Queue(app.qCounter);
            lst.forEach((n) => app.queue.enqueue(n));
            app.addPrinter(app.queue.dequeue()); // Start Bulk import process
        }
    },
    alert: (text) => {
        if(!$("#alert>span").length) return alert(text);
        $("#alert>span").html(text);
        $("#alert").show();
        setTimeout(() => $("#alert").hide(), 2500);
    },
    progress: (percent) => {
        if (!app.indicator) app.indicator = new ldBar('#indicator');
        app.indicator.set(parseInt(percent));
        if(parseInt(percent) === 100) setTimeout(() => $('#indicator').hide(), 500);
        else $('#indicator').show();
    },
    download_csv: () => {
        const parts = app.lastReply.parts ?? false;
        if(!parts) return;
        const fields = Object.keys(parts[0]).filter((e)=>!e.match(/id$/));
        const frec = (r) => fields.map((f)=>r[f].toString().replace(/[,]+/,'')).join(',');
        const blob = new Blob([fields.join(',')+'\n'+parts.map(frec).join('\n')], {type: 'text/csv;charset=utf-8;'});
        const link = document.createElement("a");
        link.setAttribute("href", URL.createObjectURL(blob));
        link.setAttribute("download", app.lastReply.name.replace(/[^a-zA-Z0-9]+/g,'_')+'.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },
    tablesorter: (selector) => {
        const sel = selector ?? 'table';
        $(sel).trigger("destroy");
        const t = $(sel).tablesorter({
            theme : 'blue',
            // headers: {'.nosort':{sorter:false}},
            // this is the default setting
            cssChildRow : "tablesorter-childRow",
            // initialize zebra and filter widgets
            widgets : [ "zebra", "filter", "uitheme"],
            widgetOptions: {
                // include child row content while filtering, if true
                filter_childRows  : true,
                // class name applied to filter row and each input
                filter_cssFilter  : 'tablesorter-filter',
                // search from beginning
                filter_startsWith : false,
                // Set this option to false to make the searches case sensitive
                filter_ignoreCase : true
            }
        });
        t.find( '.tablesorter-childRow td' ).addClass( 'hidden' );
        t.delegate( '.toggle', 'click' ,function() {
            // use "nextUntil" to toggle multiple child rows
            // toggle table cells instead of the row
            $( this ).closest( 'tr' )
                .nextUntil( 'tr.tablesorter-hasChildRow' )
                .find( 'td' ).toggleClass( 'hidden' );
            return false;
        });
        $('button.toggle-combined').click( function() {
            const wo = $table1[0].config.widgetOptions, o = !wo.filter_childRows;
            wo.filter_childRows = o;
            $( '.state1' ).html( o.toString() );
            // update filter; include false parameter to force a new search
            t.trigger( 'search', false );
            return false;
        });
    }
};
window.addEventListener('load', app.init);
