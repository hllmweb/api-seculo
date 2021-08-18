<?php

include_once("conexao.php");

$nome = $_POST['campo'];


$sql = "INSERT INTO dados (campo) VALUES ('$nome')";
$result = mysqli_query($conn, $sql);
