<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
				content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Captcha form for sending SMTP messages</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>
	.alert-message .alert-icon {
		width: 3rem;
	}
	.alert-message .close{
		font-size: 1rem;
		color: #a6a6a6;
	}
	.alert-primary .alert-icon {
		background-color: #b8daff;
	}
</style>
</head>
<body class="container ">

<div class="row " >
	<div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 offset-sm-1 offset-md-2 offset-lg-3 offset-xl-3">


<?php
// define globa variables and set to empty values
function def_var() {
	global  $nameErr, $emailErr, $phoneErr, $messageErr, $errors, $name, $email, $phone, $message, $captMes;

				$nameErr = $emailErr = $phoneErr = $messageErr = $errors = "";
				$name = $email = $phone = $message =  "";
    		$captMes ='Consider upper and lower case letters.';
}

// Beautiful error message display
function errorprinter($errormessage) {
    print <<<_HTML_
<div class="alert alert-warning alert-message d-flex rounded p-0 mt-2" role="alert">
    <div class="alert-icon d-flex justify-content-center align-items-center flex-grow-0 flex-shrink-0 py-3">
        <i class="fa fa-exclamation-triangle"></i>
    </div>
    <div class="d-flex align-items-center py-2 px-3">
        $errormessage
    </div>
    <a href="#" class="close d-flex ml-auto justify-content-center align-items-center px-3" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </a>
</div>	
_HTML_;
}

function bingomes($message) {
    print <<<_HTML_
<div class="alert alert-success alert-message d-flex rounded p-0 mt-2" role="alert">
    <div class="alert-icon d-flex justify-content-center align-items-center flex-grow-0 flex-shrink-0 py-3">
        <i class="fa fa-check"></i>
    </div>
    <div class="d-flex align-items-center py-2 pr-1">
        $message
    </div>
    <a href="#" class="close d-flex ml-auto justify-content-center align-items-center px-3" data-dismiss="alert">
        <i class="fa fa-times""></i>
    </a>
</div>  
_HTML_;
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

def_var ();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        ++$errors;
    } else {
        $name = test_input($_POST["name"]);
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
            $nameErr = "Only english letters and white space allowed";
            ++$errors;
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        ++$errors;
    } else {
        $email = test_input($_POST["email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            ++$errors;
        }
    }

    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
        ++$errors;
    } else {
        $phone = test_input($_POST["phone"]);
        // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
        if (!preg_match("/^[0-9]{1}\s{1}[0-9]{3}\s{1}[0-9]{3}\s{1}[0-9]{2}\s{1}[0-9]{2}$/",$phone)) {
            $phoneErr = "Invalid Phone number";
            ++$errors;
        }
    }

    if (empty($_POST["message"])) {
        $messageErr = "Hey User, you forgot to write a message";
        ++$errors;
    } else {
        $message = test_input($_POST["message"]);
    }


    // set the name of the cookie to get the captcha code from it,
    //  it should coincide with the corresponding. named in jcaptcha.php

    define('CAPTCHA_COOKIE', 'imgcaptcha_');
    // note: the `captcha` field is required
    if(empty($_POST['captcha']) || md5($_POST['captcha']) != @$_COOKIE[CAPTCHA_COOKIE])
    {$captMes = 'Invalid code from the image. Try again.';
        ++$errors; }
    else
        $captMes = 'Data captcha entered correctly!';

    if ($errors == 0) {
        $from = "yyyyy@gmail.com";
        $subject ="$name" . '. Telephone number: ' . "$phone";
        $headers = "From: $from\r\n Reply-to: $from\r\n Content-type: text/plain charset=utf-8\r\n";
        mail ($email, $subject, $message, $headers);
        bingomes("Message successfully sent to: " . "$email" . ". You can enter the following message to another recipient.");
        def_var();  // define global parametrs in php
    }

} else {
	echo '<h1 style="color: darkred">Hacking attempt</h1>';
}

/*
echo "<h2>Your Input:</h2>";
echo $errors;
echo "<br>";
echo $name;
echo "<br>";
echo $email;
echo "<br>";
echo $phone;
echo "<br>";
echo $message;

*/

/* In the part of the HTML output of the form fields, validation of the entered data errors is carried out; correct data is saved; */
      ?>


<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" autocomplete="off" class="py-3">
	<h2>PHP Form Validation Example</h2>
    <?php
    if (($captMes == 'Invalid code from the image. Try again.') && ($errors == 1) ) {
        errorprinter('The captcha code is not correct. Please try again.');
    }
    ?>
	<p><b>* required field</b></p>

			<div class="form-group">

			<label for="name" class="control-label" >* Name:</label>
			<input type="text" name="name" id="name" class="form-control"  placeholder="Enter yo name" value="<?php if (empty($nameErr))  echo $name;?>">
          <?php if (!empty($nameErr))  errorprinter ($nameErr);?>
				<label for="email" class="control-label mt-2">* E-mail address</label>
				<input name="email" type="text" class="form-control" id="email" placeholder="Enter email" value="<?php if (empty($emailErr))  echo $email;?>">
          <?php if (!empty($emailErr))  errorprinter ($emailErr);?>
				<label for="phone" class="control-label mt-2">* Phone number: </label>
				<input type="text" name="phone" id="phone" class="form-control" placeholder="in format: 8 495 123 25 52"
							 value="<?php if (empty($phoneErr))  echo $phone;?>">
          <?php if (!empty($phoneErr)) errorprinter($phoneErr);?>
				<label for="message" class="mt-2">* Message: </label>
			 <textarea name="message" id="message" class="form-control" rows="5" cols="40"  placeholder="<?=$messageErr;?>" ><?=$message;?></textarea>
			</div>
	<div class="row">
		<div class="col-6">
			<label for="captcha" class="control-label mt-2">Enter the code from the image:</label>
			<img class="" data-toggle="tooltip" title="Click for new code" alt="Captcha code" src="jcaptcha.php"	 style="border: 1px	solid #000000" onclick="this.src='jcaptcha.php?id=' + (+new Date());">
		</div>
		<div class="col-6">
			<input type="text" name="captcha" id="captcha" class="form-control"  > <?=$captMes;?>
		</div>
	</div>
	<input type="submit" name="submit" value="Submit" class="btn btn-primary btn-lg d-block ml-auto">
</form>

		</div>
	</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
				integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
				integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
				integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


<!-- Autoclean form value -->
<script type="javascript">
    $('input[type="text"], textarea').val('');
    $('[data-toggle="tooltip"]').tooltip();
</script>
</body>
</html>



<?php
/**
 * Created by PhpStorm.
 * User: sharkoster
 * Date: 18.02.2019
 * Time: 19:15
 */