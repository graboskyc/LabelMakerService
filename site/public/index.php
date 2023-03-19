<?php

require("../vendor/autoload.php");

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>SCBPS LabelBot</title>
  </head>
  <body class="bg-dark">
	<div class="container py-5">
		<form class="form" id="label-printing-form" action="download.php" method="post">
		<div class="row align-items-center text-light">
			<div class="mx-auto col-sm-12 col-md-10 border d-grid p-5 bg-secondary shadow">
				<div class="p-5 text-center">
				    <h1 class="mb-3">SCBPS LabelBot</h1>
				</div>
				<div class="form-group">
					<label for="printingPassword">Printing Password (you can have your browser remember this)</label>
					<input type="password" class="form-control" name="printingPassword" id="printingPassword" placeholder="Enter The Code You Were Provided To Authorize Printing" required="true">
				</div>
				<div class="form-group">
					<label for="labelOffset">Label Offset</label>
					<input type="number" class="form-control" name="labelOffset" id="labelOffset" value="0" onchange="checkDecimal();">
				</div>
				<div class="form-check py-2">
					<input type="checkbox" class="form-check-input" id="hideOID" name="hideOID">
					<label class="form-check-label" for="hideOID">Hide Order ID</label>
				</div>
				<div class="form-check py-2 col-2">
					<input type="checkbox" class="form-check-input" id="hideSKU" name="hideSKU">
					<label class="form-check-label" for="hideSKU">Hide SKU</label>
				</div>
				<div class="form-check py-2">
					<input type="checkbox" class="form-check-input" id="hideQTY" name="hideQTY">
					<label class="form-check-label" for="hideQTY">Hide Quantity</label>
				</div>
				<div class="form-check py-2">
					<input type="checkbox" class="form-check-input" id="hideEmail" name="hideEmail">
					<label class="form-check-label" for="hideEmail">Hide Email</label>
				</div>
				<div class="form-check py-2">
					<input type="checkbox" class="form-check-input" id="hidePhone" name="hidePhone">
					<label class="form-check-label" for="hidePhone">Hide Phone</label>
				</div>
				<div class="form-check py-2">
					<input type="checkbox" class="form-check-input" id="hideAddress" name="hideAddress">
					<label class="form-check-label" for="hidePhone">Hide Address</label>
				</div>
				<button id="print-button" type="submit" class="btn btn-primary mt-5">Download For Printing</button>
			</div>
		</div>
		</form>
	</div>

	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script>
		$("#label-printing-form").submit(function(e) {
		    e.preventDefault();
		    var el = $("#print-button");
		    el.prop('disabled', true);
		    $.ajax({
			type: 'post',
			url: 'download.php',
			cache: false,
			data: $('#label-printing-form').serialize(),
			statusCode: {
			      401:function() { alert("Unauthorized, Please Check Your Password"); },
			      500:function() { alert("Something Went Wrong, If This Continues Seek Technical Support"); }
			},
			xhr: function () {
			    var xhr = new XMLHttpRequest();
			    xhr.onreadystatechange = function () {
				if (xhr.readyState == 2) {
				    if (xhr.status == 200) {
					xhr.responseType = "blob";
				    } else {
					xhr.responseType = "text";
				    }
				}
			    };
			    return xhr;
			},
			success: function (data) {
			    //Convert the Byte Data to BLOB object.
			    var blob = new Blob([data], { type: "application/octet-stream" });
			    var fileName = "address_labels.pdf";

			    //Check the Browser type and download the File.
			    var isIE = false || !!document.documentMode;
			    if (isIE) {
				window.navigator.msSaveBlob(blob, fileName);
			    } else {
				var url = window.URL || window.webkitURL;
				link = url.createObjectURL(blob);
				var a = $("<a />");
				a.attr("download", fileName);
				a.attr("href", link);
				$("body").append(a);
				a[0].click();
				$("body").remove(a);
			    }
			}
	            });
		    //Prevent people double clicking for a second
		    setTimeout(function(){el.prop('disabled', false); }, 1000);
		});
		function checkDecimal() {
			if(!$("#labelOffset")[0].value) {
				$("#labelOffset")[0].value = 0;
			} else {
				$("#labelOffset")[0].value = Math.floor($("#labelOffset")[0].value);
			}
		}
	</script>
  </body>
</html>
