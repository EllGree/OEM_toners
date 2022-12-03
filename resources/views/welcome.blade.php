<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OEM Toners</title>
    <script src="/app.js"></script>
</head>
<body class="antialiased">
<div id="app" style="display: none; padding:1rem;">
    <div class="alert alert-warning alert-dismissible hidden" id="alert">
        <span></span>
    </div>
    <table id="printers" class="tablesorter-blue">
        <thead>
            <tr>
                <th class="filter-select filter-exact" data-placeholder="Show all">Manufacturer</th>
                <th>Model</th>
                <th>Coverage</th>
            </tr>
        </thead>
        <tbody>
        @foreach($printers as $printer)
            <tr data-name="{{$printer->name}}">
                <td>{{preg_replace('/ [\d\D]+$/', '', $printer->name)}}</td>
                <td>{{preg_replace('/^[^ ]+ /', '', $printer->name)}}</td>
                <td>{{$printer->getAttribute('coverage')}}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-printer-modal">
            ＋ Add printer
        </button>
    </div>
</div>

<!-- Modal Add Printer -->
<div class="modal fade" id="add-printer-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <form class="needs-validation" novalidate id="add-printer-form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add new printer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" required
                               aria-describedby="inputGroupPrepend" autofocus
                               id="printer-name" placeholder="Input the printer name">
                        <div class="invalid-feedback">
                            Please enter the printer name.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Add printer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal Printer Details -->
<div class="modal fade bd-example-modal-lg" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Printer Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="printerParts">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="filter-select filter-exact" data-placeholder="Show all">Type</th>
                            <th class="filter-select filter-exact" data-placeholder="Show all">Color</th>
                            <th>Yield</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="printerPartsBody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="app.download_csv()">Download CSV</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
