<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$userData = DB::table('admins')
                        ->where('id', 1)
                        ->first();
$email = $userData->address;

$messageToSend = str_replace($toRepArray, $fromRepArray, $email);

echo $messageToSend;
?>


Hello [!firstname!],

Your password has been changed, successfully.
Your new password is:" [!resetLink!]" 
Kindly, change your password later on.