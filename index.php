<?php
    require_once 'stadus/stadus.php';
    $stadus = new stadus;
    $stadus->conn("localhost","root","" ,"shop");
    $stadus->stat(TRUE,TRUE);
    $info = $stadus->printStat();
	echo date('m/d/Y h:i:s');

    echo "Дата: ".$info['0']."<br>";
    echo "Кол-во посещений: ".$info['1'];