<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OEM Toners</title>
    <style>
        #mask {display: none;position: absolute;user-select: none;height: 100%;width: 100%;background-color: rgb(0 0 0 / 43%);bottom: 0;left: 0;right: 0;top: 0;z-index: 9999;}
        #mask.loading {display: block;}
        #mask.loading:before {content: "";background-color: rgba(0, 0, 0, 0);border: 5px solid rgb(255 243 16 / 90%);opacity: 0.9;border-right: 5px solid rgb(0 191 255 / 95%);border-left: 5px solid rgb(255 12 230);border-top: 5px solid black;border-radius: 50px;box-shadow: 0 0 3px 6px #ffffff;width: 50px;height: 50px;-moz-animation: spinPulse 1s infinite ease-in-out;-webkit-animation: spinPulse 1s infinite linear;margin: -25px 0 0 -25px;position: absolute;top: 50%;left: 50%;}
        #mask.loading:after {content: "";background-color: rgba(0, 0, 0, 0);border: 5px solid rgb(255 243 16 / 90%);opacity: 0.9;border-left: 5px solid rgb(255 12 230);border-right: 5px solid rgb(0 191 255 / 95%);border-top: 5px solid black;border-radius: 50px;box-shadow: 0 0 3px 2px #ffffff;width: 30px;height: 30px;-moz-animation: spinoffPulse 1s infinite linear;-webkit-animation: spinoffPulse 1s infinite linear;margin: -15px 0 0 -15px;position: absolute;top: 50%;left: 50%;}
        @-moz-keyframes spinPulse {
            0% {-moz-transform: rotate(160deg);opacity: 0;box-shadow: 0 0 1px #2187e7;}
            50% {-moz-transform: rotate(145deg);opacity: 1;}
            100% {-moz-transform: rotate(-320deg);opacity: 0;}
        }
        @-moz-keyframes spinoffPulse {
            0% {-moz-transform: rotate(0deg);}
            100% {-moz-transform: rotate(360deg);}
        }
        @-webkit-keyframes spinPulse {
            0% {-webkit-transform: rotate(160deg);opacity: 0;box-shadow: 0 0 1px #2187e7;}
            50% {-webkit-transform: rotate(145deg);opacity: 1;}
            100% {-webkit-transform: rotate(-320deg);opacity: 0;}
        }
        @-webkit-keyframes spinoffPulse {
            0% {-webkit-transform: rotate(0deg);}
            100% {-webkit-transform: rotate(360deg);}
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" crossorigin="anonymous" defer></script>
    <script src="/jquery.tablesorter.js" defer></script>
    <script src="/app.js" defer></script>
    <link rel="stylesheet" crossorigin="anonymous" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" />
    <link rel="stylesheet" crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/app.css" />
</head>
<body class="antialiased">
<div id="mask" class="loading"></div>
<div id="app">
    <table id="printers" class="tablesorter-blue">
        <thead>
            <tr>
                <th class="filter-select filter-exact" data-placeholder="All Brands">Manufacturer</th>
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
                                Please enter a list of no more than 50 printer names to import.
                                <textarea class="form-control" requiredaria-label="Printer names"
                                          aria-describedby="inputGroupPrepend"
                                          id="printer-list"></textarea>
                                <div class="invalid-feedback">Please enter a list of printers.</div>
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
<button id="plus" type="button" class="btn btn-primary" title="App Printer(s)" data-toggle="modal" data-target="#add-printer-modal">+</button>

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
                                <th class="filter-select filter-exact" data-placeholder="All">Type</th>
                                <th class="filter-select filter-exact" data-placeholder="All">Color</th>
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
