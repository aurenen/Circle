<?php
require_once 'config.php';
require_once 'PasswordHash.php';

// ini_set('display_errors',1);  
// error_reporting(E_ALL);
// mysqli_report(MYSQLI_REPORT_STRICT);

/*****************************************
    Database Related Functions
*****************************************/

$db; // global mysqli connection object

function db_connect() {
    global $db, $db_host, $db_user, $db_pass, $db_name;
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name, 3306);

    if($db->connect_errno > 0) {
        fail('MySQL db_connect ', $db->connect_error);
    }
}

function db_disconnect() {
    global $db;
    $db->close();
}

// function taken from phpass instructions/examples 
function fail($pub, $pvt = '') {
    $debug = true;
    $msg = $pub;
    if ($debug && $pvt !== '')
        $msg .= ": $pvt";
/* The $pvt debugging messages may contain characters that would need to be
 * quoted if we were producing HTML output, like we would be in a real app,
 * but we're using text/plain here.  Also, $debug is meant to be disabled on
 * a "production install" to avoid leaking server setup details. */
    exit("An error occurred ($msg).\n");
}

/*****************************************
    Misc Functions
*****************************************/

// technically depreciated, but just in case
function get_post_var($var) {
    $val = $_POST[$var];
    if (get_magic_quotes_gpc())
        $val = stripslashes($val);
    return $val;
}

function cleanPOST($form) {
    return htmlspecialchars($form);
}
function cleanSQL($val) {
    global $db;
    return $db->real_escape_string($val);
}

/*****************************************
    Ring Functions
*****************************************/

function getPrevLink($sid) {
    global $db;
    db_connect();

    $sql = "SELECT url FROM list WHERE id < ? ORDER BY id DESC LIMIT 1";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getPrevLink prepare', $stmt->error);
    if (!$stmt->bind_param('i', $sid))
        fail('MySQL getPrevLink bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getPrevLink execute', $stmt->error);
    if (!$stmt->bind_result($link))
        fail('MySQL getPrevLink bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getPrevLink fetch', $stmt->error);

    db_disconnect();
    return $link;
}

function getNextLink($sid) {
    global $db;
    db_connect();

    $sql = "SELECT url FROM list WHERE id > ? ORDER BY id ASC LIMIT 1";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getNextLink prepare', $stmt->error);
    if (!$stmt->bind_param('i', $sid))
        fail('MySQL getNextLink bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getNextLink execute', $stmt->error);
    if (!$stmt->bind_result($link))
        fail('MySQL getNextLink bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getNextLink fetch', $stmt->error);

    db_disconnect();
    return $link;
}

function getRandLink($sid) {
    global $db;
    db_connect();

    if ($result = $db->query("SELECT id FROM list")) {
        $max = $result->num_rows;
        $result->close();
    }
    else
        fail('MySQL getRandLink max', $db->error);

    $rand = rand(1, $max);
    if ($rand == $sid) {
        if ($rand == 1)
            $rand++;
        else
            $rand--;
    }

    $sql = "SELECT url FROM list WHERE id = ?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getNextLink prepare', $stmt->error);
    if (!$stmt->bind_param('i', $rand))
        fail('MySQL getNextLink bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getNextLink execute', $stmt->error);
    if (!$stmt->bind_result($link))
        fail('MySQL getNextLink bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getNextLink fetch', $stmt->error);

    db_disconnect();
    return $link;
}