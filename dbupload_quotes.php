 <?php
    ini_set( 'display_errors', 1 );
    ini_set( 'display_startup_errors', 1 );
    error_reporting( E_ALL );
    $debug = 0;
    require $_SERVER['DOCUMENT_ROOT'].'/globals/dbcona.inc';
    $dbcona->query( "DELETE FROM ivquotation;" );
    
    $display = "<p>Last Update: " . date( "Y-m-d H:i" ) . '<br>'; //Satrt Display
    
    $dateFrom = date( 'm-d-Y' ); //Today        
    $dateTo   = date( 'm-d-Y', strtotime( '-365 days' ) ); //Today in string format
    
    $display .= "<p>Date Selection is from: <b>$dateFrom</b> To: <b>$dateTo</b></p>";
    $url = 'http://sac-system.co.za:5650/Customers/Ivquotation?select=DATE='.$dateFrom;
    echo $url;
    $curl = curl_init(); //Initialize CURL
    curl_setopt_array( $curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_FAILONERROR => true, // Required for HTTP error codes to be reported via our call to curl_error($curl)
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                                     'Authorization: Basic U0FDLUNvbW1lcmNpYWwtUGFydHM6cHRtNVNiMjhaSmozSzZNbThKVEFCRXBa' 
                    ) 
    ) );
    
    $response     = curl_exec( $curl ); //Execute CURL Call
    $responseCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE ); //Catch CURL Reponse Code for Validation
    $json         = json_decode( $response, true ); //Parse JSON
    curl_close( $curl ); //Close CURL Connection

    $display .= "<table>"; //Start Table display
    $display .= "<tr><th>Quote Number</th><th>Salesman Name</th><th>Rep Code Nr</th><th>Branch Name</th><th>Branch Code</th><th>Created Date</th><th>Created Time</th></tr>"; //Table Headings
//    if(isset($json[ 'Ivquotation' ])) {

//     }
     $dataArray = $json[ 'Ivquotation' ]; //Array of Data from JSON Response
//     echo sizeof($dataArray);
//     echo count($dataArray);
    if ( $responseCode === 200 )
            {
                    foreach ( $dataArray as $rawValues )
                            {
                                    // Variable to use in App
                                    $branchName   = $rawValues[ 'br_name' ];
                                    $branchCode   = $rawValues[ 'branch' ];
                                    $quoteNumber  = $rawValues[ 'qte_no' ];
                                    $repCode      = $rawValues[ 'salesman_' ];
                                    $salesmanName = $rawValues[ 'rep_name' ];
                                    $dateCreated  = $rawValues[ 'date' ];
                                    $timeCreated  = $rawValues[ '$time' ];
                                    $invoiceNumber = $rawValues[ 'invoice_no' ];
                                    
                                    // Insert Record into IVQuotation Table
                                    $sql = "INSERT IGNORE INTO ivquotation (quote_no, invoice_no, salesman_name, rep_code, branch_name, branch_code, created_date, created_time) VALUES (?,?,?,?,?,?,?,?);";
                                    $qry = $dbcona->prepare( $sql );
                                    $qry->bind_param( 'ssssssss', $quoteNumber, $invoiceNumber, $salesmanName, $repCode, $branchName, $branchCode, $dateCreated, $timeCreated );
                                    // If there's an error with INSERT operation Display it
                                    if ( $qry->execute() )
                                            {
                                                    if ( $debug )
                                                            {
                                                                    debuging( $qry->error );
                                                            } //$debug
                                            } //$qry->execute()
                                    $display .= "<tr><td>$quoteNumber</td><td>$salesmanName</td><td><b>$repCode</b></td><td>$branchName</td><td>$branchCode</td><td>$dateCreated</td><td>$timeCreated</td></tr>"; //Populate Table with records              
                            } //$dataArray as $rawValues
                    $qry->close(); //Close your connection to MySQL
                    
                    // Debug the CURL Call
                    if ( $debug )
                            {
                                    debuging( $responseCode );
                                    debuging( $response );
                            } //$debug
            } //$responseCode === 200
    elseif ( $responseCode === 404 )
            {
                    if ( $debug )
                            {
                                    debuging( $responseCode );
                                    debuging( $response );
                            } //$debug
                    exit();
            } //$responseCode === 404
    else
            {
                    if ( $debug )
                            {
                                    debuging( $responseCode );
                                    debuging( $response );
                            } //$debug
                    exit();
            }
        //}
    $display .= '</table>';
//     echo $display;
    
    
    function debuging( $debugVar ) //Debug Function for CURL Call
            {
                    echo $debugVar;
            }
    exit();
?>