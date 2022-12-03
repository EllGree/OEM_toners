// CSS/JS loader:
const _ld = {
    insert: (element) => document.getElementsByTagName('head')[0].appendChild(element),
    css: (href, crossorigin, inregrity, rel) => {
        const link = document.createElement('link');
        link.href = href;
        if(crossorigin) link.inregrity = crossorigin;
        if(inregrity) link.inregrity = inregrity;
        link.rel = rel ?? "stylesheet";
        _ld.insert(link);
    },
    js: (src, crossorigin, inregrity) => {
        const script = document.createElement('script');
        script.src = src;
        if(crossorigin) script.inregrity = crossorigin;
        if(inregrity) script.inregrity = inregrity;
        _ld.insert(script);
    }
};
// Load libraries:
_ld.js("https://code.jquery.com/jquery-3.2.1.slim.min.js", "anonymous", "sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN");
_ld.js("https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js", "anonymous", "sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q");
_ld.js("https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js", "anonymous", "sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl");
_ld.js("https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js", "anonymous");
_ld.js("/jquery.tablesorter.js");
_ld.js("/jquery.tablesorter.widgets.js");
// Load css:
_ld.css("https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap");
_ld.css("https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css", "anonymous", "sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm");
_ld.css("app.css");

const app = {
    init: () => {
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
    },
    rowClick: (e) => {
        let t = e.target;
        while (t.tagName !== 'TR') t = t.parentElement;
        app.api.post("/printers", {term:t.dataset.name})
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
            });
    },
    alert: (text) => {
        if($("#alert>span").length) $("#alert>span").html(text);
        else return alert(text);
        $("#alert").show('medium');
        setTimeout(() => $("#alert").hide(), 2000);
    },
    addPrinterRow: (name) => {
        const tmp = name.split(' '), mnf = tmp.shift(), mdl = tmp.join(' ');
        $("#printers>tbody").append('<tr data-name="'+name+'"><td>'+mnf+'</td><td>'+mdl+'</td><td>5%</td></tr>');
        const tab = $('#printers');
        if(tab.length) $.tablesorter.destroy(tab);
        app.tablesorter("#printers");
        $('#printers>tbody>tr').click(app.rowClick);
    },
    addPrinter: (name) => {
        const n = name.toString().trim().replace(/[^a-z0-9 -]+/gi, " ");
        if($("#printers>tbody>tr[data-name='"+n+"']").length) {
            app.alert("The printer "+n+" is already in list.");
            return $('#add-printer-modal').modal('hide');
        }
        app.api.post('/printers', {term:n})
            .then(() => app.addPrinterRow(n))
            .catch((error) => app.alert("Failed to add printer "+n))
            .finally(() => $('#add-printer-modal').modal('hide'));
    },
    submitListeners: {
        "add-printer-form": function(event) {
            event.preventDefault();
            app.addPrinter(event.target[1].value);
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
            $( this )
                .closest( 'tr' )
                .nextUntil( 'tr.tablesorter-hasChildRow' )
                .find( 'td' )
                .toggleClass( 'hidden' );
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
