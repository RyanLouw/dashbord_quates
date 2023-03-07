<?php
    // ini_set( 'display_errors', 1 );
    // ini_set( 'display_startup_errors', 1 );
    // error_reporting( E_ALL );

    require '../../globals/dbconn.inc';
    $today = date('m-d-Y');

    $currentSystemTime = $currt = date('H:i:s');
    $oneHourOld = $min05 = date('H:i:s', strtotime('-60 minutes'));
    $twoHoursOld = $min10 = date('H:i:s', strtotime('-120 minutes'));
    $threeHoursOld = $min20 = date('H:i:s', strtotime('-180 minutes'));
    
    $sql = "SELECT created_time FROM ivquotation WHERE created_date = date('$today');";
    $qry = $dbconn->query($sql);
    if ( $qry->num_rows > 0 ){
        $row = $qry->fetch_assoc();
        $lastupdate = $row['created_time'];
    }
    else {
        $lastupdate = 0;
    }
    // DEFINE VARS
        $pagename = "Quotes Dashboard";
        $maxcols = 6;
        $showinfo = null;
        $lastQuote = null;
        $totalQuotes = $arrayCounts = $invoiceCount = 0;
    // end: DEFINE VARS
    if ( isset($_GET['bc']) && !empty($_GET['bc']) ){
        $selectedBranchCode = $_GET['bc'];
      }
      else {
        $selectedBranchCode = 101;
    }
    if ( isset($_GET['do']) && $_GET['do'] == 'p' ){
        $refreshdo = "&do=".$_GET['do'];
        $do = 'p';
        $maxrows = 27;
      }
      else {
        $refreshdo = '&do=l';
        $do = 'l';
        $maxrows = 16; // 4 Cols - 19 = 6 Cols - 30 = 8 Cols
    }
    if ( isset($_GET['rt']) && $_GET['rt'] == 'y' ){
        $refnextpg = '&rt=y';
        $nextpage = 'overview.php';
      }
      else {
        $refnextpg = '';
        $nextpage = 'quoteDashboard.php';
    }
    if ( $lastupdate !== 0 ){

        $sql = "SELECT rep_code,quote_no,created_time,branch_code,salesman_name,invoice_no,created_date FROM ivquotation WHERE branch_code=? AND created_date=date(?) AND rep_code <> '' AND rep_code <> $selectedBranchCode AND rep_code <> 'BUY' AND rep_code <> 'IT' GROUP BY quote_no ORDER BY rep_code ASC,quote_no ASC;";

        $qry = $dbconn->prepare($sql);
        $qry ->bind_param('is',$selectedBranchCode,$today);
        $qry ->execute();
        $result = $qry->get_result();
        if ( $result->num_rows > 0 ){
            $x = 0;
            while ( $row = $result->fetch_assoc() ){
                
                
                if($row['invoice_no'] === ''){
                    $quoteBranchCode[$arrayCounts] = $row['branch_code'];
                    $quoteNumber[$arrayCounts] = $row['quote_no'];
                    $quoteCreationTime[$arrayCounts] = substr($row['created_time'],0,-3);
                    $quoteSalesmanName[$arrayCounts] = strstr($row['salesman_name'],' ', true);
                    $quoteCreationDate[$arrayCounts] = $row['created_date'];
                    $invNumber[$arrayCounts] = $row['invoice_no'];
                    if ( $quoteCreationTime !== $lastQuote ){
                        $lastQuote = $quoteCreationTime;
                        $totalQuotes++;
                    }

                    $salesRepNameForCount = $row['salesman_name'];
                    // echo $salesRepNameForCount;
                    $countSqlCmd = "SELECT salesman_name, count(*) as invoicescounts from ivquotation WHERE invoice_no <> '' AND salesman_name = ? group by rep_code;";
                    $nqry = $dbconn->prepare($countSqlCmd);
                    $nqry ->bind_param('s',$salesRepNameForCount);
                    $nqry ->execute();
                    $nresult = $nqry->get_result();
                    if ( $nresult->num_rows > 0 ){
                        while ( $nrow = $nresult->fetch_assoc() ){
                            $fokfok[$arrayCounts] = $nrow['invoicescounts'];
                            $arrayCounts ++;
                            $totInvs ++;
                        }
                    }



                }
                $arrayCounts++;
            }
            $showrecs = 1;
            $quoteBranchCode = array_chunk($quoteBranchCode,$maxrows);
            $quoteNumber = array_chunk($quoteNumber,$maxrows);
            $quoteCreationTime = array_chunk($quoteCreationTime,$maxrows);
            $quoteSalesmanName = array_chunk($quoteSalesmanName,$maxrows);
            $quoteCreationDate = array_chunk($quoteCreationDate,$maxrows);
            $invoiceNumbers = array_chunk($invNumber,$maxrows);
            $convertedInvoicesCount = array_chunk($fokfok,$maxrows);
            // $qcounts =array_chunk($Qfokfok,$maxrows);
            $countRecords = count($quoteCreationTime);
            $countInvs = count($convertedInvoicesCount);
        }
        else {
            $showrecs = 0;
            $countRecords = 1;
        }

        if ( $countRecords > $maxcols ){
            $scroll = 1;
            $screenScrollTime = $countRecords * 15;
        }
        else {
            $scroll = 0;
            $screenScrollTime = '60';
        }
        $refreshsctm = $screenScrollTime / 2 + 2;

        $thisUrl = 'https://sacmarketing.co.za/branches/quotes/'.$nextpage.'?bc='.$selectedBranchCode.$refreshdo.$refnextpg;
        header("Refresh:$refreshsctm; url=$thisUrl");
    }
    else {
        $thisUrl = 'https://sacmarketing.co.za/branches/quotes/'.$nextpage.'?bc='.$selectedBranchCode.$refreshdo.$refnextpg;
        header("Refresh:5; url=$thisUrl");
    }
?>
<html>
    <head>
        <title><?php echo "$selectedBranchCode $pagename"; ?></title>
        <!--meta http-equiv="refresh" content="<?php echo $screenScrollTime; ?>; url=https://sacmarketing.co.za/wh/<?php echo $nextpage; ?>?bc=<?php echo $selectedBranchCode.$refreshdo.$refnextpg; ?>"-->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.js" integrity="sha256-DrT5NfxfbHvMHux31Lkhxg42LY6of8TaYyK50jnxRnM=" crossorigin="anonymous"></script>
        <style>
            /* these are for colomn amonut */
            table{font-size:1.8vw} /* 1.3vw ofr 6 cols */
            .item1,.item2{width:18vw} /* 16.6vw for 6 cols */

            body{margin:0;padding:0;background-color:#000;color:#fff;font-family:Arial,Helvetica,sans-serif;white-space:nowrap;overflow:hidden;height:99% !important;}
            *::-webkit-scrollbar {display:none;-ms-overflow-style:none;scrollbar-width:none}
            header{position:fixed;font-size:8vw;color:#666;text-align:center;width:100vw;top:50%;-ms-transform:translateY(-50%);transform:translateY(-50%);z-index:-1}
            #heading{padding:0 16px 2px;border-bottom:2px solid #888;font-size:2.5vw;width:100%}
            section{display:inline-block;width:calc(33% - 16px);font-weight:600}
            article{margin:0;padding:0;height:calc(96% - 2px);overflow-x:auto;overflow-y:scroll;z-index:9}
            article div div{margin:0;padding:0;height:100%;display:inline-block;border-right:2px solid #888;vertical-align:middle;z-index:9}
            article div div:last-child{border:0;z-index:9;border-right:2px solid #888;height:100%}
            table{width:100%;z-index:9}
            td{padding:1px 4px;}
            td.ctr{text-align:center;}
            td.lhs{text-align:left;}
            .txt-rhs{text-align:right;}
            .txt-ctr{text-align:center;}
            .page{color:#fff}      /* Qoute count Number Colour */
            /*.invoiceColour{color:#47cc4e}*/
            .invoiceColour{color:#47cc4e}
            .slip{color:#ffffff}    /* Quote Number Colour */
            .slsh{color:#00ffff}
            .time{color:#00ffff}    /* Time Colour */
            .red{color:#ff0000} 
            .txtblk{color:#000}     /* Name Font Colour */
            .bold{font-weight:780}
            .bb{background-color: #424242}  /* Gray Back Ground Colour */
            .br{background-color: #f007}    /* Red Back Groud Colour */
    		.by{background-color: #ff07}    /* Yellow Back Ground Colour */
            .bg{background-color: #0f07}
            .bgw{background-color: #aaa9}   /* Name Back Ground Colour*/
            .topbrdr{border-top:2px solid #888}
            .nodata{display:block;color:#00ffff;font-weight:600;width:100%;text-align:center;font-size:2.5vw;margin-top:5vw}
            .item1 {display:inline-block;height:100%;vertical-align:top;}

            <?php if ( $scroll === 1 ){ ?>
                .marquee {width:100%;height:100%;margin:0 auto;overflow:hidden;white-space:nowrap;}
                .marquee-content {display:inline-block;animation:marquee <?php echo $screenScrollTime; ?>s linear 2s;}
                .item-collection-1 {position:relative;left:0%;animation:swap <?php echo $screenScrollTime; ?>s linear 2s;}
                @keyframes swap { 0%, 50% { left: 0%; } 50.01%, 100% { left: 100%; } }
                .item2 {display:inline-block;height:100%;vertical-align:top;}
                @keyframes marquee { 0% { transform: translateX(0) } 100% { transform: translateX(-100%) } }
                
            <?php } ?>
        </style>
    </head>
    <body>

        <?php
            if ( $lastupdate == 0 ){
                echo '<header>Please wait for next refresh</header>';
            }
            else {
                ?>
                <div id="heading">
                    <section><?php echo $pagename; ?></section>
                    <section class="txt-ctr">Quotes: <span id="tps" class="page"><?php echo $totalQuotes; ?></span></section>
                    <section class="txt-rhs">Updated: <span id="timenow" class="time"><?php echo date('H:i:s'); ?></span></section>
                </div>
                <?php
                if ( $showrecs === 1 ){
                    ?>
                    <article class="marquee">
                        <div class="marquee-content">
                            <?php
                            $qteCount = 0;
                            $lastQteNumber = $lastSalesmanName = null;
                            echo '<div class="item-collection-1">';
                            foreach( $quoteNumber as $key => $val){
                                echo '<div class="item1"><table width="100%">';
                                foreach ( $quoteNumber[$key] as $row => $rv ){
                                    $hlight = $trclass = null;
                                    $quoteNumbers = $quoteNumber[$key][$row];
                                    $quoteBranchCodec = $quoteBranchCode[$key][$row];
                                    $salesmanName = $quoteSalesmanName[$key][$row];
                                    $quoteDate = $quoteCreationDate[$key][$row];
                                    $totalinvoiceCountPerSalesmanName = $convertedInvoicesCount[$key][$row];
                                    // echo '<br> '.$totalinvoiceCountPerSalesmanName;
                                    // $qc = $qcounts[$key][$row];
                                    // echo $qc;
                                    if ( isset($quoteCreationTime[$key][$row]) && !empty($quoteCreationTime[$key][$row]) ){
                                        $qteTime = $quoteCreationTime[$key][$row];
                                        $quoteNumbers = $quoteNumbers;
                                        // $quoteNumbers = substr($quoteNumbers,0,8);
                                        if ( $quoteNumbers !== $lastQteNumber ){
                                            //$lastQteNumber = $quoteNumbers;
                                            $qteCount++;
                                            $showQuoteNumbers = $quoteNumbers;
                                            $showQuoteTimes = $qteTime;

                                        if ( $quoteDate !== $today){ $hlight = 'br'; }
                                        else if ( $qteTime < $oneHourOld && $qteTime >= $twoHoursOld ){ $hlight = 'bb'; }
                                        else if ( $qteTime < $twoHoursOld && $qteTime >= $threeHoursOld ){ $hlight = 'by'; }
                                        else if ( $qteTime < $threeHoursOld ){ $hlight = 'br'; }
                                        else { $hlight = ''; }
                                        }
                                        else {
                                            $showQuoteNumbers = '';
                                            $showQuoteTimes = '';
                                            
                                        }
                                        if ( $salesmanName !== $lastSalesmanName ){
                                            $lastSalesmanName = $salesmanName;
                                            if ( $salesmanName == '0 branches' ){ $salesmanName = ''; }
                                            echo '<tr><td colspan="3" class="topbrdr txt-ctr bgw"><span class="txtblk bold">'.$salesmanName.' - </span> <span class="invoiceColour">'. $totalinvoiceCountPerSalesmanName.'</span></td></tr>';
                                        }
                                        echo '<tr><td class="'.$trclass.' ctr"><span class="time '.$hlight.'">'.$showQuoteTimes.'&nbsp;</span></td><td class="lhs '.$hlight.$trclass.'"><span class="slip">'.$showQuoteNumbers.'</span></td></tr>';										
                                    }
                                }

                                echo '</table></div>';
                            }
                            echo '</div>';

                            if ( isset($scroll) && $scroll === 1 ){
                                $qteCount = 0;
                                $lastQteNumber = $lastSalesmanName = null;
                                echo '<div class="item-collection-2">';
                                foreach( $quoteNumber as $key => $val ){
                                    echo '<div class="item2"><table>';
                                    foreach ( $quoteNumber[$key] as $row => $rv ){
                                        $hlight = $trclass = null;
                                        $quoteNumbers = $quoteNumber[$key][$row];
                                        $quoteBranchCodec = $quoteBranchCode[$key][$row];
                                        $salesmanName = $quoteSalesmanName[$key][$row];
                                        $totalinvoiceCountPerSalesmanName = $convertedInvoicesCount[$key][$row];
                                        echo '<br> '.$totalinvoiceCountPerSalesmanName;

                                        $quoteNumbers = $quoteNumber[$key][$row];
                                        if ( isset($quoteCreationTime[$key][$row]) && !empty($quoteCreationTime[$key][$row]) ){
                                            $qteTime = $quoteCreationTime[$key][$row];
                                        
                                            $quoteNumbers = substr($quoteNumbers,0,8);
                                            if ( $quoteNumbers !== $lastQteNumber ){
                                                $lastQteNumber = $quoteNumbers;
                                                $qteCount++;
                                                $showQuoteNumbers = $quoteNumbers;
                                                $showQuoteTimes = $qteTime;
                                                $trclass = ' topbrdr';

                                                if ( $quoteDate !== $today){ $hlight = 'br'; }
                                                else if ( $qteTime < $oneHourOld && $qteTime >= $twoHoursOld ){ $hlight = 'bb'; }
                                                else if ( $qteTime < $twoHoursOld && $qteTime >= $threeHoursOld ){ $hlight = 'by'; }
                                                else if ( $qteTime < $threeHoursOld ){ $hlight = 'br'; }
                                                else { $hlight = ''; }
                                            }
                                            else {
                                                $showQuoteNumbers = '';
                                                $showQuoteTimes = '';
                                                $trclass = '';
                                            }
                                            if ( $salesmanName !== $lastSalesmanName ){
                                                $lastSalesmanName = $salesmanName;
                                                if ( $salesmanName == '0 branches' ){ $salesmanName = ''; }
                                                echo '<tr><td colspan="3" class="topbrdr txt-ctr bgw"><span class="txtblk bold">'.$salesmanName.' - </span> <span class="page">'. $totalinvoiceCountPerSalesmanName.'</span></td></tr>';
                                            }
                                            echo '<tr><td class="'.$trclass.' ctr"><span class="time '.$hlight.'">'.$showQuoteTimes.'&nbsp;</span></td><td class="lhs '.$hlight.$trclass.'"><span class="slip">'.$showQuoteNumbers.'</span></td></tr>';										
                                        }
                                    }
                                            echo '</table></div>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </article>
                    <?php
                }
                else {
                    echo '<span class="nodata">No records to display at this time</span>';
                }
            }
        ?>
    </body>
</html>