<!DOCTYPE html>
<html lang="it">

<head>
    <?php
    include '../config.php';

    $getTable = $default_table;
    if (isset($_GET['table'])) {
        $getTable = $_GET['table'];
    }
    print("<title>$getTable - Luca Fenu</title>");
    ?>
    <title>Stazioni - Luca Fenu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + deps -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/r-2.2.5/sc-2.0.2/datatables.min.css" />

    <!-- Datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/fh-3.1.7/r-2.2.5/sc-2.0.2/sp-1.1.1/datatables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/fh-3.1.7/r-2.2.5/sc-2.0.2/sp-1.1.1/datatables.min.js"></script>

    <link rel="stylesheet" href="../css/css.css">

    <style>
        body {
            background-color: #2a2a2e;
        }

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

        .paginate_button {
            border: 1px solid #343a40;
        }

        .paginate_button.disabled a {
            background-color: #2a2a2e !important;
            border: 0px !important;
        }

        .paginate_button.disabled {
            border: 1px solid #343a40 !important;
        }

        .modal-body {
            position: relative;
            overflow: hidden;
            max-height: 400px;
            padding: 15px;
        }

        body .modal-dialog {
            max-width: 100%;
            width: auto !important;
            display: inline-block;
        }

        .modal {
            z-index: -1;
            display: flex !important;
            justify-content: center;
            align-items: center;
        }

        .modal-open .modal {
            text-align: center;
            z-index: 1050;
        }

        .form-control {
            background-color: #343a40;
            border: 0px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="d-flex flex-grow-1">
            <span class="w-100 d-lg-none d-block">
                <!-- Hidden spacer to center the name on mobile --></span>
            <a class="navbar-brand" href="#">
                Luca Fenu
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
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Map</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <div style="display: none;"><a id="latCoord"></a><a id="lngCoord"></a></div>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-auto">
                    <!-- Map div + importing the Bing Maps JS (using the key "YourBingMapsKey" because it looks like it works) -->
                    <div id='myMap' style='position: relative; width: 30vw; height: 30vh;'></div>
                    <script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?key=YourBingMapsKey' async defer></script>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- The container that contains the table + the controls. Display:none because once the page has finished loading it gets changed -->
    <div id="table_container" class="container mt-3 table-responsive" style="display: none;">
        <div class="row">
            <div class="col">
                <!-- Choose table dropdown. Every item in the dropdown is a href to the page with ?table=tablename. -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Choose table
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php
                        include '../config.php';

                        $tables = [];
                        try {
                            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
                            // set the PDO error mode to exception
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $tablesTmp = $conn->query('SHOW TABLES;')->fetchAll(PDO::FETCH_NUM);

                            foreach ($tablesTmp as $table) {
                                $tables[] = $table[0];
                            }
                            unset($tablesTmp);

                            foreach ($tables as $table) {
                                print('<a class="dropdown-item" href=".?table=' . $table . '">' . $table . '</a>');
                            }
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- The actual table -->
        <table id="table" class="table table-bordered table-striped table-dark table-hover">
            <thead>
                <tr>
                    <?php
                    include '../config.php';
                    include '../functions.php';

                    try {
                        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
                        // set the PDO error mode to exception
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Check if the table actually exists, otherwise go back to the default one
                        if (!in_array($getTable, $tables)) {
                            $getTable = $default_table;
                        }

                        // Getting the columns of the table
                        $cols = $conn->query('SELECT `COLUMN_NAME`
                            FROM `INFORMATION_SCHEMA`.`COLUMNS`
                            WHERE `TABLE_SCHEMA`="' . $db_name . '" AND `TABLE_NAME`="' . $getTable . '"')->fetchAll();
                        foreach ($cols as $col) {
                            print('<th>' . $col['COLUMN_NAME'] . '</th>');
                        }
                        // If the table is air_stations, add an empty column that will contain the map buttons
                        if ($getTable == 'air_stations') {
                            print('<th></th>');
                        }
                        print('</tr>
                        </thead>
                        <tbody id="tbody">');
                        $rows = $conn->query('SELECT * FROM ' . $getTable)->fetchAll(PDO::FETCH_NUM);

                        // Fill the table
                        foreach ($rows as $row) {
                            print('<tr>');
                            foreach ($row as $index => $value) {
                                if ($index == 0) {
                                    print('<th scope="row">' . $value . '</th>');
                                } else {
                                    print('<td>' . $value . '</td>');
                                }
                            }
                            // If the table is air_stations, add the buttons
                            if ($getTable == 'air_stations') {
                                $city = $row[2];
                                $coords = $conn->query("SELECT air_cities.latitude, air_cities.longitude
                                FROM air_cities
                                WHERE air_cities.city = \"$city\"")->fetchAll(PDO::FETCH_ASSOC);
                                print('<td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal" data-lat="' . $coords[0]['latitude'] . '" data-lng="' . $coords[0]['longitude'] . '">Open map</button></td>');
                            }
                            print('</tr>');
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                    </tbody>
        </table>
    </div>

    <script>
        // The function used to load the Bing map
        function loadMapScenario() {
            var lat = document.getElementById('latCoord').text
            var lng = document.getElementById('lngCoord').text
            console.log(lat)

            var map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
                center: new Microsoft.Maps.Location(lat, lng)
            });
            var pushpin = new Microsoft.Maps.Pushpin(map.getCenter(), {
                icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',
                anchor: new Microsoft.Maps.Point(12, 39)
            });
            map.entities.push(pushpin);

        }

        // When the document is ready, load DataTables and then show the table
        $(document).ready(function() {
            $('#table').DataTable({
                colReorder: true,
            });
            document.getElementById('table_container').style.display = 'block';
        });
        $('#modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var lat = button.data('lat') // Extract info from data-* attributes.
            var lng = button.data('lng')
            document.getElementById('latCoord').text = lat // Saving the coords into hidden text fields, to get them
            document.getElementById('lngCoord').text = lng // from the modal
            var modal = $(this)
            modal.find('.modal-title').text("Latitude: " + lat + ' - Longitude: ' + lng) // Set the modal title
            loadMapScenario() // Load the map
        })
    </script>

    <!-- Footer -->
    <footer class="page-footer font-small bg-dark">
        <div class="container-fluid text-center">
            <a href="https://github.com/Knocks83">Creato da Luca Fenu per la prova di maturità 2019/2020</a>
        </div>
    </footer>

</body>

</html>