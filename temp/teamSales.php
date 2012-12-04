<?
$loadDataPoints = true;
include("config.php");
#Kunder
include("header.php");
?>
<style type="text/css">
    .ui-widget-header {
border: 1px solid #AAA;
background: #CCC url(images/ui-bg_highlight-soft_75_cccccc_1x100.png) 50% 50% repeat-x;
color: #222;
font-weight: bold;
}

.ui-tabs .ui-tabs-nav li {
position: relative;
float: left;
border-bottom-width: 0 !important;
margin: 0 .2em -1px 0;
padding: 0;
}

.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
border: 1px solid lightGrey;
background: #E6E6E6 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
font-weight: normal;
color: #555;

}

.ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited {
color: #212121;
text-decoration: none;
}
.ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited {
color: #212121;
text-decoration: none;
}
.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited {
color: #555;
text-decoration: none;
}

</style>
<div class="mainContainer">
<div class="mainbox taskList">
<h2><?=$pageTitle?></h2>
<?php

if(isset($_REQUEST['smille']) || true){

global $users;
// Handel the date interval
$todayDate = date('Y-m-d');
$dateOneMonthSubtracted = date("Y-m-d", strtotime($todayDate. "-3 month"));
$sellers = getSellerList();
$sellers[00] ="Samtliga";

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
   /*
    BOKADE MÖTEN
     */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $filter = "AND timeUnit >= '{$start}' AND timeUnit <= '{$end}' ";
    $userArray = array();
    foreach ($sellers as $uid => $value)
    {
       $userData = getMeetingsDataByUser($uid,$filter);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

<div id="tabs1">
    <ul>
        <li><a href="#tabs1-1">Bokade möten</a></li>
        <li><a href="#tabs1-2">Bokade möten medel</a></li>
    </ul>
    <div id="tabs1-1">
    <?php 
    generateChart('chart1',$userArray,null,null,'Bokade böten', false);
    ?>
    </div>
    <div id="tabs1-2">
    <?php 
    /*
    BOKADE MÖTEN MEDEL
     */
    generateChart('chart1v2',$userArray,null,null,'Bokade Möten Medel', true);
    ?>
    </div>
</div>

<?php
   /*
    GJORDA MÖTEN
     */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $filter = "AND finishDate >= '{$start}' AND finishDate <= '{$end}'";
    $timeUnitField ='finishDate';
    $userArray = array();
    foreach ($sellers as $uid => $value)
    {
       $userData = getMeetingsDataByUser($uid,$filter,$timeUnitField);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

<div id="tabs2">
    <ul>
        <li><a href="#tabs2-1">Gjorda Möten</a></li>
        <li><a href="#tabs2-2">Gjorda Möten Medel</a></li>
    </ul>
    <div id="tabs2-1">
    <?php 
    generateChart('chart2',$userArray,null,null,'Gjorda möten', false);
    ?>
    </div>
    <div id="tabs2-2">
    <?php 
    /*
    BOKADE MÖTEN MEDEL
     */
    generateChart('chart2v2',$userArray,null,null,'Gjorda Möten Medel', true);
    ?>
    </div>
</div>

<?php
   /*
    ANTAL GODKÄNDA AVTAL
     */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $filter = "AND timeUnit >= '{$start}' AND timeUnit <= '{$end}' ";
    $userArray = array();
    foreach ($sellers as $uid => $value)
    {
       $userData = getAgreementDataCountByUser($uid,$filter);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

<div id="tabs3">
    <ul>
        <li><a href="#tabs3-1">Antal avtal</a></li>
        <li><a href="#tabs3-2">Antal avtal medel</a></li>
    </ul>
    <div id="tabs3-1">
    <?php 
    generateChart('chart3',$userArray,null,null,'Antal Godkända Avtal', false);
    ?>
    </div>
    <div id="tabs3-2">
    <?php 
    /*
    ANTAL GODKÄNDA AVTAL MEDEL
     */
    generateChart('chart3v2',$userArray,null,null,'Antal Godkända Avtal Medel', true);
    ?>
    </div>
</div>


<?php
   /*
    GODKÄNDA AVTAL I KR
     */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $filter = "AND timeUnit >= '{$start}' AND timeUnit <= '{$end}' ";
    $userArray = array();
    foreach ($sellers as $uid => $value)
    {
       $userData = getAgreementDataByUser($uid,$filter);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

<div id="tabs4">
    <ul>
        <li><a href="#tabs4-1">Avtal i kr</a></li>
        <li><a href="#tabs4-2">Antal i kr medel</a></li>
    </ul>
    <div id="tabs4-1">
    <?php 
    generateChart('chart4',$userArray,null,null,'Godkända Avtal KR', false);
    ?>
    </div>
    <div id="tabs4-2">
    <?php 
    /*
    GODKÄNDA AVTAL MEDEL
     */
    generateChart('chart4v2',$userArray,null,null,'Godkända Avtal Medel KR', true);
    ?>
    </div>
</div>
<?php 
//Dölj från singel vyn
if($singleUid < 1){ 
?>
<?php

   /*
    MÄNGD REPETERANDE AVTAL KR
     */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));

    $userArray = array();
    $types = getAgreementTypeList();
    $types[00]= "Samtliga";
    foreach ($types as $uid => $value)
    {
       $userData =   getRepetingsAgreementDataByUser($uid,$startDate,$endDate);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

    <?php 
    generateChart('chart5',$userArray,null,null,'Repeterande Avtal KR / Månad', false,'generateUnitSeries');
    ?>

<?php
   /*
    MÄNGD REPETERANDE AVTAL TIMMAR
     */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));

    $userArray = array();
    $types = getAgreementTypeList();
    $types[00]= "Samtliga";
    foreach ($types as $uid => $value)
    {
       $userData =   getRepetingsAgreementDataByUser($uid,$startDate,$endDate,true);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>
        <?php 
    generateChart('chart5v2',$userArray,null,null,'Repeterande Avtal i timmar / Månad', false,'generateUnitSeries');
    ?>



    <?php
   /*
    FÖRTJÄNST I KR PER AFFÄRSOMRÅDE
    */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $filter = "AND timeUnit >= '{$start}' AND timeUnit <= '{$end}' ";
    $userArray = array();
    $types = getAgreementTypeList();
    $types[00]= "Samtliga";
    foreach ($types as $uid => $value)
    {
       $userData = getAgreementDataByUnit($uid,$filter);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

<div id="tabs6">
    <ul>
        <li><a href="#tabs6-1">Avtal i kr</a></li>
        <li><a href="#tabs6-2">Antal i kr medel</a></li>
    </ul>
    <div id="tabs6-1">
    <?php 
    generateChart('chart6',$userArray,null,null,'Förtjänst per affärsområde', false,'generateUnitSeries');
    ?>
    </div>
    <div id="tabs6-2">
    <?php 
    /*
     MEDEL FÖRTJÄNST I KR PER AFFÄRSOMRÅDE
     */
    generateChart('chart6v2',$userArray,null,null,'Medelförtjänst per affärsområde', true,'generateUnitSeries');
    ?>
    </div>
</div> 

   <?php
   /*
    SPENDING I KR PER AFFÄRSOMRÅDE
    */
    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $filter = "AND timeUnit >= '{$start}' AND timeUnit <= '{$end}' ";
    $userArray = array();
    $types = getAgreementTypeList();
    $types[00]= "Samtliga";
    foreach ($types as $uid => $value)
    {
       $userData = getAgreementSpendingDataByUnit($uid,$filter);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>

<div id="tabs7">
    <ul>
        <li><a href="#tabs7-1">Spening i kr</a></li>
        <li><a href="#tabs7-2">Spedning i kr medel</a></li>
    </ul>
    <div id="tabs7-1">
    <?php 
    generateChart('chart7',$userArray,null,null,'Spending per affärsområde', false,'generateUnitSeries');
    ?>
    </div>
    <div id="tabs7-2">
    <?php 
    /*
     MEDEL FÖRTJÄNST I KR PER AFFÄRSOMRÅDE
     */
    generateChart('chart7v2',$userArray,null,null,'Spenidng per affärsområde', true,'generateUnitSeries');
    ?>
    </div>
</div>
  
  <?php } //Slut på dölj från singel vy ?>

  <?php if($singleUid > 0){

    $start = date("YW",strtotime($startDate));
    $end   = date("YW",strtotime($endDate));
    $sql = "SELECT DATE_FORMAT(DATE,'%Y%v') AS dates, DATE_FORMAT(DATE,'%Y-%v') AS dateKey FROM helperDates WHERE DATE >= '{$startDate}' AND DATE <= '{$endDate}' GROUP BY dates";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {

     $date = $row['dates'];
     $key = $row['dateKey'];
     $sql  = "SELECT COUNT(*) AS avslutade FROM (SELECT *, DATE_FORMAT(finishDate,'%Y%v') AS timeUnit FROM tasks WHERE taskType = 5 AND fromUserId = {$singleUid}) AS bokade WHERE timeUnit = '{$date}'";
     $sql2 = "SELECT COUNT(*) AS bokade FROM (SELECT *, DATE_FORMAT(creationDate,'%Y%v') AS timeUnit FROM tasks WHERE taskType = 5 AND toUserId = {$singleUid}) AS bokade WHERE timeUnit = '{$date}'";
     $subResult = mysql_query($sql);

     $subResult2 = mysql_query($sql2);
     $avslutade = mysql_fetch_array($subResult);
     $bokade = mysql_fetch_array($subResult2);


     $bokade     = $bokade['bokade'];
     $avslutade =  $avslutade['avslutade'];

     
     $sql = "SELECT * FROM goals_sellers WHERE userid = {$singleUid} AND DATE = '{$key}'";
     $subResult = mysql_query($sql);
     $goals = mysql_fetch_array($subResult);
     $skaBoka = max((int)$goals['boka'],0);
     $skaAvsluta = max((int)$goals['avsluta'],0);
     $keys[] = $key;
     $bokatArray[]   = array("goal" => $skaBoka, "actions"=> $bokade);
     $avslutaArray[] = array("goal" => $skaAvsluta, "actions"=> $avslutade);        
    }
       generateBarChart('chart8',$bokatArray,'Veckomål Bokningar',$keys);
       generateBarChart('chart9',$avslutaArray,'Veckomål Avslut',$keys);
     
    } ?>

<!-- Wee use this to geerate all the tabs after we load the dockument -->

<script type="text/javascript">
    $(function(){
        setTimeout(function() {
              $("#tabs1").tabs();
              $("#tabs2").tabs();
              $("#tabs3").tabs();
              $("#tabs4").tabs();
              $("#tabs6").tabs();
              $("#tabs7").tabs();
        }, '200');       
    });
</script>
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
include("teamSales-sidebar.php");

include("footer.php");
?>