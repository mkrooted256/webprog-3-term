<?php


$servername = "localhost";
$username = "mk-dev";
$password = "TJs9k2&Ex#84rR\$\$\$Dt3";
$dbname = "mkdev";

function p(...$args) {
    echo"<p>";
    foreach ($args as $arg) {
        echo $arg, " ";
    }

    echo "</p>";
}

function is_name($str) {
    $permitted = "zxcvbnmasdfghjklqwertyuiopZXCVBNMASDFGHJKLQWERTYUIOP-`'";
    for ($i=0; $i<strlen($str); $i++) {
        $okay = false;
        for ($j=0; $j<strlen($permitted); $j++) {
            if ($permitted[$j] == $str[$i]) {
                $okay = true;
                break;
            }
        }
        if (!$okay) return false;
    }
    return true;
}

if (isset($_GET['select']) and !empty($_GET['select'])) {
// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * from lab_mysql where `uid`=" . $_GET['select'] . "";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Name: " . $row["name-1"]. " " . $row["name-2"].
                "   - gradyear: " . $row["gradyear"] . " - phone: ". $row['phone'] . "<br>";
        }
    } else {
        echo "0 results";
    }

    $conn->close();

    exit(0);
}


$errors = array();
$csv = "key, val\n";

const fields = ["name-1", "name-2", "name-3", "gradyear", "phone", "email"];
const db_fields = ["name-1", "name-2", "gradyear", "phone"];

foreach (fields as $field) {
    if (!isset($_GET[$field])) {
        array_push($errors, "$field");
        continue;
    }
    $csv .= $field . "," . $_GET[$field] . "\n";
}
if (sizeof($errors) > 0) {
    header("Content-Type: text/plain");
    echo "Missing fields: ";
    foreach ($errors as $i => $field) {
        echo ($i == 0 ? "" : ", "), $field;
    }
    die();
}

if (!is_name($_GET['name-1'])) {
    array_push($errors, "name-1 contains invalid chars");
}
if (!is_name($_GET['name-2'])) {
    array_push($errors, "name-2 contains invalid chars");
}
if (!is_name($_GET['name-3'])) {
    array_push($errors, "name-3 contains invalid chars");
}
if ((int)$_GET['gradyear'] < 1950) {
    array_push($errors, "invalid gradyear: you're too old");
}
if((int)$_GET['gradyear'] > 2019) {
    array_push($errors, "invalid gradyear: you're too young");
}

if (sizeof($errors) > 0) {
    header("Content-Type: text/plain");
    echo "There are errors: \n";
    foreach ($errors as $err) {
        echo "* $err\n";
    }
    die();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO lab_mysql (`name-1`, `name-2`, gradyear, phone)
VALUES ('" . htmlspecialchars($_GET['name-1']) . "', '". htmlspecialchars($_GET['name-2']) . "'," .
    htmlspecialchars($_GET['gradyear']) . ",'" . htmlspecialchars($_GET['phone']) . "')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

if ($_GET['noui']) {
    header("Content-Type: text/plain");
    echo "--- Main info ------\n";
    echo "name:", htmlspecialchars($_GET['name-1']), "\n";
    echo "surname:", htmlspecialchars($_GET['name-2']), "\n";
    echo "fathername:", htmlspecialchars($_GET['name-3']), "\n";
    echo "gradyear:", (integer)$_GET['gradyear'], "\n";
    echo "gender:",  $_GET['gender'], "\n";
    echo "--------------------", "\n", "\n";

    echo "--- Contacts -------", "\n";
    echo "phone:",  htmlspecialchars($_GET['phone']), "\n";
    echo "email:",  htmlspecialchars($_GET['email']), "\n";
    echo "--------------------", "\n";



} else { ?>
<html lang="uk">
<head>
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro&display=swap" rel="stylesheet">
    <title>Registration handler</title>
    <style>
        body {
            font-family: "Source Code Pro", monospace;
        }
    </style>
</head>

<body>

<?php

p("--- Main info ------");
p("name:", htmlspecialchars($_GET['name-1']));
p("surname:", htmlspecialchars($_GET['name-2']));
p("fathername:", htmlspecialchars($_GET['name-3']));
p("gradyear:", (integer)$_GET['gradyear']);
p("gender:", $_GET['gender']);
p("--------------------");

p( "--- Contacts -------");
p( "phone:",  htmlspecialchars($_GET['phone']));
p( "email:",  htmlspecialchars($_GET['email']));
p( "--------------------");

?>
</body>
</html>

<?php } ?>