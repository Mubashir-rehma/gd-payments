<?php

include 'config.php';

$mail->addAddress('rmubashir03@gmail.com', 'Mubashir');     // Add a recipient
// $mail->addReplyTo('accounting@digitalcontent.tech', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'This is new test 2';
$mail->Body    =  mail_content($mysqli, 753);
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}




function mail_content($mysqli, $id){
    $loadquery = "SELECT *
        FROM newload n
        LEFT OUTER JOIN truck_details AS t
        ON t.truck_id = n.truck_Number
        LEFT OUTER JOIN
        (select * from purchase_payment_updates PPU left outer join (select setting_payment_method as DSPM, setting_payment_status as DSPS, settings_id from settings where added_for='Driver') as settings on settings.settings_id = PPU.payment_status where exists (select * from (select pur_load_id, max(pur_payment_id) as PPid
        from purchase_payment_updates ppP
        GROUP BY pur_load_id
        ) as PPP where PPU.pur_payment_id = PPP.PPid)) PPU on n.id = PPU.pur_load_id
        where id='$id' and PPU.pur_load_id = '$id'
        ORDER BY id DESC";

    $loaddata = $mysqli->query($loadquery) or die($mysqli->error);


    $date = new DateTime();
    $week = $date->format("W");

    $rows = "";

    $rows .= '<div style="text-align: center; max-width: 450px; display: flex; align-items: center; margin: auto; justify-content: center;">';
        $rows .= '<div>';
            $rows .= '<p style="text-align: center; font-weight: 600;">Contract history statement of</p>';
            $rows .= '<div style="display: flex;">';
                $rows .= '<div style="text-align: left;">';
                    $rows .= '<p style="font-weight: 600; font-size: 12px; margin: 0; width: 155px;">GTMM Transportation LLC</p>';
                    $rows .= '<a style=" font-size: 12px; margin: 0; color: black; text-decoration: none;" href="tel:+17042881045">704 288 1045</a>';
                $rows .= '</div>';
                $rows .= '<div style="margin-left: 130px;">';
                    $rows .= '<img style="width: 150px;" src="http://gtmmtrans.com/images/logo.png" alt="" srcset="">';
                    $rows .= '<p style="margin: 0;">Issued Date: ' . $date->format("m-d-Y")  . '</p>';
                $rows .= '</div>';
            $rows .= '</div>';
            $rows .= '<p style="text-align: left;">Week: ' . $week . '</p>';
            $rows .= '<table style="padding: 10px; border: 1px solid #dadada; border-collapse: collapse;">';;
                $rows .= '<thead>';
                    $rows .= '<tr>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">#</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Drivers/ Trucks</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Pick Up</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Drop Off</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Paid On</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Dispatcher</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Total Rate</th>';
                    $rows .= '</tr>';
                $rows .= '</thead>';
                $rows .= '<tbody>';
                    $i = "";
                    foreach ($loaddata as $row) {
                        $i++;

                        count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []) > 0 ? $pickuplocation = unserialize($row['Pick_up_Location'])[0] : $pickuplocation = unserialize($row['Pick_up_Location']);
                        count((is_countable(unserialize($row['Destination']))) ? unserialize($row['Destination']) : []) > 0 ? $destination = unserialize($row['Destination'])[0] : $destination = unserialize($row['Destination']);

                        $rows .= '<tr>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $i . '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $row['truckDriver']  . '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $pickuplocation;
                                $rows .= '<br><span style="color: #8f8f8f; font-size: 12px;">';
                                    if (strtotime($row['pickupdate']) > 0) {
                                        $pickupdate = $row['pickupdate'];
                                        $forpickupdate = date("m-d-y", strtotime($pickupdate));
                                        $rows .= $forpickupdate . " ";
                                        $forpickuptime = date("h:i a", strtotime($pickupdate));
                                        $rows .= $forpickuptime;
                                    } else {
                                        $rows .= '';
                                    }
                                $rows .= '</span>';
                            $rows .= '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $destination;
                                $rows .= '<br><span style="color: #8f8f8f; font-size: 12px;">';
                                    if (strtotime($row['dropdate']) > 0) {
                                        $originalDate = $row['dropdate'];
                                        $newDate = date("m-d-y", strtotime($originalDate));
                                        $rows .= $newDate . " ";

                                        $forpickuptime = date("h:i a", strtotime($originalDate));
                                        $rows .= $forpickuptime;
                                    } else {
                                        $rows .= '';
                                    }
                                $rows .= '</span>';
                            $rows .= '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">';
                                if (strtotime($row['paid_on']) > 0) {
                                    $paid_on = $row['paid_on'];
                                    $rows .= $newDate = date("m-d-y", strtotime($paid_on));
                                } else {
                                    $rows .= '';
                                }
                            $rows .= '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $row['dispatcher']  . '</td>';
                           $rows .= ' <td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">$ ' . $row['Carier_Driver_Rate']  . '</td>';
                        $rows .= '</tr>';
                    } 
                $rows .= '</tbody>';
            $rows .= '</table>';
        $rows .= '</div>';
    $rows .= '</div>';

    return $rows;
}

?>