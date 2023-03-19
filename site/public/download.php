<?php
try {
	$saveDir="storage";
	$id = uniqid();
	$timestamp = date("Y-m-d_H-i-s");
	$base_name = "address_label";
	$full_name = "{$base_name}_{$timestamp}_{$id}.pdf";
	$full_path = "$saveDir/$full_name";

	if(isset($_POST["printingPassword"]) && isset($_POST["labelOffset"])) {
		$trimmedPassword = trim($_POST["printingPassword"]);

		if(getenv("lblmkrapipw") != $trimmedPassword) {
			http_response_code(401);
		}

		$hideArray = array();
		if(isset($_POST["hideOID"])) { $hideArray[] = "OID"; }
		if(isset($_POST["hideSKU"])) { $hideArray[] = "SKU"; }
		if(isset($_POST["hideQTY"])) { $hideArray[] = "qty"; }
		if(isset($_POST["hideEmail"])) { $hideArray[] = "email"; }
		if(isset($_POST["hidePhone"])) { $hideArray[] = "phone"; }
		if(isset($_POST["hideAddress"])) { $hideArray[] = "address"; }
		$safePassword = urlencode($trimmedPassword);
		$safeOffset = is_numeric($_POST["labelOffset"]) ? $_POST["labelOffset"] : 0;
		$safeHide = !empty($hideArray) ? "&hide=" . join(",", $hideArray) : "";
		$url = "http://labelsite_app/?password=$safePassword$safeHide&offset=$safeOffset";
		if (file_put_contents($full_path, file_get_contents($url)) && file_exists($full_path))
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $base_name . '.pdf"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($full_path));
			flush();
			readfile($full_path);
			die();
		}
		else
		{
			error_log("File download failed");
			http_response_code(500);
		}
	}
} catch(Exception $e) {
	error_log($e->getMessage());
	http_response_code(500);
}
?>
