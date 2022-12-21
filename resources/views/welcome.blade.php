<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OEM Toners</title>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" crossorigin="anonymous" defer></script>
    <script src="/jquery.tablesorter.min.js" defer></script>
    <script src="/loading-bar.min.js" defer></script>
    <script src="/app.js" defer></script>
    <link rel="stylesheet" crossorigin="anonymous" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" />
    <link rel="stylesheet" crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/loading-bar.min.css" />
    <link rel="stylesheet" href="/app.css" />
</head>
<body class="antialiased">
<div id="app">
    <table id="printers" class="tablesorter-blue">
        <thead>
            <tr>
                <th><input type="checkbox" class="toggleAll" title="Toggle visible checkboxes" onclick="app.toggleChecked()" /></th>
                <th class="filter-select filter-exact" data-placeholder="All Brands">Manufacturer</th>
                <th>Model</th>
                <th class="filter-select filter-exact" data-placeholder="All">Coverage</th>
            </tr>
        </thead>
        <tbody>
        @foreach($printers as $printer)
            <tr data-name="{{$printer->name}}" data-id="{{$printer->id}}">
                <td><input type="checkbox" class="printer-selector" /></td>
                <td>{{preg_replace('/ [\d\D]+$/', '', $printer->name)}}</td>
                <td>{{preg_replace('/^[^ ]+ /', '', $printer->name)}}</td>
                <td>{{$printer->getAttribute('coverage')}}%</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm">
                                <button type="button" class="btn btn-dark" id="AddPrintersButton"
                                        data-toggle="modal" data-target="#add-printer-modal">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                                    </svg>
                                    <span><u>A</u>dd Printer(s)</span>
                                </button>
                            </div>
                            <div class="col-sm">
                                <button type="button" class="btn btn-dark" id="ExportAllButton">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2zm-1 8V6h2v4h3l-4 4-4-4h3zm6 7H7v-2h10v2z"></path>
                                    </svg>
                                    <span><u>E</u>xport all</span>
                                </button>
                            </div>
                            <div class="col-sm">
                                <button type="button" class="btn btn-dark" disabled id="ExportButtonSelected">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"></path>
                                    </svg>
                                    <span>E<u>x</u>port</span>
                                </button>
                            </div>
                            <div class="col-sm">
                                <button type="button" class="btn btn-dark" disabled id="DeleteButtonSelected">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zm2.46-7.12 1.41-1.41L12 12.59l2.12-2.12 1.41 1.41L13.41 14l2.12 2.12-1.41 1.41L12 15.41l-2.12 2.12-1.41-1.41L10.59 14l-2.13-2.12zM15.5 4l-1-1h-5l-1 1H5v2h14V4z"></path>
                                    </svg>
                                    <span><u>D</u>elete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
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
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"></path>
                                    </svg> Add printer
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane hidden" id="bulk" role="tabpanel" aria-labelledby="bulk-tab">
                        <form class="needs-validation" novalidate id="add-printers-form">
                            <div class="form-group">
                                <textarea class="form-control" requiredaria-label="Printer names"
                                          placeholder="Please paste a list of printer names to import here"
                                          aria-describedby="inputGroupPrepend"
                                          id="printer-list"></textarea>
                                <div class="invalid-feedback">Enter a list of printer names to import.</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"></path>
                                    </svg> Add printers
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path>
                        </svg> Cancel
                    </button>
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
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"></path>
                                    </svg> Update Printer
                                </button>
                                <button type="button" onclick="app.deletePrinter()" class="btn btn-danger" name="delete">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zm2.46-7.12 1.41-1.41L12 12.59l2.12-2.12 1.41 1.41L13.41 14l2.12 2.12-1.41 1.41L12 15.41l-2.12 2.12-1.41-1.41L10.59 14l-2.13-2.12zM15.5 4l-1-1h-5l-1 1H5v2h14V4z"></path>
                                    </svg> Delete Printer
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane hidden" id="parts" role="tabpanel" aria-labelledby="parts-tab">
                        <table id="printerParts">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th class="filter-select filter-exact" data-placeholder="All">Type</th>
                                <th class="filter-select filter-exact" data-placeholder="All">Color</th>
                                <th>Yield</th>
                                <th>Price</th>
                            </tr>
                            </thead>
                            <tbody id="printerPartsBody"></tbody>
                        </table>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="app.download_parts()">
                            <svg viewBox="0 0 24 24">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"></path>
                            </svg> Download CSV
                        </button>
                    </div>
                    <div class="tab-pane hidden" id="groups" role="tabpanel" aria-labelledby="groups-tab">
                        <p><strong>This tab is under development.</strong></p>
                        <p>Presumably here should be a solution to the combinatorial problem of breaking down consumable parts
                            (toner, replaceable spare parts, cleaning materials, etc.) into groups, normalize the cost of consumables
                            (e.g. cost of printing per 1,000 pages), and calculate toner consumption based on a given printer's page coverage.</p>
                        <p>Unfortunately, I could not formalize the problem, because of the lack of data on yield and/or price of parts,
                            or the apparent lack of necessary types of parts, I could not even determine the principle of their grouping.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path>
                    </svg> Close
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Alert window -->
<div class="alert alert-danger alert-dismissible hidden" id="alert"><span></span></div>
<!-- Progress indicator -->
<div class="ldBar label-center" id="indicator" data-value="0" data-preset="circle"></div>
</body>
</html>
