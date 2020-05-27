<!DOCTYPE html>
<html lang="it">

<head>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jq-3.3.1/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/r-2.2.5/sc-2.0.2/datatables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jq-3.3.1/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/cr-1.5.2/r-2.2.5/sc-2.0.2/datatables.min.js"></script>

    <link rel="stylesheet" href="../../css/css.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="d-flex flex-grow-1">
            <span class="w-100 d-lg-none d-block">
                <!-- spacer nascosto per centrare il nome su mobile --></span>
            <a class="navbar-brand" href="#">
                Luca Fenu
            </a>
            <!-- Bottone per navbar su mobile -->
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
                    <a class="nav-link" href="../../">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownAir" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Sensori Aria
                    </a>
                    <div class="dropdown-menu bg-dark" aria-labelledby="navbarDropdownAir">
                        <a class="dropdown-item" href="#">Stazioni</a>
                        <a class="dropdown-item" href="../Dati">Dati</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Milano">Dati aree pedonali e ZTL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Componenti">Cosa ho usato?</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-3 table-responsive">
        
        <table id="table_id" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <?php
                    include '../../config.php';
                    include '../../functions.php';

                    try {
                        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
                        // set the PDO error mode to exception
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $cols = $conn->query('SELECT `COLUMN_NAME`
                            FROM `INFORMATION_SCHEMA`.`COLUMNS`
                            WHERE `TABLE_SCHEMA`="esame" AND `TABLE_NAME`="air_stations"')->fetchAll();
                        foreach ($cols as $col) {
                            print('<th>' . $col['COLUMN_NAME'] . '</th>');
                        }
                        print('</tr>
                        </thead>
                        <tbody id="tbody">');
                        $vals = $conn->query('SELECT * FROM air_stations')->fetchAll();

                        foreach ($vals as $val) {
                            print('<tr>');
                            print('<th scope="row">' . $val['ID'] . '</th>');
                            print('<td>' . $val['name'] . '</td>');
                            print('<td>' . $val['city'] . '</td>');
                            print('<td>' . $val['height'] . '</td>');
                            print('</tr>');
                        }
                        /*
                        if (!isset($_GET['whatever'])) {
                        }*/
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                    </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                colReorder: true
            });
        });
    </script>
    <!-- Footer -->
    <footer class="page-footer font-small bg-dark">
        <div class="footer-copyright text-center py-3">
            <a href="https://github.com/Knocks83">Creato da Luca Fenu per la prova di maturità 2019/2020</a>
        </div>

    </footer>

</body>

</html>