<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="z-ua-compatible" content="ie=edge">
        <title>Google Market Data - Sandbox</title>
        <link rel="stylesheet" href="libraries/bootstrap/css/bootstrap.min.css">
    </head>
    <body>
        
    <?php
    //An alternate treatment using finance history
    /*
    Reference codes for finance history api
    
    http://www.google.com/finance/getprices?q=000001&x=SHA&i=86400&p=40Y&f=d,c,v,k,o,h,l&df=cpct&auto=0&ei=Ef6XUYDfCqSTiAKEMg
    http://www.google.com/finance/getprices
    ?q=000001 (stock symbol)
    &x=SHA (stock exchange symbol)
    &i=86400 (interval size in seconds (86400 = 1 day intervals)
    &p=40Y (period. a number followed by a ÔdÕ or ÔyÕ e.g. days or years. ex: 40Y = 40 years)
    &f=d,c,v,k,o,h,l (d=date/timestamp,c-close,v=volume,k=cday,o=opening price,h=high,l=low)
    &df=cpct (?)
    &auto=0 (?)
    &ei=Ef6XUYDfCqSTiAKEMg (?)
    */
    /*
    //create array of data that can't be retrieved via an api
    $marketsArray = array(
        "SHA:000001"=>array("exchange"=>"SHA","stock"=>"000001","name"=>"Shanghai","deviation"=>""),
        "INDEXNIKKEI:NI225"=>array("exchange"=>"INDEXNIKKEI","stock"=>"NI225","name"=>"Nikkei 225","deviation"=>0),
        "INDEXHANGSENG:HSI"=>array("exchange"=>"INDEXHANGSENG","stock"=>"HSI","name"=>"Hang Seng Index","deviation"=>0),
        "TPE:TAIEX"=>array("exchange"=>"TPE","stock"=>"TAIEX","name"=>"TSEC","deviation"=>0),
        "INDEXFTSE:UKX"=>array("exchange"=>"INDEXFTSE","stock"=>"UKX","name"=>"FTSE 100","deviation"=>0),
        "INDEXSTOXX:SX5E"=>array("exchange"=>"INDEXSTOXX","stock"=>"SX5E","name"=>"EURO STOXX 50","deviation"=>0),
        "INDEXEURO:PX1"=>array("exchange"=>"INDEXEURO","stock"=>"PX1","name"=>"CAC 40","deviation"=>0),
        "INDEXTSI:OSPTX"=>array("exchange"=>"INDEXTSI","stock"=>"OSPTX","name"=>"S&P TSX","deviation"=>0),
        "INDEXASX:XJO"=>array("exchange"=>"INDEXASX","stock"=>"XJO","name"=>"S&P/ASX 200","deviation"=>0),
        "INDEXBOM:SENSEX"=>array("exchange"=>"INDEXBOM","stock"=>"SENSEX","name"=>"BSE Sensex","deviation"=>0),
        "TLV:T25"=>array("exchange"=>"TLV","stock"=>"T25","name"=>"TA25","deviation"=>0),
        "INDEXSWX:SMI"=>array("exchange"=>"INDEXSWX","stock"=>"SMI","name"=>"SMI","deviation"=>0),
        "INDEXVIE:ATX"=>array("exchange"=>"INDEXVIE","stock"=>"ATX","name"=>"ATX","deviation"=>0),
        "INDEXBVMF:IBOV"=>array("exchange"=>"INDEXBVMF","stock"=>"IBOV","name"=>"IBOVESPA","deviation"=>0),
        "INDEXBKK:SET"=>array("exchange"=>"INDEXBKK","stock"=>"SET","name"=>"SET","deviation"=>0),
        "INDEXIST:XU100"=>array("exchange"=>"INDEXIST","stock"=>"XU100","name"=>"BIST100","deviation"=>0),
        "INDEXBME:IB"=>array("exchange"=>"INDEXBME","stock"=>"IB","name"=>"IBEX","deviation"=>0),
        "WSE:WIG"=>array("exchange"=>"WSE","stock"=>"WIG","name"=>"WIG","deviation"=>0),
        "TADAWUL:TASI"=>array("exchange"=>"TADAWUL","stock"=>"TASI","name"=>"TASI","deviation"=>0),
        "BCBA:IAR"=>array("exchange"=>"BCBA","stock"=>"IAR","name"=>"MERVAL","deviation"=>0),
        "INDEXBMV:ME"=>array("exchange"=>"INDEXBMV","stock"=>"ME","name"=>"IPC","deviation"=>0),
        "IDX:COMPOSITE"=>array("exchange"=>"IDX","stock"=>"COMPOSITE","name"=>"IDX Composite","deviation"=>0)
    );
    
    //return csv of all codes only
    $marketsSymbols = implode(',',array_keys($marketsArray));
    
    //loop through each market as key=>val
    foreach($marketsArray as $key =>$val){
        $stock = $val['stock'];
        $exchange = $val['exchange'];
        $symbol = $key;
        
        
        //This method would be way more accurate but I'm leaving it commented out because the actual assigment was to use the percentage provided which appears to be from the http://www.google.com/finance/info?client
        
        //get historical data
        $url = 'http://www.google.com/finance/getprices?q='.$stock.'&x='.$exchange.'&i=86400&p=5d&f=c&df=cpct&auto=0&ei=Ef6XUYDfCqSTiAKEMg';
        $obj = file_get_contents($url);
        
        if($obj){
            
            //explode to array and convert string values to int and filter by numeric...strips out instructional lines
            $lines = array_map('intval',array_filter(explode("\n", $obj),'is_numeric'));
            var_dump($lines);
            echo '<br/>';
            
            //sum up values. there's only a single value being returned on each line so no need to split lines up
            $linesTotal =  array_sum($lines);
            //var_dump($linesTotal);
            //echo '<br/>';
            
            //count lines
            $linesCount = count($lines);
            //var_dump($linesCount);
            //echo '<br/>';
             
            //find mean as float val 
            $mean = floatval($linesTotal/$linesCount);
            var_dump($mean);
            echo '<br/>';
            
            //loop through lines and add up 
            $janitor = array();
            $endSum = 0;
            foreach($lines as $line){
                //devide each value by the mean
                $val = $line-$mean;
                //square new values
                $endSum += $val*$val;
            }
            //var_dump($endSum);
            
            //devide endsum by item count -1, get sqrt and round
            $endSum = round(sqrt($endSum/($linesCount-1)),2);
            //var_dump($endSum);
            
            //update value in marketsArray
            $marketsArray[$symbol]['deviation'] = $endSum;
            //var_dump($marketsArray[$symbol]);
            
            
            //last value in lines...is todays
            $todaysClosing = end($lines);
            //var_dump($todaysClosing);
        }
        
    }//end each market
    */
        
    ?>
    <div class="container">
        
        <nav class="navbar navbar-default">
          <div class="container">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">World Markets</a>
            </div>
          </div>
        </nav>
        
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <!--tables for tabular data :) ... only-->
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Last Price</th>
                            <th>Change (%)</th>
                            <th>Standard Dev.</th>
                            <th>Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $marketsArray = array(
                        "SHA:000001"=>array("name"=>"Shanghai"),
                        "INDEXNIKKEI:NI225"=>array("name"=>"Nikkei 225"),
                        "INDEXHANGSENG:HSI"=>array("name"=>"Hang Seng Index"),
                        "TPE:TAIEX"=>array("name"=>"TSEC"),
                        "INDEXFTSE:UKX"=>array("name"=>"FTSE 100"),
                        "INDEXSTOXX:SX5E"=>array("name"=>"EURO STOXX 50"),
                        "INDEXEURO:PX1"=>array("name"=>"CAC 40"),
                        "INDEXTSI:OSPTX"=>array("name"=>"S&P TSX"),
                        "INDEXASX:XJO"=>array("name"=>"S&P/ASX 200"),
                        "INDEXBOM:SENSEX"=>array("name"=>"BSE Sensex"),
                        "TLV:T25"=>array("name"=>"TA25"),
                        "INDEXSWX:SMI"=>array("name"=>"SMI"),
                        "INDEXVIE:ATX"=>array("name"=>"ATX"),
                        "INDEXBVMF:IBOV"=>array("name"=>"IBOVESPA"),
                        "INDEXBKK:SET"=>array("name"=>"SET"),
                        "INDEXIST:XU100"=>array("name"=>"BIST100"),
                        "INDEXBME:IB"=>array("name"=>"IBEX"),
                        "WSE:WIG"=>array("name"=>"WIG"),
                        "TADAWUL:TASI"=>array("name"=>"TASI"),
                        "BCBA:IAR"=>array("name"=>"MERVAL"),
                        "INDEXBMV:ME"=>array("name"=>"IPC"),
                        "IDX:COMPOSITE"=>array("name"=>"IDX Composite")
                    );
                    
                    //this isn't the most accurate as it's only taking into account 2 data points. However, I'm using this one as it was requested specifically as far as I can tell. An alternate treatment is in comments above.
                    
                    foreach($marketsArray as $key => $val){
                        //get url for this symbol
                        $infoUrl = 'http://www.google.com/finance/info?client=ig&q='.$key;
                        //get contents, remove //
                        $infoObj = str_replace('//','',file_get_contents($infoUrl));
                        if($infoObj){
                            //clean control chars and convert to asc array
                            $infoObj = json_decode(utf8_encode($infoObj),true);
                            
                            /*"id": "338568" - internal google security id
                            ,"t" : "IDX" - stock symbol
                            ,"e" : "NYSEARCA" - exchange name
                            ,"l" : "19.72" - last trade price
                            ,"l_fix" : "19.72" - last trade ?
                            ,"l_cur" : "19.72" - last trade with currency
                            ,"s": "0" - last trade size
                            ,"ltt":"3:59PM EDT" - last trade time
                            ,"lt" : "May 4, 3:59PM EDT"  - last trade date time long
                            ,"lt_dts" : "2016-05-04T15:59:59Z" - last trade date time
                            ,"c" : "-0.31" - change
                            ,"c_fix" : "-0.31" - ? 
                            ,"cp" : "-1.55" - ? percentage
                            ,"cp_fix" : "-1.55" - ? 
                            ,"ccol" : "chr" - ? 
                            ,"pcls_fix" : "20.03" - previous close price
                            */
                            
                            $stock = $infoObj[0]['t'];
                            $exchange = $infoObj[0]['e'];
                            $code = $stock.':'.$exchange;
                            $cPercentage = $infoObj[0]['cp'];//this is the percentage, which is basically just comparing the change between the end of the last day, and the most recent transaction
                            $cAmount = $infoObj[0]['c'];
                            $prevClosePrice = floatval(str_replace(',','',$infoObj[0]['pcls_fix']));
                            $lastTradePrice = floatval(str_replace(',','',$infoObj[0]['l']));
                            $lastTradePriceCur = $infoObj[0]['l_cur'];
                            $lastTradeDate = $infoObj[0]['lt'];
                            
                            //mean
                            $mean = ($prevClosePrice+$lastTradePrice)/2;
                            
                            //subtract mean from each, and square
                            $newPrev = ($prevClosePrice-$mean)*($prevClosePrice-$mean);
                            $newLast = ($lastTradePrice-$mean)*($lastTradePrice-$mean);
                    
                            //sum/devide by 1 (so nothing) and sqrt
                            $final = round(sqrt($newPrev+$newLast),2);
                            
                            //output row
                            echo '<tr><td>'.$val['name'].'</td><td>'.$code.'</td><td>'.$lastTradePriceCur.'</td><td>'.$cAmount.' ('.$cPercentage.')</td><td>'.$final.'</td><td>'.$lastTradeDate.'</td></tr>';
                            
                        }
                    }
                    ?>
                    </tbody>
                </table> 
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="libraries/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>