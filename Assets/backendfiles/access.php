<?php

function access($usertype, $redirect = true)
{

    if (isset($_SESSION["ACCESS"]) && !$_SESSION['ACCESS'][$usertype]) {

        if ($redirect) {
            header("location: login.php");
            die;
        }

        return false;
    }

    return true;
}


$_SESSION["ACCESS"]["ADMIN"] = isset($_SESSION['myusertype']) && trim($_SESSION['myusertype']) == "admin";
$_SESSION["ACCESS"]["DISPATCHER"] = isset($_SESSION['myusertype']) && (trim($_SESSION['myusertype']) == "dispatcher" || trim($_SESSION['myusertype']) == "admin");
$_SESSION["ACCESS"]["TRACKER"] = isset($_SESSION['myusertype']) && (trim($_SESSION['myusertype']) == "tracker" || trim($_SESSION['myusertype']) == "admin");
$_SESSION["ACCESS"]["ALL"] = isset($_SESSION['myusertype']) && (trim($_SESSION['myusertype']) == "tracker" || trim($_SESSION['myusertype']) == "admin" || trim($_SESSION['myusertype']) == "dispatcher" || trim($_SESSION['myusertype']) == "Accountant");
$_SESSION['ACCESS']["ACCOUNTANT"] = isset($_SESSION['myusertype']) && (trim($_SESSION['myusertype']) == "Accountant"  || trim($_SESSION['myusertype']) == "admin");
$_SESSION["ACCESS"]["HR-ADMIN"] = isset($_SESSION['myusertype']) && (trim($_SESSION['myusertype']) == "hr-admin"  || trim($_SESSION['myusertype']) == "admin");
