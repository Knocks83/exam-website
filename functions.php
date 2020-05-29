<?php

include 'config.php';

function updateAirDB(PDO $PDO)
{
    global $air_stationsUrl, $air_dataUrl;

    try {
        $stations = json_decode(file_get_contents($air_stationsUrl));
        $data = json_decode(file_get_contents($air_dataUrl));

        if ($stations === false || $data === false) {
            die('Error decoding the JSON!');
        }
    } catch (Exception $e) {
        die('Error! ' . print_r($e, true));
    }

    $PDO->exec("SET FOREIGN_KEY_CHECKS = 0; 
        TRUNCATE table air_cities;
        TRUNCATE table air_measurements; 
        TRUNCATE table air_sensors; 
        TRUNCATE table air_sensortypes; 
        TRUNCATE table air_stations; 
        SET FOREIGN_KEY_CHECKS = 1;
    ");

    // Generate temp table name
    $tempTableName = 'temp' . mt_rand(1000, 999999);

    $createTableStmt = $PDO->prepare("CREATE TABLE $tempTableName (
        IdSensore VARCHAR(30) PRIMARY KEY,
        NomeTipoSensore VARCHAR(30),
        UnitaMisura VARCHAR(30),
        IdStazione VARCHAR(30),
        NomeStazione VARCHAR(30),
        Quota VARCHAR(30),
        Provincia VARCHAR(30),
        Comune VARCHAR(30),
        Storico VARCHAR(30),
        DataStart VARCHAR(30),
        DataStop VARCHAR(30),
        lat DOUBLE,
        lng DOUBLE
    );");

    $createTableStmt->execute();
    unset($createTableStmt);

    // Long, shouldn't be like that but it's just a list.
    // Using positional values to not increase the length of the execute statement
    $addStmt = $PDO->prepare("INSERT INTO $tempTableName(IdSensore, NomeTipoSensore,UnitaMisura,IdStazione,NomeStazione,Quota,Provincia,Comune,Storico,DataStart,DataStop,lat,lng)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($stations as $station) {
        if (!isset($station->datastart)) {
            $station->datastart = null;
        }

        if (!isset($station->datastop)) {
            $station->datastop = null;
        }

        $addStmt->execute([
            $station->idsensore, $station->nometiposensore, $station->unitamisura, $station->idstazione, $station->nomestazione, $station->quota, $station->provincia,
            $station->comune, $station->storico, $station->datastart, $station->datastop, $station->lat, $station->lng
        ]);
    }
    unset($addStmt, $stations);

    $citiesStmt = $PDO->prepare("INSERT INTO air_cities(city, province, latitude, longitude)
        SELECT DISTINCT comune, provincia, lat, lng FROM $tempTableName
        GROUP BY comune, provincia");
    $citiesStmt->execute();

    $sensorTypesStmt = $PDO->prepare("INSERT INTO air_sensortypes(type_name, unit_of_measure)
        SELECT DISTINCT NomeTipoSensore, UnitaMisura FROM $tempTableName");
    $sensorTypesStmt->execute();

    $airStationsStmt = $PDO->prepare("INSERT INTO air_stations (ID, name, city, height)
        SELECT DISTINCT IdStazione, NomeStazione, Comune, Quota FROM $tempTableName");
    $airStationsStmt->execute();

    $airSensorsStmt = $PDO->prepare("INSERT INTO air_sensors (ID, type, station_ID, data_start, data_end)
        SELECT DISTINCT IdSensore, NomeTipoSensore, IdStazione, DataStart, DataStop FROM $tempTableName");
    $airSensorsStmt->execute();

    $PDO->exec("DROP TABLE $tempTableName");

    $measurementsStmt = $PDO->prepare("INSERT INTO air_measurements (sensor_ID, date, value, status) VALUES (:sensorid, :date, :value, :status)");
    foreach ($data as $measurement) {
        $measurementsStmt->execute(['sensorid' => $measurement->idsensore, 'date' => $measurement->data, 'value' => $measurement->valore, 'status' => $measurement->stato]);
    }
    unset($measurementsStmt, $data);
}
