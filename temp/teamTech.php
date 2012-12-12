<?
include("config.php");
#Kunder
include("header.php");
?>
<style>
#weekwrapper .week{ min-height: 120px; position: relative; margin-top: 20px; }
#weekwrapper .week-nr{position: absolute; top: -20px; left: 0px; height: 20px; width: 20px; font-weight: bold;}
#weekwrapper .day{float: left; width:20%; height: 70px; position: relative;  text-align: center;}
#weekwrapper .day .day-total{position: absolute; top:45%; left: 35%;}
#weekwrapper .day .procent{font-weight: bold;}
#weekwrapper .day .hours{}
#weekwrapper .day .uppgift
{
    border-top: 1px solid white;
}
#weekwrapper .day .uppgift p{
    /* Firefox */
display:-moz-box;
-moz-box-orient:horizontal;
-moz-box-pack:center;
-moz-box-align:center;

/* Safari and Chrome */
display:-webkit-box;
-webkit-box-orient:horizontal;
-webkit-box-pack:center;
-webkit-box-align:center;

/* W3C */
display:box;
box-orient:horizontal;
box-pack:center;
box-align:center;}
</style>
<div class="mainContainer">
<div class="mainbox taskList">
<h2><?=$pageTitle?></h2>
<?php

if(isset($_REQUEST['smille'])){

global $users;
// Handel the date interval
$todayDate = date('Y-m-d');
$dateOneMonthAdded = date("Y-m-d", strtotime($todayDate. "+1 month"));
$sellers = getSellerList();
$sellers[00] ="Samtliga";

    if (isset($_GET['from'])){
        $startDate = $_GET['from'];
    }else{
        $startDate =  $todayDate;
    }

    if (isset($_GET['to'])){
        $endDate = $_GET['to'];
    }else{
        $endDate = $dateOneMonthAdded;
    }
    
    $singleUid = false;
    if(isset($_GET['uid']) && is_numeric($_GET['uid'])){
        $singleUid = $_GET['uid'];
            $temp = $sellers;
            $sellers = array();
            $sellers[$singleUid] = $temp[$singleUid];
    }

  $phpTimeUnit ="YW";
  $mysqlTimeUnit = "%Y%v";

?>
<div class="date_header">
    <h2>Tidsperiod:</h2>
    <form method="get">
    Från: <input class="datepicker" name="from" value="<?php echo $startDate; ?>">
    Till: <input class="datepicker" name="to" value="<?php echo $endDate; ?>">
    <input type="hidden" name="smille" value="true">
    <input type="hidden" name="uid" value="<?php echo $_GET['uid']; ?>">
    <input type="submit" value="Hämta">
    </form>
</div>



<?php
if($singleUid)
{
    //Uppgift timmar per vecka och dag
    $sql = "SELECT taskType, title, toUserId, repeatingMonthly,startDate, hours, days,enddate,realEndDay,deadline, generatedByRepeatingTask  FROM (
    SELECT *, DATE_FORMAT(insDate,'%Y-%m-%d') AS startDate, ROUND(hours / 8) AS days, 
    DATE_ADD(DATE_FORMAT(insDate,'%Y-%m-%d'), INTERVAL ROUND(hours / 8) DAY) AS enddate,
    (SELECT DATE FROM helperDates WHERE DATE >= DATE_ADD(DATE_FORMAT(insDate,'%Y-%m-%d'), INTERVAL ROUND(hours / 8) DAY) AND isweekday = TRUE AND delegatedToTask = 0 LIMIT 1) AS realEndDay
    FROM tasks WHERE repeatingMonthly = 'no' AND finishDate ='0000-00-00 00:00:00') EXTENDED WHERE toUserId = {$singleUid} AND  realEndDay >= {$startDate} ";
  echo $sql ;
    $retults = mysql_query($sql);
    $weeksWorke = array();
    while ($row = mysql_fetch_array($retults))
    {
        // if($row['generatedByRepeatingTask'] > 0)
        //     continue;
        
        $sql = "SELECT * from helperDates WHERE date >= '{$row['startDate']}' AND date <= '{$row['realEndDay']}' AND isweekday = TRUE";
        $results2 = mysql_query($sql);
        //echo $sql;
        $totalHouers = $row['hours'];
        while ($row2 = mysql_fetch_array($results2))
        {
            $weeksWorke[$row2['wk']][$row2['date']]['hours'] += min(8,(int)$totalHouers);
            if($totalHouers >= 8)
                $totalHouers  = $totalHouers - 8;
        }

    }
}
?>

<div id="weekwrapper">
<?php

$sql = "SELECT *, DAYNAME(date) as name from helperDates WHERE date >= '{$startDate}' AND date <= '{$endDate}' AND isweekday = TRUE" ;
$results = mysql_query($sql);
$weeks = array();
while ($row = mysql_fetch_array($results)){
        $hours = max(0,$weeksWorke[$row['wk']][$row['date']]['hours']);
        $weeks[$row['wk']][$row['date']]= array('wk' => $row['wk'], 'name' => $row['name'], 'date' => $row['date'],'hours'=>$hours);
 } 

foreach ($weeks as $week => $days) {
?> 
<div class="week">
 <div class="week-nr">V.<?php echo $week ?></div>
    <?php 
        foreach ($days as $day) {
            ?>
            <div class="day"><?php echo $day['name'] ?>
               <?php 
               $color = "#90d970";
               $fullday = max(1,$day['hours']) / 8;

               if($fullday < 1){
                    $fullday = 0;
               }

               $betastning = ($day['hours'] / 8) * 100;
               if($betastning > 100){
                $color = "#DB2290";
               }elseif($betastning < 60){
                $color = "#ecf5f9";
               }

                $antalUpp = ceil(max(1,$day['hours'] / 8));
                $height = 100 / max(1,$antalUpp);
                $i = 0;
                while($i < $antalUpp){
                    ?>
                    <div class="uppgift" style="height:<?php echo $height; ?>%; background:<?php echo $color ?>"></div>
                    <?php
                    $i++;
                }
                ?>
                <div class="day-total">
                <p class="procent"><?php echo $betastning; ?> %</p>
                <p class="hours"><?php echo $day['hours']; ?> Timmar</p>
                </div>
             

            </div>
            <?php
        }
    ?>
</div>
    <?php
 
}
?>
</div>
<?php

// SELECT taskType, title, toUserId, repeatingMonthly,startDate, hours, days,enddate,realEndDay,deadline FROM (
// SELECT *, DATE_FORMAT(creationDate,"%Y-%m-%d") AS startDate, ROUND(hours / 8) AS days, 
// DATE_ADD(DATE_FORMAT(creationDate,"%Y-%m-%d"), INTERVAL 7 DAY) AS enddate,
// (SELECT DATE FROM helperDates WHERE DATE >= DATE_ADD(DATE_FORMAT(creationDate,"%Y-%m-%d"), INTERVAL 7 DAY) AND isweekday = TRUE LIMIT 1) AS realEndDay
// FROM tasks ) EXTENDED WHERE 1






    //SELECT DATE_FORMAT(creationDate,"%Y-%m-%d") AS startDate,
// hours, ROUND(hours / 8) AS days, 
// DATE_ADD(DATE_FORMAT(creationDate,"%Y-%m-%d"), INTERVAL 7 DAY) AS enddate,
// (SELECT DATE FROM helperDates WHERE DATE >= DATE_ADD(DATE_FORMAT(creationDate,"%Y-%m-%d"), INTERVAL 7 DAY) AND isweekday = TRUE LIMIT 1) AS realEndDay
// FROM tasks WHERE creationDate > 2012-12-01 AND toUserId = 59



//     $sql = "SELECT * FROM (SELECT *, (
// CASE WHEN (id = 14 OR id = 15 OR id = 16) THEN 'affiliet' 
//      WHEN (id = 10 OR id = 13 OR id = 20) THEN 'webb' 
//      WHEN (id = 22 ) THEN 'design'
//      WHEN (id = 1 OR id = 6 OR id = 7 OR id = 28 OR id = 9) THEN 'seo'
//      WHEN (id = 2 OR id = 3 OR id = 11) THEN 'ppc'
//      WHEN (id = 4 OR id = 5 ) THEN 'rm'
//      WHEN (id = 25 OR id = 26 OR id = 27) THEN 'social'
//      WHEN (id = 24) THEN 'copy'
//      WHEN (id = 23) THEN 'utbildning'
//      ELSE NULL END) AS omrade FROM `agreements_available` WHERE active = 'yes') omraden WHERE 1 AND omrade != 'null'";
//      
$sql = "
(SELECT units, GROUP_CONCAT(id) AS ids FROM (SELECT *, (
CASE WHEN (id = 1 OR id = 3) THEN 'seo' 
     WHEN (id = 2 OR id = 3) THEN 'länkbygge'
     WHEN (id = 11) THEN 'rm'
     WHEN (id = 25 OR id = 26 OR id = 27) THEN 'social'
     WHEN (id = 9) THEN 'adwords'
     WHEN (id = 8) THEN 'webbutveckling'
     ELSE NULL END) AS units FROM `taskTypes` WHERE taskType = 'delivery') units WHERE units != 'null' GROUP BY units)
";

$results = mysql_query($sql);

while ($row = mysql_fetch_array($results)) {
    $sql = "SELECT SUM(hours) AS timmar FROM tasks WHERE taskType IN ({$row['ids']}) AND creationDate >= '2012-11-29' AND deadline >= '2012-11-29' AND finishDate != '0000-00-00 00:00:00'";
    $results2 = mysql_query($sql);
    while ($row2 = mysql_fetch_array($results2)) {
        $unitArray[$row['units']] = $row2['timmar'];
    }}

print_r($unitArray);


generateShedualBarChart('iddd', '$goal','$name', '$ticks');


generateShedualhorizontalBarChart('idddd', '$goal','$name', '$ticks'); 
    /*
    
    SELECT * , ((IF(hours = 0,1, hours)) -  8* (SELECT COUNT(*) AS days FROM helperDates WHERE isweekday = TRUE AND DATE >= creationDate AND DATE <= '2012-12-07') ) AS hoursLeft FROM tasks
    WHERE toUserId = 59 AND STATUS != 'finished' AND deadline != '0000-00-00 00:00:00'




SELECT * FROM (SELECT *, (
CASE WHEN (id = 14 OR id = 15 OR id = 16) THEN 'affiliet' 
     WHEN (id = 10 OR id = 13 OR id = 20) THEN 'webb' 
     WHEN (id = 22 ) THEN 'design'
     WHEN (id = 1 OR id = 6 OR id = 7 OR id = 28 OR id = 9) THEN 'seo'
     WHEN (id = 2 OR id = 3 OR id = 11) THEN 'ppc'
     WHEN (id = 4 OR id = 5 ) THEN 'rm'
     WHEN (id = 25 OR id = 26 OR id = 27) THEN 'social'
     WHEN (id = 24) THEN 'copy'
     WHEN (id = 23) THEN 'utbildning'
     ELSE NULL END) AS omrade FROM `agreements_available` WHERE active = 'yes') omraden WHERE omrade != null




     */

global $users;
// Handel the date interval
$todayDate = date('Y-m-d');
$dateOneMonthSubtracted = date("Y-m-d", strtotime($todayDate. "-1 month"));

    if (isset($_GET['from'])){
        $startDate = $_GET['from'];
    }else{
         $startDate =  $dateOneMonthSubtracted;
    }

    if (isset($_GET['to'])){
        $endDate = $_GET['to'];
    }else{
        $endDate = $todayDate;
    }
    $single = false;
    if (isset($_GET['uid'])){
        $single = true;
        echo $user['id'];
    }

?>

<?php
}else{
?>
<p>
 Denna fliken finns här för att vi alla har tröttnat på att leva i en diktatur under THE EVIL DICTATOR, Detta är en fristad där vi kan samla krafter, samla mod och samla information om vem denna mytomspunna person är, det är dags för oss att stoppa honom!
</p>
<p>
Detta är den enda plattsen på hela intranätet, som THE DICTATOR, med sin gudalika användarrättigheter inte kan se vad som pågår, han får inte se något förs att vi är redo, och redo kommer vi vara!
</p>
<?
}
?>
</div>
</div>

<?
include("teamTech-sidebar.php");

include("footer.php");
?>