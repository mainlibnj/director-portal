<?php
include "../../config.php";

if (isset($_POST["username"]) && (!empty($_POST["username"]))) {
    $username = $_POST["username"];
    $sel_query = "SELECT * FROM users WHERE username='" . $username . "'";
    $result = mysqli_query($con, $sel_query);
    $numRow = mysqli_num_rows($result);
    // checks if username exists in db
    if ($numRow < 1) {
        // tell the template page to do something
       echo "<div style='color:red; font-weight:bold; text-align:center;'>That username doesn't exist.</div>";
        include "template.php";
    } else {
        echo "<div style='color:green; font-weight:bold; text-align:center;'>Please check your email.</div>";
        $expFormat = mktime(
            date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y")
        );
        $expDate = date("Y-m-d H:i:s", $expFormat);
        $key = md5(2418 * 2 + $username);
        $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
        $key = $key . $addKey;
        while ($row = mysqli_fetch_array($result)) {
            $email = $row["email"];
        }
        // this inserts the temporary key into the password_reset_temp database.
        mysqli_query($con,
            "INSERT INTO `password_reset_temp` (`username`, `key`, `expDate`)
VALUES ('" . $username . "', '" . $key . "', '" . $expDate . "');");

        // this sends the email
        $to = $email; // Send email to our user
        $subject = 'Forgot Password'; // Give the email a subject
        $message = '

    <p>Please click on the following link to reset your password.</p>

    https://portal.mainlib.org/login/forgot-password/reset-password/index.php?key=' . $key . '&username=' . $username . '&action=reset \n

    <p>The link will expire after 1 day for your security.</p>

<p>If you did not request this forgotten password email, no action
is needed, your password will not be reset. However, you may want to log into
your account and change your security password as someone may have guessed it.</p>

    '; // Our message above including the link

        $headers = 'From:data@mainlib.org' . "\r\n"; // Set from headers
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        mail($to, $subject, $message, $headers); // Send our email
    // redirects user to the success page
    header('Location: ../success/');
    }
}