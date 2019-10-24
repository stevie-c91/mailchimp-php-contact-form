<?php // Process form submission and integrate with Mailchimp API
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle unfinished form. Remember to perform additional suitable validation here
    if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['message'])) {
        // Send user back to the form
        header('Location: /');
    }

	// Set default to handle if user wants to subscribe or not
	$subscribed = false;

    // Check user has accepted to sign up to the newsletter
    if (isset($_POST['newsletter'])) {
        // Set API credentials and build URL
        $data_center = 'DATA_CENTER_HERE';
        $audience_id = 'AUDIENCE_ID_HERE';
        $api_key = 'API_KEY_HERE';
        $url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $audience_id . '/members';

        // Build user details array to send
        $user_details = [
            'email_address' => $_POST['email'],
            'status' => 'subscribed'
        ];
        $user_details = json_encode($user_details);

		// Send POST request with cURL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'newsletter:' . $api_key);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $user_details);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($user_details)
		]);
		$result = curl_exec($ch);
		$result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

        if ($result_code === 200) {
			$subscribed = true;
      	}
	}

	// Send email functionality can go here. You can notify the user that they have been subscribed or not
	if ($subscribed) {
		echo 'You have been subscribed!';
	} else {
		echo 'Something went wrong';
	}

} else {
    // Send user back to the form if URL is accessed directly
    header("Location: /");
}
