<?php

function generateBarChart($id, $goal,$name, $ticks){

	foreach ($goal as $value) {
		$s1[] = $value['goal'];
		$s2[] = $value['actions'];
		# code...
	}
	$s1 = implode(',', $s1);
	$s2 = implode(',', $s2);
	$ticks = implode("','", $ticks);

?>
<script type="text/javascript">
	$(document).ready(function(){
  var s1 = [<?php echo $s1; ?>];
  var s2 = [<?php echo $s2; ?>];
  var ticks = ['<?php echo $ticks; ?>'];

  plot3 = $.jqplot('<?php echo $id; ?>', [s1, s2], {
    // Tell the plot to stack the bars.
    stackSeries: true,
    captureRightClick: true,
    seriesDefaults:{
      renderer:$.jqplot.BarRenderer,
      rendererOptions: {
          // Put a 30 pixel margin between bars.
          barMargin: 30,
          // Highlight bars when mouse button pressed.
          // Disables default highlighting on mouse over.
      },
      pointLabels: {show: true}
    },
     series:[
            {label:'Mål'},
            {label:'Resultat'},
        ],
    axes: {
      xaxis: {
          renderer: $.jqplot.CategoryAxisRenderer,
          ticks: ticks
      },
      yaxis: {
        // Don't pad out the bottom of the data range.  By default,
        // axes scaled as if data extended 10% above and below the
        // actual range to prevent data points right on grid boundaries.
        // Don't want to do that here.
        padMin: 0
      }
    },
    legend: {
      show: true,
      location: 'e',
      placement: 'outside'
    },  
     highlighter: {
        show: false
      },    
  });


});
</script>
<h3><?php echo $name; ?></h3>
 <div id="<?php echo $id; ?>"></div>
<?php
}

function generateChart($id,$dataArray,$yaxis,$xaxis,$caption = "ChartName", $mean = false, $generateSeries = 'generateUserSeries')
{
if(!is_array($dataArray)){ return; } //Hotfix to remove array errors, need to cleeen ths by declaring Array
global $base_url, $users, $user;
?>
<script type="text/javascript">
 $(function(){
      $.jqplot.config.enablePlugins = true;

    <?php

    //Create the JSON data used by jQPlot
    foreach ($dataArray  as $userId => $data)
    {
      	 $plotData[$userId] ="";
    	 $i = 0;
        	 foreach ($data as $datum => $value) 
        	 {
        	 	if($i > 0){
        	  	$plotData[$userId] .= ",";
        	  	}
        	  	if($mean){
				$plotData[$userId] .= "['".$datum."',".$value['mean']."]";
        	  	}else{
        	  	$plotData[$userId] .= "['".$datum."',".$value['value']."]";
        	  	}
        	  	$i++;
        	 }
    } 

        $bars = "";
    	foreach($plotData as $key => $val)
	    {
	    	$bars .="bar_".$key.",";
 			echo "bar_".$key."=[".$val."]; \n\r";
	    }
    ?>	
    $.jqplot('<?php echo $id; ?>', [<?php echo $bars; ?>], {
		legend:{
			show:false,
			location: 'nw',     // compass direction, nw, n, ne, e, se, s, sw, w.
			xoffset: 12,        // pixel offset of the legend box from the x (or x2) axis.
			yoffset: 12,
			fontSize: 25 },
		axes:{
		xaxis:{
		renderer: $.jqplot.CategoryAxisRenderer,
		rendererOptions:{
			// sortMergedLabels:true,
		tickRenderer:$.jqplot.CanvasAxisTickRenderer},
		tickOptions:{
				formatString:'%d',
                fontSize:'10pt',
                fontFamily:'Tahoma', 
                angle:-30
            }
        },
       yaxis:{
       	renderer: $.jqplot.LogAxisRenderer,
       	min: 0,
       	// tickInterval:'10',
       	tickOptions:{formatString:'%d'},
       	ticks: [<?php echo $yaxis; ?>],
		tickOptions:{
               // formatString:'%b %#d, %Y', 
                formatString:'%d',
                fontSize:'9pt', 
                fontFamily:'Tahoma'
            },
		}
	},
	seriesDefaults:{
      pointLabels: {show: false}
    },

		
		series:[
		<?php
		//we use the dataArray key to get the user id, in the same order as
		//we painted the charts
		call_user_func($generateSeries, $dataArray);
		?>
		
		],


	 	highlighter: {sizeAdjust: 7.5, tooltipAxes: 'y'},
        cursor: {show: false},
        legend: { show:true, location: 'e',
      placement: 'outside'}
    });
 

 jQuery("#<?php echo $id; ?> .jqplot-table-legend").css('right','-120px'); 
  });

</script> 
<h3><?php echo $caption; ?></h3>
<div id="<?php echo $id; ?>" style="height:400px; width:700px;"></div>

<?php
}

function getAgreementDataByUser($userId = 0,$filter ='')
{

	//Making this a bit more versitile by enabeling user merge
	if($userId == '0')
	{
		$userIdFilter = "OR userId > 0";
	}

	$sql = 
	"SELECT * FROM (SELECT userId,timeUnit, orderValue, @SUM := @SUM + orderValue AS SUM, @COUNT := @COUNT + 1 AS COUNT, @AVG := @SUM / @COUNT AS AVG
	FROM (
		 SELECT calender.timeUnit, IFNULL(orderValue, 0) AS orderValue, IFNULL(QUERY.userId,{$userId}) AS userId FROM(
		 (SELECT userId, acceptDate,  DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit, SUM(IF(invoiceFrequency = 'monthly', (price - spending) * agreementLengthMonths ,(price - spending))) as orderValue FROM agreements WHERE userId = {$userId} {$userIdFilter} GROUP BY timeUnit ORDER BY timeUnit) AS QUERY 
		 RIGHT JOIN (SELECT DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit FROM `agreements` WHERE acceptDate > 2000-00-00 GROUP BY timeUnit) AS calender ON (calender.timeUnit = DATE_FORMAT(QUERY.`acceptDate`, '%Y%v')) 
		)
	      )
	AS FIRST, (SELECT @COUNT := 0, @SUM := 0,@AVG :=0) AS extra ) AS filter
	WHERE 1  {$filter}";

	$result = mysql_query($sql);
	$total = 0;
	while ($row = mysql_fetch_array($result))
	{

		$userData[$row['timeUnit']] = array('value' => $row['orderValue'],'mean' => round($row['AVG']));
		$total += (int)$row['orderValue'];
	}
 //Check to se of we want to display user in stats (Only if sales > 0)
	if($total < 1)
		return null;
 	
 	return $userData;
}
function getRepetingsAgreementDataByUser($typeId = 0,$startDate,$endDate,$timmar = false)
{
		//Making this a bit more versitile by enabeling user merge
	if($typeId == '0')
	{
		$typeIdFilter = "OR userId > 0";
	}

 	 $startDate  =   date('Y-m-d',strtotime("-6 month",strtotime($startDate)));
	//Generate timeUnit Range
	
	$sql = "SELECT DATE_FORMAT(DATE,'%Y-%m-01') AS timeUnit FROM helperDates WHERE  DATE >= '{$startDate}' AND DATE <= '{$endDate}' GROUP BY timeUnit";
	$result = mysql_query($sql);

	//echo $sql;
	while ($row = mysql_fetch_array($result)) {
		$key   = date('Ym',strtotime($row['timeUnit']));
		$date1 = $row['timeUnit'];
		$date2 = date('Y-m',strtotime("+1 month",strtotime($row['timeUnit'])));

		//$userData[$row['timeUnit']]
		$sql = "SELECT agreementTypeId, agreements_available.name , SUM(price - spending) AS total, SUM(hours) as hours FROM agreements LEFT JOIN agreements_available ON agreements_available.id = agreementTypeId  WHERE agreements.invoiceFrequency = 'monthly' AND startDate <= '{$date2}' AND (endDate > '{$date2}' OR endDate = '0000-00-00 00:00:00') AND (agreementTypeId = {$typeId} {$typeIdFilter})";
		//echo $sql;
	    $result2 = mysql_query($sql);
	    $data = mysql_fetch_array($result2);
	    if($timmar){
		$userData[$key] = array('value' => $data['hours'],'mean' => round($row['AVG']));
	    }else{
	    $userData[$key] = array('value' => $data['total'],'mean' => round($row['AVG']));
	    } 

	    $total += (int)$data['total'];
	}

   if($total < 2)
    	return null;

	return $userData;
}

function getAgreementDataCountByUser($userId = 0,$filter ='')
{

	//Making this a bit more versitile by enabeling user merge
	if($userId == '0')
	{
		$userId = "{$userId} OR userId > 0";
	}

	$sql = 
	"SELECT * FROM (SELECT antal AS antal, userId,timeUnit, orderValue, @SUM := @SUM + antal AS SUM, @COUNT := @COUNT + 1 AS COUNT, @AVG := @SUM / @COUNT AS avg
	FROM (
		 SELECT IFNULL(antal,0) AS antal, calender.timeUnit, IFNULL(orderValue, 0) AS orderValue, IFNULL(QUERY.userId,{$userId}) AS userId FROM(
		 (SELECT userId, acceptDate,  DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit, orderValue , COUNT(*) AS antal FROM agreements WHERE userId = {$userId} GROUP BY timeUnit ORDER BY timeUnit) AS QUERY 
		 RIGHT JOIN (SELECT DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit FROM `agreements` WHERE acceptDate > 2000-00-00 GROUP BY timeUnit) AS calender ON (calender.timeUnit = DATE_FORMAT(QUERY.`acceptDate`, '%Y%v')) 
		)
	      )
	AS FIRST, (SELECT @COUNT := 0, @SUM := 0,@AVG :=0) AS extra ) AS filter
	WHERE 1  {$filter}";
	// echo $sql;
	$result = mysql_query($sql);
	$total = 0;
	while ($row = mysql_fetch_array($result))
	{
		$userData[$row['timeUnit']] = array('value' => $row['antal'],'mean' => round($row['avg']));
		$total += (int)$row['antal'];
	}
 //Check to se of we want to display user in stats (Only if sales > 0)
	if($total < 1)
		return null;
 	
 	return $userData;
}


function getAgreementDataByUnit($unitId = 0,$filter ='')
{

	//Making this a bit more versitile by enabeling user merge
	if($unitId == '0')
	{
		$unitIdFilter = "OR agreementTypeId > 0";
	}

	$sql = 
	"SELECT * FROM (SELECT agreementTypeId,timeUnit, orderValue, @SUM := @SUM + orderValue AS SUM, @COUNT := @COUNT + 1 AS COUNT, @AVG := @SUM / @COUNT AS AVG
	FROM (
		 SELECT calender.timeUnit, IFNULL(orderValue, 0) AS orderValue, IFNULL(QUERY.agreementTypeId,{$unitId}) AS agreementTypeId FROM(
		 (SELECT agreementTypeId , acceptDate,  DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit, SUM(orderValue) AS orderValue FROM agreements WHERE agreementTypeId = {$unitId} {$unitIdFilter} GROUP BY timeUnit ORDER BY timeUnit) AS QUERY 
		 RIGHT JOIN (SELECT DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit FROM `agreements` WHERE acceptDate > 2000-00-00 GROUP BY timeUnit) AS calender ON (calender.timeUnit = DATE_FORMAT(QUERY.`acceptDate`, '%Y%v')) 
		)
	      )
	AS FIRST, (SELECT @COUNT := 0, @SUM := 0,@AVG :=0) AS extra ) AS filter
	WHERE 1   {$filter}";

	$result = mysql_query($sql);
	$total = 0;
	while ($row = mysql_fetch_array($result))
	{

		$userData[$row['timeUnit']] = array('value' => $row['orderValue'],'mean' => round($row['AVG']));
		$total += (int)$row['orderValue'];
	}
 //Check to se of we want to display user in stats (Only if sales > 0)
	if($total < 1)
		return null;
 	
 	return $userData;
}

function getAgreementSpendingDataByUnit($unitId = 0,$filter ='')
{

	//Making this a bit more versitile by enabeling user merge
	if($unitId == '0')
	{
		$unitIdFilter = "OR agreementTypeId > 0";
	}

	$sql = 
	"SELECT * FROM (SELECT agreementTypeId,timeUnit, spending, @SUM := @SUM + spending AS SUM, @COUNT := @COUNT + 1 AS COUNT, @AVG := @SUM / @COUNT AS AVG
	FROM (
		 SELECT calender.timeUnit, IFNULL(spending, 0) AS spending, IFNULL(QUERY.agreementTypeId,{$unitId}) AS agreementTypeId FROM(
		 (SELECT agreementTypeId , acceptDate,  DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit, SUM(spending) AS spending FROM agreements WHERE agreementTypeId = {$unitId} {$unitIdFilter} GROUP BY timeUnit ORDER BY timeUnit) AS QUERY 
		 RIGHT JOIN (SELECT DATE_FORMAT(`acceptDate`, '%Y%v') AS timeUnit FROM `agreements` WHERE acceptDate > 2000-00-00 GROUP BY timeUnit) AS calender ON (calender.timeUnit = DATE_FORMAT(QUERY.`acceptDate`, '%Y%v')) 
		)
	      )
	AS FIRST, (SELECT @COUNT := 0, @SUM := 0,@AVG :=0) AS extra ) AS filter
	WHERE 1   {$filter}";

	$result = mysql_query($sql);
	$total = 0;
	while ($row = mysql_fetch_array($result))
	{

		$userData[$row['timeUnit']] = array('value' => $row['spending'],'mean' => round($row['AVG']));
		$total += (int)$row['spending'];
	}
 //Check to se of we want to display user in stats (Only if sales > 0)
	if($total < 1)
		return null;
 	
 	return $userData;
}


function getMeetingsDataByUser($userId = '0',$filter ='',$timeUnitField = 'creationDate')
{
	// Making this a bit more versitile by enabeling user merge
	if($userId == '0' )
	{
		$userId = "{$userId} OR fromUserId > 0";
	}

	$sql = 
	"SELECT * FROM (SELECT  DATE_FORMAT(`finishDate`, '%Y%v') as `finishDate`, fromUserId, timeUnit,meetings, @SUM := @SUM + meetings as sum, @COUNT := @COUNT +1 as count, @AVG:= @SUM / @COUNT as avg
	FROM (
		SELECT `finishDate`, `fromUserId`, DATE_FORMAT(`{$timeUnitField}`, '%Y%v') as timeUnit, COUNT(*) as meetings
		FROM `tasks` WHERE `fromUserId` = {$userId}  AND `taskType` = 5 AND creationDate > 2000-00-00 group by timeUnit
		)
	as first, (SELECT @COUNT := 0, @SUM := 0, @AVG := 0) as extra 
	group by timeUnit) AS filter WHERE 1 {$filter}";

	// echo $sql;
	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result))
	{
		$userData[$row['timeUnit']] = array('value' => $row['meetings'],'mean' => $row['avg']);
	}
 	
 	return $userData;	
}



function generateUserSeries($barArray)
{
			global $users;
			foreach ($barArray as $key => $value) {
			$users[00] = "Samtliga";
			?>
			{
			label:'<?php echo $users[$key]; ?>', 
			},
			<?php
		} 
}

function generateUnitSeries($barArray)
{			
	        $sql ="SELECT id, name FROM `agreements_available`";
	        $result = mysql_query($sql);
	        while($row = mysql_fetch_array($result))
	  		{
	  			$units[$row['id']]= $row['name'];
	  		}

			foreach ($barArray as $key => $value) {
			$units[00] = "Samtliga";
			?>
			{
			label:'<?php echo $units[$key]; ?>', 
			},
			<?php
		} 
}

function generateTaskSeries($barArray)
{			
	        $sql ="SELECT id, taskName FROM `taskTypes`";
	        $result = mysql_query($sql);
	        while($row = mysql_fetch_array($result))
	  		{
	  			$units[$row['id']]= $row['taskName'];
	  		}

			foreach ($barArray as $key => $value) {
			$units[00] = "Samtliga";
			?>
			{
			label:'<?php echo $units[$key]; ?>', 
			},
			<?php
		} 
}

function dateRange( $first, $last,$step = '+1 week', $format = 'Y-m-d' ) {
    
	$dates = array();
	$current = date($format,strtotime( $first ));
	$last    = date($format,strtotime( $last ));

	while( $current <= $last ) {
	$dates["Y-W"][date('Y-W',strtotime($current))] = date('Y-W',strtotime($current));
	$dates["YW"][date('YW',strtotime($current))] = date('YW',strtotime($current));
	$dates['Y-m-d'][date('Y-m-d',strtotime($current))] = date('Y-m-d',strtotime($current));
	$dates['Y-m'][date('Y-m',strtotime($current))] = date('Y-m',strtotime($current));
	$current = date($format,strtotime($step,strtotime($current)));
	}

	return $dates;
}

function getSellerList()
{
	$list   = array();
	$query  = "SELECT * FROM `users` WHERE rights = 'inhouse' OR rights = 'admin'";
	$result = mysql_query($query);
	$res 	= mysql_fetch_array($result);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
	    $list[$row["id"]]['name'] =  $row["firstName"]." ".$row["lastName"];
	    $list[$row["id"]]['id']	  =  $row["id"];
	    $list[$row["id"]]['tech'] =  $row["techPerson"];
	}
	unset($list[63]); //Removes Okänd Mottagare (uppgifter);
	return $list;
}

function getAgreementTypeList()
{
	$list   = array();
	$query  = "SELECT * FROM agreements_available WHERE 1 AND active = 'YES'";
	$result = mysql_query($query);
	$res 	= mysql_fetch_array($result);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
	    $list[$row["id"]]['name'] = $row["name"];
	}
	return $list;
}

function getSellerRow($moten){

	
	foreach ($moten  as $key => $val)
    {
      	 $row[$key] ="";
    	 $i = 0;
        	 foreach ($val as $key2 => $val2) 
        	 {
        	 	if($i > 0){
        	  	$row[$key] .= ",";
        	  	}
        	  	$row[$key] .= "['".$key2."',".$val2['antal']."]";
        	  	$i++;
        	 }
    } 
    return $row;
}

function getWeekNumber($date){
$date = strtotime($date); 
$week = (int)date('W', $date); 
return $week; 
}

function getDateRange(){

	$sql = "SELECT DATE_FORMAT(DATE,'%Y-%m-01') AS timeUnit FROM helperDates GROUP BY timeUnit";


}

/*
//
//
//
//
//
//
//
//
//
//
 */

function getSellerTelefon($fromDate,$toDate,$uid = false,$format="Y-W")
{
	$userStats = array();
	$sql = "SELECT toUserId as userId, finishDate as 'tUnit', finishDate FROM tasks WHERE taskType = '6' AND finishDate >= '".$fromDate."' AND finishDate <= '".$toDate." 23:59:59' AND delegatedToTask = 0  ORDER BY tUnit";
	$result    = mysql_query($sql);
	$range = dateRange($fromDate,$toDate);
	if (mysql_num_rows($result)) {
	  while($row = mysql_fetch_array($result))
	  {
	  	foreach ($range[$format] as $key => $value) {
	  		$timeUnit = date($format,strtotime($row['tUnit']));
	  		if($timeUnit == $key){
	  			$dateRows[$key][$row["userId"]]['antal'] += 1;
	  			$dateRows[$key][00]['antal'] += 1;
	  		}else{
	  			$dateRows[$key][$row["userId"]]['antal'] += 0;
	  		}
	  	}
	  }
	}

    //Restor data into user arrays
	foreach ($dateRows as $key => $dateRow) {
		foreach ($dateRows[$key] as $userid => $antal)
		{
			$userStats[$userid][$key] = $antal;

		}
	}

//Single put one user
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
   ksort($userStats);
   return $userStats;
}

function getSellerMoten($fromDate,$toDate,$uid = false,$format ="Y-W")
{
	$sql = "SELECT toUserId as userId, finishDate as 'tUnit', finishDate FROM tasks WHERE taskType = '5' AND finishDate >= '".$fromDate."' AND finishDate <= '".$toDate." 23:59:59' AND delegatedToTask = 0  ORDER BY tUnit";
	$result    = mysql_query($sql);
	$range = dateRange($fromDate,$toDate);
	if (mysql_num_rows($result)) {
	  while($row = mysql_fetch_array($result))
	  {
	  	foreach ($range[$format] as $key => $value) {
	  		$timeUnit = date($format,strtotime($row['tUnit']));
	  		if($timeUnit == $key){
	  			$dateRows[$key][$row["userId"]]['antal'] += 1;
	  			$dateRows[$key][00]['antal'] += 1;
	  		}else{
	  			$dateRows[$key][$row["userId"]]['antal'] += 0;
	  		}
	  	}
	  }
	}

    //Restor data into user arrays
	foreach ($dateRows as $key => $dateRow) {
		foreach ($dateRows[$key] as $userid => $antal)
		{
			$userStats[$userid][$key] = $antal;

		}
	}

//Single put one user
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
   ksort($userStats);
   return $userStats;
}

function getSellerAvtal($fromDate,$toDate,$uid = false,$format ="Y-W"){
	$sql = "SELECT userId, acceptDate, acceptDate as tUnit FROM `agreements` WHERE acceptDate >= '".$fromDate."' AND  acceptDate <= '".$toDate." 23:59:59' ORDER BY tUnit";
	$result    = mysql_query($sql);
	$range = dateRange($fromDate,$toDate);
	if (mysql_num_rows($result)) {
	  while($row = mysql_fetch_array($result))
	  {
	  	foreach ($range[$format] as $key => $value) {
	  		$timeUnit = date($format,strtotime($row['tUnit']));
	  		if($timeUnit == $key){
	  			$dateRows[$key][$row["userId"]]['antal'] += 1;
	  			$dateRows[$key][00]['antal'] += 1;
	  		}else{
	  			$dateRows[$key][$row["userId"]]['antal'] += 0;
	  		}
	  	}
	  }
	}

    //Restor data into user arrays
	foreach ($dateRows as $key => $dateRow) {
		foreach ($dateRows[$key] as $userid => $antal)
		{
			$userStats[$userid][$key] = $antal;

		}
	}

//Single put one user
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
   ksort($userStats);
   return $userStats;
}

function getSellerAvtalSpending($fromDate,$toDate,$uid = false,$format ="Y-W"){
	$sql = "SELECT userId,agreementTypeId, acceptDate,ordervalue,spending,invoiceFrequency, acceptDate as tUnit FROM `agreements` WHERE acceptDate >= '".$fromDate."' AND  acceptDate <= '".$toDate." 23:59:59' ORDER BY tUnit";
	$result    = mysql_query($sql);
	$range = dateRange($fromDate,$toDate);
	if (mysql_num_rows($result)) {
	  while($row = mysql_fetch_array($result))
	  {
	  	foreach ($range[$format] as $key => $value) {
	  		$timeUnit = date($format,strtotime($row['tUnit']));
	  		if($timeUnit == $key){
	  			$dateRows[$key][$row["userId"]]['antal'] += 1;
	  			$dateRows[$key][00]['antal'] += 1;
	  		}else{
	  			$dateRows[$key][$row["userId"]]['antal'] += 0;
	  		}
	  	}
	  }
	}

    //Restor data into user arrays
	foreach ($dateRows as $key => $dateRow) {
		foreach ($dateRows[$key] as $userid => $antal)
		{
			$userStats[$userid][$key] = $antal;

		}
	}

//Single put one user
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
   ksort($userStats);
   return $userStats;
}

function getSellerAvtalSpendingTotal($fromDate,$toDate,$uid = false){
	$sql = "SELECT userId, agreementTypeId, acceptDate, endDate, ordervalue, spending, invoiceFrequency FROM `agreements` WHERE acceptDate <= '".$toDate." 23:59:59'";
	$result    = mysql_query($sql);

	if (mysql_num_rows($result))
	{
	  while($row = mysql_fetch_array($result))
	  {
	  	if($row["invoiceFrequency"] ==""){

	  	}
	  	else if($row['acceptDate'] >= date('Y-m-d',strtotime($fromDate)) && $row['acceptDate'] <= date('Y-m-d',strtotime($toDate)) )
	  	{
		  	$userStats[$row["agreementTypeId"]][date('Ym',strtotime($row["acceptDate"]))]["action"]["antal"]+= (int)$row["spending"];
			//$userStats[00][$row["week"]]["action"]["antal"]+= $row["spending"];
	  	}
	  }
	}


	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
	return $userStats;
}



function getSellerAvtalMedel($fromDate,$toDate,$uid = false,$format ="Y-W"){


	$sql       ="SELECT `acceptDate`,`userId`, DATE_FORMAT(`acceptDate`, '%Y%v') AS `month`, sum(`ordervalue`) AS `sum` FROM `agreements` WHERE userId > 0 AND `acceptDate`> 2001-01-01  AND  acceptDate <= '".$toDate." 23:59:59' GROUP BY `userId`, `month` ORDER BY acceptDate";
    echo $sql;
    $result    = mysql_query($sql);
	$range = dateRange('2011-01-08',$toDate);
	$numberOfUnits = 1;
	if (mysql_num_rows($result)){
	  while($row = mysql_fetch_array($result))
	  {
		  	// $users[$row['userId']]['sum']   += $row['sum'];
		  	// $users[$row['userId']]['count'] +=1;
		  	// $users[$row['userId']][$row['month']]['thisMonth'] = $row['sum'];
		  	// $users[$row['userId']][$row['month']]['avrage']    = $users[$row['userId']]['sum'] / $users[$row['userId']]['count'];

		  	//Samtliga
		  	$users['all']['sum']   += $row['sum'];
		  	$users['all']['count'] ++;
		  	$users['all'][$row['month']]['count']     = $users['all']['count'];
		  	$users['all'][$row['month']]['thisMonth']+= $row['sum'];
		  	$users['all'][$row['month']]['avrage']    = $users['all']['sum'] / $users['all']['count'];
		  	print_r($users['all']);
		  	exit();
	  }
	}

	print_r($users['all']);
	exit();

//Single put one user
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
   ksort($userStats);

   return $userStats;
}


function getSellerAvtalRepeating($fromDate,$toDate,$uid = false){
	$sql = "SELECT userId,agreementTypeId, acceptDate,ordervalue,invoiceFrequency, WEEK(acceptDate) as week FROM `agreements` WHERE invoiceFrequency = 'monthly' AND acceptDate >= '".$fromDate."' AND  acceptDate <= '".$toDate." 23:59:59' ORDER BY week";

	$result    = mysql_query($sql);
	if (mysql_num_rows($result)){
	  while($row = mysql_fetch_array($result))
	  {
		$userStats[$row["agreementTypeId"]][$row["week"]]["action"]["antal"]+= $row["ordervalue"];
		$userStats[00][$row["week"]]["action"]["antal"]+= $row["ordervalue"];
	  }
	}
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
	return $userStats;
}


function getSellerTasksRepeatingHours($fromDate,$toDate,$uid = false){


	$sql = "SELECT  taskType, WEEK(creationDate) as week, `hours` FROM `tasks` WHERE `hours` > 0 AND repeatingMonthly = 'YES' AND `creationDate` >= '".$fromDate."' AND `creationDate` <= '".$toDate." 23:59:59' ORDER BY week";
	$result    = mysql_query($sql);
	if (mysql_num_rows($result)){
	  while($row = mysql_fetch_array($result))
	  {
		$userStats[$row["taskType"]][$row["week"]]["action"]["antal"]+= $row["hours"];
		$userStats[00][$row["week"]]["action"]["antal"]+= $row["hours"];
	  }
	}
	// if($uid){
	// 	$temp = $userStats;
	// 	unset($userStats);
	// 	$userStats[$uid] = $temp[$uid];
	// 	unset($temp);
	// }
	return $userStats;
}

function getSellerBokadeMoten($fromDate,$toDate,$uid = false,$format ="Y-W")
{
	$sql = "SELECT fromUserId as userId, creationDate as 'tUnit', finishDate FROM tasks WHERE taskType = '5' AND creationDate >= '".$fromDate."' AND creationDate <= '".$toDate." 23:59:59' ORDER BY tUnit";
	$result    = mysql_query($sql);
	$range = dateRange($fromDate,$toDate);
	if (mysql_num_rows($result)) {
	  while($row = mysql_fetch_array($result))
	  {
	  	foreach ($range[$format] as $key => $value) {
	  		$timeUnit = date($format,strtotime($row['tUnit']));
	  		if($timeUnit == $key){
	  			$dateRows[$key][$row["userId"]]['antal'] += 1;
	  			$dateRows[$key][00]['antal'] += 1;
	  		}else{
	  			$dateRows[$key][$row["userId"]]['antal'] += 0;
	  		}
	  	}
	  }
	}

    //Restor data into user arrays
	foreach ($dateRows as $key => $dateRow) {
		foreach ($dateRows[$key] as $userid => $antal)
		{
			$userStats[$userid][$key] = $antal;

		}
	}

//Single put one user
	if($uid){
		$temp = $userStats;
		unset($userStats);
		$userStats[$uid] = $temp[$uid];
		unset($temp);
	}
   ksort($userStats);
   return $userStats;
}

?>