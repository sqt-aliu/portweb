<?php 
function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);

    return $length === 0 || 
    (substr($haystack, -$length) === $needle);
}

/* Ticker To Bllomberg */
function tickerBloomberg($ticker) {
    if ( endsWith( $ticker, ".HK" )) {
        $pieces = explode(".", $ticker);
        return (((string)((int)$pieces[0]))." HK");
    }
    if ( endsWith ( $ticker, ".JP" )) {
        return ($pieces[0]." JP");
    }
    return ($ticker);    
}

/* Ticker To Currency */
function tickerCurrency ($ticker) {
    if ( endsWith( $ticker, ".HK" )) {
        return "HKD";
    }
    if ( endsWith ( $ticker, ".JP" )) {
        return "JPY";
    }
    return "USD";
} 

/* Bloomberg To Ticker */
function bloombergTicker($bloomberg) {
    if ( endsWith( $bloomberg, " HK")) {
        $pieces = explode(" ", $bloomberg);
        return (str_pad($pieces[0], 4, "0", STR_PAD_LEFT).".HK");
    }
    return ($bloomberg);
}

/* BCA To Ticker */
function bcaTicker ($bca) {
    if ( endsWith( $bca, ":HK" )) {
        $pieces = explode(":", $bca);
        return (str_pad($pieces[0], 4, "0", STR_PAD_LEFT).".HK");
    }
    if ( endsWith ( $ticker, ":JP" )) {
        return ($pieces[0].".JP");
    }
    return $bca;
} 

/* Restricted List To Ticker */
function restrictedListTicker($code, $exch) {
    $ticker = $code.".".$exch;
    if ($exch == "HK") {
        $ticker = str_pad($code,4,"0",STR_PAD_LEFT).".".$exch;
    }
    return($ticker);
}
?>
