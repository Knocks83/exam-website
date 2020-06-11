<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Get the $_GET['table'], check if it's in the tables and set the title -->
    <?php
    include '../config.php';

    $tablesApi = json_decode(file_get_contents($base_dir . '/api/schema.php'), true);

    if (isset($tablesApi['tables'])) {
        $tables = $tablesApi['tables'];
    } else {
        $tables = [];
    }

    // If it gets a table as parameter, check if it's in the DB tables
    if (isset($_GET['table']) && in_array($_GET['table'], $tables)) {
        // If the table is in the DB tables than use it
        $getTable = $_GET['table'];
    } else {
        // If the table isn't in the DB tables, use the default table
        $getTable = $default_table;
    }

    // Set the page title
    print("<title>$getTable - AllMyData</title>");
    ?>

    <!-- Set favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../img/apple-touch-icon.png">

    <!-- Bootstrap + deps -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <!-- Datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/fh-3.1.7/r-2.2.5/sc-2.0.2/sp-1.1.1/datatables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/fh-3.1.7/r-2.2.5/sc-2.0.2/sp-1.1.1/datatables.min.js"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/6871400b1b.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../css/css.css">

    <style>
        /* DataTables styling */
        label {
            color: white;
        }

        #table_info {
            color: white;
        }

        .paginate_button a {
            background-color: #2a2a2e;
            border: 0px;
        }

        .paginate_button.disabled a {
            background-color: #2a2a2e !important;
            border: 0px !important;
        }

        .paginate_button,
        .paginate_button.disabled {
            border: 1px solid #343a40;
        }

        .custom-select,
        .custom-select option {
            background-color: #2a2a2e;
            color: white;
        }

        #table-select a:hover {
            background-color: #2a2a2e;
        }

        #table-select-button {
            background-color: #343a40;
        }

        .form-control {
            background-color: #343a40;
            border: 0px;
        }

        /* End DataTables styling */

        #table-select {
            background-color: #3e444a;
        }

        #table-select a {
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="d-flex flex-grow-1">
            <span class="w-100 d-lg-none d-block">
                <!-- Hidden spacer to center the name on mobile --></span>
            <a class="navbar-brand" href="#">
                AllMyData
            </a>
            <!-- Navbar button for mobile view -->
            <div class="w-100 text-right">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
        <div class="collapse navbar-collapse flex-grow-1 text-right" id="myNavbar">
            <ul class="navbar-nav ml-auto flex-nowrap">
                <!-- Link -->
                <li class="nav-item">
                    <a class="nav-link" href="../">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">Air Sensors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Componenti">What did I use?</a>
                </li>
            </ul>
        </div>
    </nav>


    <!-- Modal to show the map when the table is air_stations -->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalTitle">Map</h5>
                    <button type="button" class="close" data-dismiss="modal" data-target="#mapModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div style="display: none;"><a id="latCoord"></a><a id="lngCoord"></a></div>
                </div>
                <div class="modal-body mx-auto">
                    <!-- Map div + importing the Bing Maps JS -->
                    <div id='map' style='position: relative; width: 40vw; height: 50vh;'></div>
                    <?php
                    include '../config.php';

                    print("<script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?key=$bing_API_key' async defer></script>");
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-target="#mapModal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal to edit the data -->
    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalTitle">Add/Edit data</h5>
                    <button type="button" class="close" data-dismiss="modal" data-target="#dataModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="dataForm" method="POST" action=""></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-target="#dataModal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- The container that contains the table + the controls. Display:none to avoid a FOUC -->
    <div id="table_container" class="container mt-3 table-responsive" style="display: none;">
        <div class="row" style="padding: 5px 0px;">
            <div class="col">
                <!-- Choose table dropdown. Every item in the dropdown is a href to the page with ?table=tablename. -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="table-select-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Choose table
                    </button>
                    <div class="dropdown-menu" id="table-select" aria-labelledby="dropdownMenuButton">
                        <?php
                        foreach ($tables as $table) {
                            print('<a class="dropdown-item" href=".?table=' . $table . '">' . $table . '</a>');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- The actual table -->
        <table id="table" class="table table-bordered table-striped table-dark table-hover">
        </table>
    </div>

    <script>
        // The function used to load the Bing map
        function loadMapScenario() {
            var lat = document.getElementById('latCoord').text
            var lng = document.getElementById('lngCoord').text

            var map = new Microsoft.Maps.Map(document.getElementById('map'), {
                center: new Microsoft.Maps.Location(lat, lng)
            });
            var pushpin = new Microsoft.Maps.Pushpin(map.getCenter(), {
                icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',
                anchor: new Microsoft.Maps.Point(12, 39)
            });
            map.entities.push(pushpin);

        }

        function getTable(baseUrl, table, tableId) {
            // Set xmlhttp
            xmlhttp = new XMLHttpRequest();
            var url = baseUrl + '/api/get.php';
            xmlhttp.open("POST", url, true);
            xmlhttp.setRequestHeader("Content-type", "application/json");
            // When the state change then execute a function
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    const res = JSON.parse(xmlhttp.responseText).values // Turn the response into an object
                    const usedTable = <?php print("\"$getTable\""); ?>;
                    var cols = [{
                        data: 'buttons',
                        title: '<button class="btn btn-success"><i class="fas fa-plus-circle"></i></button>'
                    }]
                    var colDefs = [{
                        'targets': 0,
                        'orderable': false,
                        'data': 'ID',
                        'defaultContent': '<button class="btn btn-danger"><i class="far fa-trash-alt"></i></button><button class="btn btn-primary"><i class="fas fa-pen"></i></button>'
                    }]
                    $.each(Object.getOwnPropertyNames(res[0]), function(i, val) { // For each property, create a column
                        cols.push({
                            'data': val,
                            'title': val
                        })
                    })

                    if (usedTable == 'air_stations') {
                        cols.push({
                            'data': 'mapButtons',
                            'title': 'map'
                        })

                        colDefs.push({
                            'targets': -1,
                            'orderable': false,
                            'data': 'mapButtons',
                            "defaultContent": '<button class="btn btn-info"><i class="fas fa-map-marked"></i></button>'
                        })

                        var colReorder = {
                            "fixedColumnsLeft": 1,
                            "fixedColumnsRight": 1,
                        }
                    } else {
                        var colReorder = {
                            "fixedColumnsLeft": 1
                        }
                    }

                    $('#' + tableId).DataTable({
                        'order': [
                            [1, 'asc']
                        ],
                        'columns': cols,
                        'data': res,
                        'columnDefs': colDefs,
                        'colReorder': colReorder
                    });
                }
            }
            var parameters = {
                "table": table
            };
            xmlhttp.send(JSON.stringify(parameters));
        }

        function deleteRow(baseUrl, table, column, value) {
            var choose = confirm('Are you sure you want to delete the row with ' + column + ' ' + value + '?')
            if (choose) {
                var data = {
                    'table': table,
                    'column': column,
                    'value': value
                }

                $.post(baseUrl + '/api/delete.php', data, function() {
                    alert('Row deleted succesfully!');
                })
            }
        }

        function openAddModal() {
            <?php
            print("var baseUrl = \"$base_dir\"; var usedTable = \"$getTable\";");
            ?>
            // Set xmlhttp
            xmlhttp = new XMLHttpRequest();
            var url = baseUrl + '/api/columns.php';
            xmlhttp.open("POST", url, true);
            xmlhttp.setRequestHeader("Content-type", "application/json");
            // When the state change then execute a function
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    const res = JSON.parse(xmlhttp.responseText) // Turn the response into an object
                    const cols = res.columns

                    cols.forEach(function(element) {
                        var colName = element.COLUMN_NAME;

                        $('#dataForm').append('<div class="form-group">')
                        $('#dataForm').append('<label class="col-sm-4 col-form-label" style="color: black;">' + colName + '</label>')

                        if (element.DATA_TYPE == 'int' || element.DATA_TYPE == 'double') {
                            $('#dataForm').append('<input type="number" name="' + colName + '" />');

                        } else {
                            $('#dataForm').append('<input type="text" name="' + colName + '" />');
                        }
                        $('#dataForm').append('</div>')
                    })
                    $('#dataModal').modal({
                        show: true
                    })
                }
            }
            var parameters = {
                "table": usedTable
            };
            console.log(parameters)
            xmlhttp.send(JSON.stringify(parameters));
        }

        function openEditModal(data) {
            <?php
            print("var baseUrl = \"$base_dir\"; var usedTable = \"$getTable\";");
            ?>
            // Set xmlhttp
            xmlhttp = new XMLHttpRequest();
            var url = baseUrl + '/api/columns.php';
            xmlhttp.open("POST", url, true);
            xmlhttp.setRequestHeader("Content-type", "application/json");
            // When the state change then execute a function
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    const res = JSON.parse(xmlhttp.responseText) // Turn the response into an object
                    const cols = res.columns

                    cols.forEach(function(element) {
                        var colName = element.COLUMN_NAME;
                        $('#dataForm').append('<div class="form-group">')
                        $('#dataForm').append('<label class="col-sm-4 col-form-label" style="color: black;">' + colName + '</label>')
                        if (element.DATA_TYPE == 'int' || element.DATA_TYPE == 'double') {
                            
                            $('#dataForm').append('<input type="number" name="' + colName + '" value="' + data[colName] + '"/>');

                        } else {
                            $('#dataForm').append('<input type="text" name="' + colName + '" value="' + data[colName] + '"/>');
                        }
                        $('#dataForm').append('</div>')
                    })
                    $('#dataModal').modal({
                        show: true
                    })
                }
            }
            var parameters = {
                "table": usedTable
            };
            xmlhttp.send(JSON.stringify(parameters));
        }

        // When the document is ready, load DataTables and then show the table
        $(document).ready(function() {

            <?php
            include_once '../config.php';

            print("getTable('$base_dir', '$getTable', 'table')");
            ?>

            document.getElementById('table_container').style.display = 'block';
        });

        $('#table').on('click', 'button', function(e) {
            <?php
            print("var baseUrl = \"$base_dir\"; var usedTable = \"$getTable\";");
            ?>
            var table = $('#table').DataTable();
            var data = table.row($(this).parents('tr')).data();
            if ($(e.target).is('.btn-danger') || $(e.target).is('.fa-trash-alt')) {
                // If the pressed button is the red one or the icon pressed is the trash can one
                // then start the deleteRow function                                                     
                deleteRow($baseUrl, usedTable, Object.keys(data)[0], data[Object.keys(data)[0]])

            } else if ($(e.target).is('.btn-success') || $(e.target).is('.fa-plus-circle')) {
                // If the pressed button is the green one or the pressed icon is the plus one
                openAddModal()

            } else if ($(e.target).is('.btn-primary') || $(e.target).is('.fa-pen')) {
                // If the pressed button is the blue one or the pressed icon is the pen one
                openEditModal(data)
                console.log('Primary!')

            } else if ($(e.target).is('.btn-info') || $(e.target).is('.fa-map-marked')) {
                // If the pressed button is the light-blue one or the pressed icon is the map one
                // then make the baseUrl variable with the content of the PHP $base_dir var
                <?php
                print("var baseUrl = \"$base_dir\";");
                ?>
                // Get the city from the row where the button was pressed
                const city = table.row($(this).parents('tr')).data().city;

                // Set xmlhttp to make the POST request
                xmlhttp = new XMLHttpRequest();
                var url = baseUrl + '/api/getCoords.php';
                xmlhttp.open("POST", url, true);
                xmlhttp.setRequestHeader("Content-type", "application/json");
                // When the state changes then execute a function
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        const res = JSON.parse(xmlhttp.responseText)[0] // Turn the response into an object
                        document.getElementById('latCoord').text = res.latitude // Set those two texts as the vars
                        document.getElementById('lngCoord').text = res.longitude // so they can be read by the map

                        // Show the modal and set its title
                        var modal = $('#mapModal').modal({
                            'show': true
                        })
                        modal.find('.modal-title').text("Latitude: " + res.latitude + ' - Longitude: ' + res.longitude) // Set the modal title
                    }
                }

                // The parameters to be sent via post
                var parameters = {
                    "city": city
                };
                xmlhttp.send(JSON.stringify(parameters));
            } else {
                console.log(e.target)
            }
        })

        $('#mapModal').on('show.bs.modal', function(event) {
            loadMapScenario() // Load the map
        })

        $('#dataModal').on('hidden.bs.modal', function() {
            document.getElementById('dataForm').innerHTML = ''
        })
    </script>

    <!-- Footer -->
    <footer class="page-footer font-small bg-dark">
        <div class="container-fluid text-center">
            <a href="https://github.com/Knocks83">Website made by Luca Fenu for the graduation exam year 2019/2020</a>
        </div>
    </footer>

</body>

</html>