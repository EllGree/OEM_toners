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
            <tr data-name="{{$printer->name}}" data-id="{{$printer->id}}">
                <td>{{preg_replace('/ [\d\D]+$/', '', $printer->name)}}</td>
                <td>{{preg_replace('/^[^ ]+ /', '', $printer->name)}}</td>
                <td>{{$printer->getAttribute('coverage')}}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-printer-modal">
            Add Printer(s)
        </button>
    </div>
</div>

<!-- Modal Add Printer -->
<div class="modal fade" id="add-printer-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add new printer(s)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="addPrintersTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="single-tab" data-toggle="tab" href="#single" role="tab" aria-controls="single" aria-selected="true">Single Printer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="bulk-tab" data-toggle="tab" href="#bulk" role="tab" aria-controls="bulk" aria-selected="false">Bulk Import</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content" style="padding-top:12px;">
                    <div class="tab-pane active" id="single" role="tabpanel" aria-labelledby="single-tab">
                        <form class="needs-validation" novalidate id="add-printer-form">
                            <div class="form-group">
                                <input type="text" class="form-control" required  maxlength="50"
                                       aria-describedby="inputGroupPrepend" autofocus
                                       id="printer-name" placeholder="Input the printer name">
                                <div class="invalid-feedback">Please enter the printer name.</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Add printer</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane hidden" id="bulk" role="tabpanel" aria-labelledby="bulk-tab">
                        <form class="needs-validation" novalidate id="add-printers-form">
                            <div class="form-group">
                                <textarea class="form-control" requiredaria-label="Printer names"
                                          aria-describedby="inputGroupPrepend"
                                          id="printer-list"></textarea>
                                <div class="invalid-feedback">Please enter bulk printers list.</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Add printers</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
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
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="PrinterDetailsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Printer Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="parts-tab" data-toggle="tab" href="#parts" role="tab" aria-controls="bulk" aria-selected="false">Parts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="groups-tab" data-toggle="tab" href="#groups" role="tab" aria-controls="bulk" aria-selected="false">Groups</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content" style="padding-top:12px;">
                    <div class="tab-pane active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <form class="needs-validation" novalidate id="update-printer-form">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="ManufacturerLabel">Printer Manufacturer</span>
                                </div>
                                <input type="text" class="form-control" required
                                       aria-describedby="ManufacturerLabel"
                                       id="info-manufacturer" placeholder="Input the printer manufacturer">
                                <div class="invalid-feedback">Please enter the manufacturer name.</div>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="ModelLabel">Printer Model</span>
                                </div>
                                <input type="text" class="form-control" required
                                       aria-describedby="ModelLabel"
                                       id="info-model" placeholder="Input the printer model name">
                                <div class="invalid-feedback">Please enter the model name.</div>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="CoverageLabel">Page coverage (%)</span>
                                </div>
                                <input type="number" class="form-control" required autofocus
                                       aria-describedby="CoverageLabel" min="1" max="100"
                                       id="info-coverage" placeholder="Input the printer coverage">
                                <div class="invalid-feedback">Please input the coverage.</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update printer</button>
                                <button type="button" onclick="app.deletePrinter()" class="btn btn-danger" name="delete">Delete printer</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane hidden" id="parts" role="tabpanel" aria-labelledby="parts-tab">
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
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="app.download_csv()">Download CSV</button>
                    </div>
                    <div class="tab-pane hidden" id="groups" role="tabpanel" aria-labelledby="groups-tab">
                        <br/><strong>This tab is still under development.</strong><br/><br/>
                        It will break down consumables into groups, normalize the cost of consumables (toner, replaceable spare parts, cleaning materials) per 1,000 pages of printing, and calculate toner consumption based on a given printer's page coverage.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Alert window -->
<div class="alert alert-danger alert-dismissible hidden" id="alert"><span></span></div>
</body>
</html>
