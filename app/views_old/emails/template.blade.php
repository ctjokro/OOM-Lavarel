<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Email Template</title>
    </head>
    <body>
        <div style="background-color: #E9EDF1; text-align:center;
             width: 100%; padding:50px 0;">
            <table width="710" align="center" style=" padding: 0 50px 10px; text-align:left;  table-layout:fixed; font-family:Arial, Helvetica, sans-serif;background-color: #FFFFFF;
                   border: 1px solid #DDDDDD;  ">
                {{ View::make('emails.header')->render() }}
                <tr>
                    <td valign="top">
                        <!-- Begin Middle Content -->
                        <table width="100%">
                            <tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding: 10px 0 0;word-wrap: break-word;">
                                    <?php
                                    if (isset($firstname) && !empty($firstname)) {
                                        echo "Dear " . $firstname . ',';
                                    }
                                    ?>
                                    
                                </td>
                            </tr>
                            <?php if (isset($resetLink)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><?php echo $resetLink ?></p>
                                    </td>
                                </tr>
                            <?php }
                            ?>
                            <tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding: 10px 0 0;word-wrap: break-word;">
                                    <?php
                                    if (isset($text) && !empty($text)) {
                                        echo $text;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php if (isset($customerContent)) { ?>
                                <tr>
                                    <td>
                                        <?php echo $customerContent; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            if (isset($orderContent)) {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $orderContent; ?>
                                    </td>
                                </tr>
                            <?php }if (isset($email)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;">Email Address:</strong>  <?php echo $email; ?></p>
                                    </td>
                                </tr>
                            <?php }if (isset($username)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;">Username:</strong>  <?php echo $username; ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (isset($new_password)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;">New Password:</strong>  <?php echo $new_password; ?></p>
                                    </td>
                                </tr>
                            <?php } if (isset($password)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Password:</strong>  <?php echo $password; ?></p>
                                    </td>
                                </tr>
                            <?php } if (isset($name)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Name:</strong>  <?php echo $name; ?></p>
                                    </td>
                                </tr>
                            <?php }if (isset($cname)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Customer Name:</strong>  <?php echo $name; ?></p>
                                    </td>
                                </tr>
                            <?php } if (isset($location)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Location:</strong>  <?php echo $location; ?></p>
                                    </td>
                                </tr>
                            <?php }if (isset($email_address)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Email Address:</strong>  <?php echo $email_address; ?></p>
                                    </td>
                                </tr>
                            <?php } if (isset($contact_number)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Contact Number:</strong>  <?php echo $contact_number; ?></p>
                                    </td>
                                </tr>
                            <?php } if (isset($country_name)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Country:</strong>  <?php echo $country_name; ?></p>
                                    </td>
                                </tr>
                            <?php } if (isset($message2)) { ?>
                                <tr>
                                    <td style="font-size:12px; color:#000; line-height:18px;">
                                        <p style="margin:10px 0 0;"><strong style="width:150px;"> Message:</strong>  <?php echo $message2; ?></p>
                                    </td>
                                </tr>
                            <?php } ?>

                            {{ View::make('emails.footer')->render() }}