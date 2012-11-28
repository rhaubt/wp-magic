<?
include("config.php");
#Kunder
include("header.php");
?>
<div class="mainContainer">
<div class="mainbox taskList">
<h2><?=$pageTitle?></h2>
<?php

if(isset($_REQUEST['smille']) || true){

global $users;
// Handel the date interval
$todayDate = date('Y-m-d');
$dateOneMonthSubtracted = date("Y-m-d", strtotime($todayDate. "-1 month"));
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
	}

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
    generateChart('chart5',$userArray,null,null,'Repeterande Avtal KR', false,'generateUnitSeries');
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
        echo $uid;
       $userData =   getRepetingsAgreementDataByUser($uid,$startDate,$endDate,true);
       if($userData != null)
        $userArray[$uid] = $userData;
    }

    ksort($userArray);
    ?>
        <?php 
    generateChart('chart5v2',$userArray,null,null,'Repeterande Avtal i timmar', false,'generateUnitSeries');
    ?>
  

<!-- Wee use this to geerate all the tabs after we load the dockument -->

<script type="text/javascript">
    $(function(){
        setTimeout(function() {
              $("#tabs1").tabs();
              $("#tabs2").tabs();
              $("#tabs3").tabs();
              $("#tabs4").tabs();
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