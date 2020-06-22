<?php
error_reporting(0);
$siteName = '';
$errorMsg = '';
if ($_GET['siteName']) {
    $sitePostName = $_GET['siteName'];
    $siteNameCheck = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $sitePostName);
    if ($siteNameCheck) {
        $siteName = $sitePostName;
    } else {
        header("Location: https://www.online-order.menu/demo/404.php");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Create a Subdomain using PHP and .Htaccess</title>


        <link href='https://fonts.googleapis.com/css?family=Montserrat%3A400%2C700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script>
            $(document).ready(function ()
            {

                $("#getStarted").click(function () {
                    var A = $("#subDomain").val();
                    if (/^[a-zA-Z0-9_-]{3,25}$/i.test(A))
                    {
                        var url = "https://" + A + ".online-order.menu";
                        window.location.replace(url);
                    } else
                    {
                        $("#errorMsg").html("Please give valid site name, no spaces or special characters.");
                    }

                    return false;
                });


            });
        </script>

    </head>
    <body>
        <div id="container">
<?php if (empty($siteName)) { ?>
                <h1>Create a Page</h1>


                <input type="text" id="subDomain" name="siteName" /> <span id="domainName">.online-order.menu</span>
                <div id="errorMsg"></div>

                <div><input type="button" value="Get Started" id="getStarted" ></div>


<?php } else { ?>
                <h1>Welcome to <span class="dark"><?php echo ucfirst($siteName); ?></span> Page </h1>

                https://<span class="dark"><?php echo ucfirst($siteName); ?></span>.online-order.menu
    <?php
}
?>

        </div>

    </body>
</html>