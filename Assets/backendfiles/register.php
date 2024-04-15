<?php


function create_userid()
{
    $length = rand(4, 20);
    $number = "";
    for ($i = 0; $i < $length; $i++) {
        $new_rand = rand(0, 9);
        $number = $number . $new_rand;
    }

    return $number;
}

include './config.php';

$DB = new PDO("mysql:host = localhost;dbname = logisticscrm", "root", "");
if (!$DB) {
    die("could not connect to the data base.");
}

$userspage = "../../users.php";

if (($_REQUEST['action_type'] == "newuseradd")) {
    $arr['userid'] = create_userid();
    $condition = true;
    $error = "";

    while ($condition) {
        $query = "select id from logisticscrm.users where userid = :userid limit 1";
        $stm = $DB->prepare($query);
        if ($stm) {
            $check = $stm->execute($arr);
            if ($check) {
                $data = $stm->fetchAll(PDO::FETCH_ASSOC);

                if (is_array($data) && count($data) > 0) {
                    $arr['userid'] = create_userid();
                    continue;
                }
            }
        }
        $condition = false;
    }

    $arr['first_name'] = $first_name = $_POST['first_name'];
    $arr['last_name'] = $last_name = $_POST['last_name'];
    $arr['password'] = isset($_POST['user_password']) ? hash('sha1', $_POST['user_password']) : '';
    $arr['user_name'] = $user_name = $_POST['user_name'];
    $arr['contact_no'] = $contact_no = $_POST['contact_no'];
    $arr['usertype'] = $usertype = $_POST['usertype'];
    $arr['email'] = $email = $_POST['email'];
    $arr['status_access'] = $access_status = isset($_POST['status_access_checkbox']) ? $_POST['status_access_checkbox'] : 0;
    $arr['ip_address'] = $ipaddress = isset($_POST['ipaddress']) ? $_POST['ipaddress'] : 0;
    $redirect = $_POST['redirect'];
    $id = $_POST['id'];
    $msg = "";
    $success = 0;

    if (empty($id)) {
        $query = "insert into logisticscrm.users (userid, first_name, last_name, password, user_name, contact_no, usertype, email, status_access, ip_address) values (:userid, :first_name, :last_name, :password, :user_name,:contact_no, :usertype, :email, :status_access, :ip_address)";
        $stm = $DB->prepare($query);
        if ($stm) {
            $check = $stm->execute($arr);
            if (!$check) {
                $error = "could not save to the database";
            }

            if ($error == "") {
                $msg = "User Added Successfully";
                $success = 1;
            }
        }
    } else {
        $q = "select * from users where (user_name = '$user_name') and id != $id";
        $check_users = $mysqli->query($q) or die($mysqli->error);

        if ($check_users->num_rows > 0) {
            echo "User name or email already exists! Please try some other";
        } else {
            $msg = "User Updated Successfully";
            $success = 1;

            $mysqli->query("update users set first_name = '$first_name', last_name = '$last_name', user_name = '$user_name', contact_no = '$contact_no', usertype = '$usertype', email = '$email', status_access= '$access_status', ip_address= '$ipaddress' where id = '$id'");

            // header("location: $userspage");
        }
    }

    $data = [
        'msg' => $msg,
        'success' => $success,
        'data' => $data
    ];

    echo json_encode($data);
} elseif (isset($_POST['updateuser'])) {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    // $password = hash('sha1', $_POST['password']);
    $user_name = $_POST['user_name'];
    $contact_no = $_POST['contact_no'];
    $usertype = $_POST['usertype'];
    $email = $_POST['email'];
    $access_status = $_POST['status_access_checkbox'];
    $ipaddress = $_POST['ipaddress'];
    $q = "select * from users where (user_name = '$user_name') and id !=$id";
    // print($q . "<br><br>");
    $check_users = $mysqli->query($q) or die($mysqli->error);
    // $check_users = mysql_fetch_array($check_users);
    // print_r($check_users);

    if ($check_users->num_rows > 0) {
        print("Condition passed <br><br>");
        echo "User name or email already exist! Pease try some other";
        // return;
    } else {
        echo "success";

        $mysqli->query("update users set first_name = '$first_name', last_name = '$last_name', user_name = '$user_name', contact_no = '$contact_no', usertype = '$usertype', email = '$email', status_access= '$access_status', ip_address= '$ipaddress' where id = '$id'");

        //header("location: $userspage");
    }

    // $userprename = $mysqli->query("select user_name from users where id = '$id'");
    // $olduser_name = mysqli_fetch_assoc($userprename)['user_name'];
    // $updateusername = $mysqli->query("update users set user_name = '$user_name' where id = '$id'");
    // $usercheck = $mysqli->query("select user_name from users where user_name = '$user_name'");

    // $userpre_email = $mysqli->query("select email from users where id = '$id'");
    // $olduser_email = mysqli_fetch_assoc($userpre_email)['email'];
    // $update_email = $mysqli->query("update users set email = '$email' where id = '$id'");
    // $email_check = $mysqli->query("select email from users where email = '$email'");

    // if ($check_users->num_rows > 0) {
    //     echo "usename or email Already exists. Please try some other username or email.";
    //     $mysqli->query("update users set user_name = '$olduser_name', email = '$olduser_email' where id = '$id'");

    //     die;
    //     header("location: $userspage");
    // } else {
    //     echo "success";

    //     $mysqli->query("update users set first_name = '$first_name', last_name = '$last_name', user_name = '$user_name', contact_no = '$contact_no', usertype = '$usertype', email = '$email', status_access= '$access_status' where id = '$id'");

    //     header("location: $userspage");
    // }
} elseif (isset($_POST['password_rest'])) {
    $id = $_POST['id'];
    $password = hash('sha1', $_POST['user_password']);
    $pass1 = $_POST['user_password'];
    $pass2 = $_POST['confirm_password'];

    if ($pass1 !== $pass2) {
        echo "Password do not match";

        die;
        header("location: $userspage");
    } else {
        echo "success";

        $mysqli->query("update users set password  = '$password ' where id = '$id'");
        header("location: $userspage");
    }
// } elseif (($_REQUEST['action_type'] == 'newuser')) {
//     $arr['userid'] = create_userid();
//     $condition = true;

//     while ($condition) {
//         $query = "select id from logisticscrm.users where userid = :userid limit 1";
//         $stm = $DB->prepare($query);
//         if ($stm) {
//             $check = $stm->execute($arr);
//             if ($check) {
//                 $data = $stm->fetchAll(PDO::FETCH_ASSOC);

//                 if (is_array($data) && count($data) > 0) {
//                     $arr['userid'] = create_userid();
//                     continue;
//                 }
//             }
//         }
//         $condition = false;
//     }

//     $arr['first_name'] = $_POST['first_name'];
//     $arr['last_name'] = $_POST['last_name'];
//     $arr['password'] = hash('sha1', $_POST['user_password']);
//     $arr['user_name'] = $_POST['user_name'];
//     $arr['contact_no'] = $_POST['contact_no'];
//     $arr['usertype'] = $_POST['usertype'];
//     $arr['email'] = $_POST['email'];
//     // $redirect = $_POST['redirect'];


//     $query = "insert into logisticscrm.users (userid, first_name, last_name, password, user_name, contact_no, usertype, email) values (:userid, :first_name, :last_name, :password, :user_name,:contact_no, :usertype, :email)";
//     $stm = $DB->prepare($query);
//     if ($stm) {
//         $check = $stm->execute($arr);
//         if (!$check) {
//             $error = "could not save to the data base";
//         }

//         echo "success : User Added Successfully";

//         // if ($error == "") {
//         // header("location: ../../$redirect");
//         // die;
//         // }
//     }
} elseif (($_REQUEST['action_type'] == 'newusernamecheck')) {
    $username = $_GET['user_name'];
    $query = "select user_name from logisticscrm.users where user_name ='$username' limit 1";
    $usercheck = $mysqli->query($query);

    if ($usercheck->num_rows > 0) {
        echo "Username Already exists. Please try some other username";

        die;
    } else {
        echo "success";
    }
} elseif (($_REQUEST['action_type'] == 'newusernamecheck')) {
    $username = $_GET['user_name'];
    $query = "select user_name from logisticscrm.users where user_name ='$username' limit 1";
    $usercheck = $mysqli->query($query);

    if ($usercheck->num_rows > 0) {
        echo "Username Already exists. Please try some other username";

        die;
    } else {
        echo "success";
    }
} elseif (($_REQUEST['action_type'] == 'newuseremailcheck')) {
    $username = $_GET['user_name'];
    $query = "select user_name from logisticscrm.users where email ='$username' limit 1";
    $usercheck = $mysqli->query($query);

    if ($usercheck->num_rows > 0) {
        echo "Email Already exists. Please try some other Email";

        die;
    } else {
        echo "success";
    }
} elseif (($_REQUEST['action_type'] == 'newuserpasswordcheck')) {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 !== $pass2) {
        echo "Password Do not match. Please try again.";

        die;
    } else {
        echo "success";
    }
} elseif (($_REQUEST['action_type'] == 'oldusernamecheck')) {
    $id = $_GET['editid'];
    $user_name = $_POST['user_name'];

    $userprename = $mysqli->query("select user_name from users where id = '$id'");
    $olduser_name = mysqli_fetch_assoc($userprename)['user_name'];
    $updateusername = $mysqli->query("update users set user_name = '$user_name' where id = '$id'");
    $usercheck = $mysqli->query("select user_name from users where user_name = '$user_name'");


    if ($usercheck->num_rows > 1) {
        echo "usename Already exists. Please try some other username";
        $mysqli->query("update users set user_name = '$olduser_name' where id = '$id'");
        die;
    } else {
        echo "success";
        $mysqli->query("update users set user_name = '$olduser_name' where id = '$id'");
    }
} elseif (($_REQUEST['action_type'] == 'olduseremailcheck')) {
    $id = $_GET['editid'];
    $email = $_POST['user_name'];

    $userpre_email = $mysqli->query("select email from users where id = '$id'");
    $olduser_email = mysqli_fetch_assoc($userpre_email)['email'];
    $update_email = $mysqli->query("update users set email = '$email' where id = '$id'");
    $email_check = $mysqli->query("select email from users where email = '$email'");


    if ($email_check->num_rows > 1) {
        echo "Email Already exists. Please try some other Email";
        $mysqli->query("update users set email = '$olduser_email' where id = '$id'");
        die;
    }else {
        echo "success";
        $mysqli->query("update users set email = '$olduser_email' where id = '$id'");
    } 
} elseif (($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $mysqli->query("DELETE FROM users where id = '$id'");

    header("location: $userspage");
}
