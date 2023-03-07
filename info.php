<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://SAC-SYSTEM.CO.ZA:5650/Customers/subroutine/postDelivery',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
	"invoice_no": "AL47408",
	"user": "120",
	"vehicle": "",
	"completed": "1",
	"account": "161011210",
	"clientName": "CSK GASS DISTRIBUTERS",
	"branchCode": "161",
	"contactNumber": "0834833694",
	"locationLatitude": "N/A",
	"locationLongitude": "N/A",
	"driverLocationLatitude": "-25.8019182",
	"driverLocationLongitude": "27.8897027",
	"pickupTime": "2022-03-14 07:31:03",
	"deliveryTime": "2022-03-14 07:33:28",
	"signatureUrl": "signature/ST261099.png",
	"photoUrl": "uploads/AL47408"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic U0FDLUNvbW1lcmNpYWwtUGFydHM6cHRtNVNiMjhaSmozSzZNbThKVEFCRXBa',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
