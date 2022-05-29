<?php

$csvFile = fopen(base_path($path), "r");

$firstline = true;

$arrNamaColumn = [];
while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
    if ($firstline) {
        foreach ($data as $idx => $namaColumn) {
            array_push($arrNamaColumn, $namaColumn);
        }
        $firstline = false;
        continue;
    }

    $arrCreate = [];
    foreach ($arrNamaColumn as $idx => $namaColumn) {
        $arrCreate[$namaColumn] = $data[$idx] == 'null' ? null : $data[$idx];
    }

    $model::create($arrCreate);
}

fclose($csvFile);
