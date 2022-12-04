// CSS/JS loader:
const _ld = {
    insert: (element) => document.getElementsByTagName('head')[0].appendChild(element),
    css: (href) => {
        const link = document.createElement('link');
        link.href = href; link.crossorigin = "anonymous"; link.rel = "stylesheet";
        _ld.insert(link);
    },
    js: (src) => {
        const script = document.createElement('script');
        script.src = src; script.inregrity = "anonymous";
        _ld.insert(script);
    }
};
// Load libraries:
_ld.js("https://code.jquery.com/jquery-3.2.1.slim.min.js");
_ld.js("https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js");
_ld.js("https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js");
_ld.js("https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js");
_ld.js("/jquery.tablesorter.js");
_ld.js("/jquery.tablesorter.widgets.js");
// Load css:
_ld.css("https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap");
_ld.css("https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css");
_ld.css("app.css");

const app = {
    init: () => {
        app.manufacturers = 'HP,Brother,Canon,Dell,Epson,iHome,Kodak,Kyocera,Lexmark,OKI,Polaroid,Panasonic,Ricoh,Samsung,Sharp,Xerox'
            .split(',');
        app.api = axios.create({
            headers: { "content-type": "application/x-www-form-urlencoded" },
            baseURL: window.location.origin + "/api"
        });
        app.tablesorter('#printers');
        $('#printers>tbody>tr').click(app.rowClick);
        $('#app')[0].style.display='block';
        // Autofocus in modal
        $('.modal').on('shown.bs.modal', function() {$(this).find('[autofocus]').focus();});
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
            e.preventDefault()
            $(this).tab('show');
            $(this).find('[autofocus]').focus();
        });
        $('#printers').trigger('update').trigger("appendCache").trigger("applyWidgets");
    },
    alert: (text) => {
        if(!$("#alert>span").length) return alert(text);
        $("#alert>span").html(text);
        $("#alert").show();
        setTimeout(() => $("#alert").hide(), 2500);
    },
    rowClick: (e) => {
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
            })
            .catch((error) => {
                console.log({error});
                app.alert("Failed to fetch details for printer. "+(error.message??''));
            });
    },
    getModel: (name) => {
        const brand = app.getBrand(name), n = name.toString()
            .replace(/[^a-z0-9 -]+/gi, " ")
            .trim()
            .substring(brand.length).trim();
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
    addPrinter: (name) => {
        const brand = app.getBrand(name), model = app.getModel(name), n = brand + ' ' + model;
        if(!brand) return app.alert("Failed to add "+n+"<br>Unknown printer manufacturer.");
        if(!model) return app.alert("Failed to add "+n+"<br>Unknown printer model.");
        if(model.length>50) return app.alert("Failed to add "+n+"<br>Model name is too long.");
        if($("#printers>tbody>tr[data-name='"+n+"']").length) return app.alert("The printer "+n+" is already in list.");
        $('#add-printer-modal').modal('hide');
        app.api.post('/printers', {term:n})
            .then((r) => app.addPrinterRow(r.data.name, r.data.id))
            .catch((error) => {
                console.log({error});
                const msg = "Failed to add " + n +
                    (error.message ? '<br>' +error.message : '') +
                    (error.response && error.response.data.message ? ' -- ' + error.response.data.message :
                        (error.response && error.response.statusText? ' -- ' + error.response.statusText : ''));
                app.alert(msg);
            })
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
    updatePrinter: () => {
        if(!app.LastPrinter) return;
        app.api.post('/printer/' + app.LastPrinter.id, app.LastPrinter)
            .then(app.updatePrinterRow).catch((error) => {
            console.log({error});
            const msg = "Failed to update " + app.LastPrinter.name +
                (error.message ? '<br>' +error.message : '') +
                (error.response && error.response.data.message ? ' -- ' + error.response.data.message :
                    (error.response && error.response.statusText? ' -- ' + error.response.statusText : ''));
            app.alert(msg);
        }).finally(() => $('#detailsModal').modal('hide'));
    },
    deletePrinter: () => {
        if(!app.LastPrinter) return;
        $('#detailsModal').modal('hide');
        app.api.delete('/printer/' + app.LastPrinter.id)
            .then(() => {
                $('#printers>tbody>tr[data-id="'+app.LastPrinter.id+'"]').remove();
                $('#printers').trigger('update').trigger("appendCache").trigger("applyWidgets");
            }).catch((error) => {
                console.log({error});
                const msg = "Failed to delete " + app.LastPrinter.name +
                    (error.message ? '<br>' +error.message : '') +
                    (error.response && error.response.data.message ? ' -- ' + error.response.data.message :
                        (error.response && error.response.statusText? ' -- ' + error.response.statusText : ''));
                app.alert(msg);
            }).finally(() => (app.LastPrinter = false));
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
            if(lst.length>50) {
                lst = lst.slice(0, 50);
                app.alert("The list of more than 50 models is shortened.");
            } else if(lst.length<1) {
                return;
            }
            lst.forEach((n, i) => {
                setTimeout(() => app.addPrinter(n), i*450);
            });
        }
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
