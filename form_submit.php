<?php
// Google reCAPTCHA API keys settings
$secretKey  = '6Lda9TEqAAAAALTLSV9VpOKyBBRNXgXWc_SyvjS3';

// Email settings
$recipientEmail = 'jhuku1984@gmail.com';

// If form data is submitted by AJAX request
if(isset($_POST['submit'])){
    // Define default response
    $response = array(
        'status' => 0,
        'msg' => 'Something went wrong, please try again after some time.'
    );

    // Retrieve value from the form input fields
    $email = trim($_POST['email']);

    $valErr = '';
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $valErr .= 'Please enter a valid email.<br/>';
    }

    if(empty($valErr)){
        // Validate reCAPTCHA response
        if(!empty($_POST['g-recaptcha-response'])){
            // Google reCAPTCHA verification API Request
            $api_url = 'https://www.google.com/recaptcha/api/siteverify';
            $resq_data = array(
                'secret' => $secretKey,
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            );

            // Initialize cURL request
            $curlConfig = array(
                CURLOPT_URL => $api_url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $resq_data,
                CURLOPT_SSL_VERIFYPEER => false
            );

            $ch = curl_init();
            curl_setopt_array($ch, $curlConfig);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $api_error = curl_error($ch);
            }
            curl_close($ch);

            // Decode JSON data of API response in array
            $responseData = json_decode($response);

            // If the reCAPTCHA API response is valid
            if(!empty($responseData) && $responseData->success){
                // Send email notification to the site admin
                $to = $recipientEmail;
                $subject = 'New Contact Request Submitted';
                $htmlContent = "
                    <h4>Contact request details</h4>
                    <p><b>Email: </b>".$email."</p>
                ";

                // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                // Sender info header
                $headers .= 'From:<'.$email.'>' . "\r\n";

                // Send email
                mail($to, $subject, $htmlContent, $headers);

                $response = array(
                    'status' => 1,
                    'msg' => 'Thank you! Your contact request has been submitted successfully.'
                );
            }else{
                $response['msg'] = !empty($api_error)?$api_error:'The reCAPTCHA verification failed, please try again.';
            }
        }else{
            $response['msg'] = 'Please check the reCAPTCHA checkbox.';
        }
    }else{
        $valErr = !empty($valErr)?'<br/>'.trim($valErr, '<br/>'):'';
        $response['msg'] = 'Please fill all the mandatory fields:'.$valErr;
    }

    // Return response
    echo json_encode($response);
}
?>
