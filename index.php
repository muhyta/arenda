<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8' /><meta name='description' content='Азбука Жилья БД' />
<title>Азбука Жилья БД</title>
<link rel="stylesheet" type="text/css" href="./style.css">
<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script> 
</head><body>
<script src="./calendar.js"></script>
<script src="./scripting.js"></script>
<script src="./jquery.js"></script>

<?php
//--------------------------------variable-declaration
$address1="";$agent="";$agent1="";$b="";$bron="";$bron1="";$comments1="";$d="";$f="";$id="";$l="";$mastername1="";
$nond="";$nond1="";$o="";$ocup="";$ocup1="";$p="";$pw="";$region1="";$src="";$tax1="";$tel1="";$tip_obj1="";

//--------------------------------functions-declaration
function new_client($dbconn,$oid,$mit,$mat,$reg,$typ){
	$query="SELECT obj_id FROM objects WHERE nond='f' AND tax > $mit AND tax < $mat AND region='$reg' AND type_object='$typ';";
	$res=pg_query($dbconn,$query);
	$query="";
	while($r = pg_fetch_row($res)){
		$query.="INSERT INTO sent_sms (cli_id,obj_id) VALUES ('$oid','$r[0]');";}
	$wtf=write_base($dbconn,$query);
	if ($wtf <> 0) {return $wtf;}
	return 0;}

function new_object($dbconn,$oid,$tax,$reg,$typ){
	$query="SELECT cli_id FROM clients WHERE max_tax > $tax AND min_tax < $tax AND region='$reg' AND type_object='$typ';";
	$res=pg_query($dbconn,$query);
	$query="";
	while($r = pg_fetch_row($res)){
		$query.="INSERT INTO sent_sms (cli_id,obj_id) VALUES ('$r[0]','$oid');";}
	$wtf=write_base($dbconn,$query);
	if ($wtf <> 0) {return $wtf;}
	return 0;}

function write_base($dbconn,$query) {
	pg_query($dbconn, "BEGIN WORK");
	$res=pg_query($dbconn,$query);
	if (!$res) {
		$wtf=pg_query($dbconn, "ROLLBACK");
		return $wtf;}
	else {
		$wtf=pg_query($dbconn, "COMMIT");	
		return 0;}}

function send_sms2all($dbconn) {
	$today=date('Y-m-d H:i:s');
	$text=$today." from ".$_SERVER['REMOTE_ADDR'];
	$query="SELECT * FROM objects WHERE nond='f';";
	$res1=pg_query($dbconn,$query);
	$query="SELECT * FROM clients WHERE ocupat='Незаселен' AND sms > 0;";
	$res2=pg_query($dbconn,$query);
	$query="SELECT * FROM sent_sms;";
	$res3=pg_query($dbconn,$query); $i=0;
	while ($r3[$i] = pg_fetch_row($res3)){$i++;}
	$query="UPDATE outbox SET processed='f',text='$text' WHERE number='+79091933799';";
	while ($r1 = pg_fetch_row($res1)){
		while ($r2 = pg_fetch_row($res2)){
			if ($r1[5] < $r2[8] and $r1[5] > $r2[7] and $r1[12] == $r2[12] and $r1[3] == $r2[6]){
				$tel="+7".$r2[5];
				$text=$r1[3]." ".$r1[5]."p. ".$r1[12]." +7".$r1[7];
				$r2[13]=$r2[13]-1;
				$query.="INSERT outbox (text,number) VALUES ('$text','$tel');UPDATE clients SET sms=$r2[13] WHERE cli_id=$r2[0];";}}}
	$result=pg_query($dbconn,$query);
	return 0;}

function show_base($b,$w,$o,$dbconn,$id) {
if ($w == "" or $w == " ") {$w="WHERE 1=1 ".$w;}
	switch ($b) {
	        case "objects": $w.="AND nond='f'"; break;
        	case "objects_arh": break;
	        case "clients": break;
	        case "clients_arh": break;
		case "1": $w.="AND nond='f'";$b="objects";break;
		case "2": $b="objects_arh";break;
		case "3": $b="clients";break;
		case "4": $b="clients_arh";break;
		case "5": $o="num";$b="agents";break;
		case "6": $w.="AND nond='t'";$b="objects";break;
		case "agents":$o="num";break;
		case "_arh": $b="objects_arh";break;
		case "": $b="objects";break;}
	switch ($o) {
		case "11": $o="obj_id DESC";break;
		case "12": $o="obj_id ASC";break;
		case "21": $o="date_edit DESC";break;
		case "22": $o="date_edit ASC";break;
		case "31": $o="date_insert DESC";break;
		case "32": $o="date_insert ASC";break;
		case "41": $o="type_object DESC";break;
		case "42": $o="type_object ASC";break;
		case "51": $o="mastername DESC";break;
		case "52": $o="mastername ASC";break;
		case "61": $o="tax DESC";break;
		case "62": $o="tax ASC";break;
		case "71": $o="adress DESC";break;
		case "72": $o="adress ASC";break;
		case "81": $o="tel DESC";break;
		case "82": $o="tel ASC";break;
		case "101": $o="region DESC";break;
		case "102": $o="region ASC";break;
		case "111": $o="ocup_date DESC";break;
		case "112": $o="ocup_date ASC";break;}
	$o="ORDER BY ".$o;
	$query="SELECT * FROM $b $w $o;";
	$result=pg_query($dbconn, $query);
	$base="<div style='text-align:center;align:center;'>";
	switch ($b) {
		case "objects":
                $base.="<table border='0' width='100%' id='z'>
			<tr style='text-align:center;font-weight:bold;'>
              	<td class='basetd'><a href='./index.php?p=aak&b=1&o=11&id=$id'>↑</a> № <a href='./index.php?p=aak&b=1&o=12&id=$id'>↓</a></td>
		<td class='basetd'><a href='./index.php?p=aak&b=1&o=21&id=$id'>↑</a> Дата правки <a href='./index.php?p=aak&b=1&o=22&id=$id'>↓</a></td>
		<td class='basetd'><a href='./index.php?p=aak&b=1&o=31&id=$id'>↑</a> Дата ввода <a href='./index.php?p=aak&b=1&o=32&id=$id'>↓</a></td>
		<td class='basetd'><a href='./index.php?p=aak&b=1&o=41&id=$id'>↑</a> Тип <a href='./index.php?p=aak&b=1&o=42&id=$id'>↓</a></td>
		<td class='basetd'><a href='./index.php?p=aak&b=1&o=101&id=$id'>↑</a> Район <a href='./index.php?p=aak&b=1&o=102&id=$id'>↓</a></td>
		<td class='basetd'><a href='./index.php?p=aak&b=1&o=71&id=$id'>↑</a> Адрес <a href='./index.php?p=aak&b=1&o=72&id=$id'>↓</a></td>
		<td class='basetd'><a href='./index.php?p=aak&b=1&o=61&id=$id'>↑</a> Цена <a href='./index.php?p=aak&b=1&o=62&id=$id'>↓</a></td>
		<td class='basetd'>Бронь?</td>
		<td class='basetd'> Телефон </td>
		<td class='basetd'> Хозяин </td>
			</tr>";
                while ($r = pg_fetch_row($result)) {
                       $base.="<tr class='btr' onclick='cl(\"./index.php?p=o&b=1&n=$r[0]&id=$id\")'>";
                        if ($r[9]=="0" and $r[10]=="0") {$r[9]="Да";$r[10]="Нет";}
                                elseif ($r[10]=="0" and $r[9] != "0") {$r[9]="c ".$r[9];$r[10]="Нет";}
                                elseif ($r[9]=="0" and $r[10] != "0") {$r[9]=="Бронь";$r[10]="до ".$r[10];}
                                else {$r[9]="c ".$r[9];$r[10]="до ".$r[10];}
			for ($n=0;$n<14;$n++){
                                if ($r[$n]=="t") {$r[$n]="√";} 
				if ($r[$n]=="f") {$r[$n]="-";}}
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[0]</abbr></td>";//#
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[1]</abbr></td>";//Дата правки
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[2]</abbr></td>";//Дата ввода
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[3]</abbr></td>";//Тип
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[12]</abbr></td>";//Район
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[6]</abbr></td>";//Адрес
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[5]</abbr></td>";//Цена
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[10]</abbr></td>";//Бронь
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>(+7)$r[7]</abbr></td>";//Телефон
			$base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[4]</abbr></td>";//Хозяин
//			$base.="<td class='basetd'><abbr title='Агент:$r[8]; Описание:$r[11]'>$r[13]</abbr></td>";//Но-нд
			$base.="</tr>";}
                $base.="</table>";
		break;
	case "objects_arh":
                $base.="<table border='0' width='100%' id='z'>
			<tr style='text-align:center;font-weight:bold;'>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=11&id=$id'>↑</a> № <a href='./index.php?p=aak&b=2&o=12&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=21&id=$id'>↑</a> Дата правки <a href='./index.php?p=aak&b=2&o=22&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=31&id=$id'>↑</a> Дата ввода <a href='./index.php?p=aak&b=2&o=32&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=41&id=$id'>↑</a> Тип <a href='./index.php?p=aak&b=2&o=42&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=101&id=$id'>↑</a> Район <a href='./index.php?p=aak&b=2&o=102&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=71&id=$id'>↑</a> Адрес <a href='./index.php?p=aak&b=2&o=72&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=61&id=$id'>↑</a> Цена <a href='./index.php?p=aak&b=2&o=62&id=$id'>↓</a></td>
                <td class='basetd'><a href='./index.php?p=aak&b=2&o=111&id=$id'>↑</a> Свободно? <a href='./index.php?p=aak&b=2&o=112&id=$id'>↓</a></td>
                <td class='basetd'> Телефон </td>
                <td class='basetd'> Хозяин </td>
		<td class='basetd'>Н/о|Н/д</td>
			</tr>";
                while ($r = pg_fetch_row($result)) {
                       $base.="<tr class='btr' onclick='cl(\"./index.php?p=o&b=2&n=$r[0]&id=$id\")'>";
                        if ($r[9]=="0" and $r[10]=="0") {$r[9]="Да";$r[10]="Нет";}
                                elseif ($r[10]=="0" and $r[9] != "0") {$r[9]="c ".$r[9];$r[10]="Нет";}
                                elseif ($r[9]=="0" and $r[10] != "0") {$r[9]=="Бронь";$r[10]="до ".$r[10];}
                                else {$r[9]="c ".$r[9];$r[10]="до ".$r[10];}
                         for ($n=0;$n<14;$n++){
                                if ($r[$n]=="t") {$r[$n]="√";} 
                                if ($r[$n]=="f") {$r[$n]="-";}}
                       $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[0]</abbr></td>";//#
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[1]</abbr></td>";//Дата правки
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[2]</abbr></td>";//Дата ввода
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[3]</abbr></td>";//Тип
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[12]</abbr></td>";//Район
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[6]</abbr></td>";//Адрес
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[5]</abbr></td>";//Цена
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[9]</abbr></td>";//Бронь
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>(+7)$r[7]</abbr></td>";//Телефон
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[4]</abbr></td>";//Хозяин
                        $base.="<td class='basetd'><abbr title='Агент: $r[8]\nОписание: $r[11]'>$r[13]</abbr></td>";//Но-нд
			$base.="</tr>";}
                $base.="</table>";
		break;
	case "clients":
                $base.="<table border='0' width='100%' id='z'>
                        <tr style='text-align:center;font-weight:bold;'>
                                <td class='basetd'>№</td>
                                <td class='basetd'>Дата правки</td>
                                <td class='basetd'>Дата ввода</td>
                                <td class='basetd'>Ф.И.О.</td>
                                <td class='basetd'>договор №</td>
                                <td class='basetd'>Телефон</td>
                                <td class='basetd'>Тип</td>
                                <td class='basetd'>Заселен?</td>
                                <td class='basetd'>н/д или н/о</td>
				<td class='basetd'>SMS</td>
                        </tr>";
                while ($r = pg_fetch_row($result)) {
                        $base.="<tr class='btr' onclick='cl(\"./index.php?p=c&b=3&n=$r[0]&id=$id\")'>";
                        for ($n=0;$n<14;$n++){
                                if ($r[$n]=="t") {$r[$n]="√";} elseif ($r[$n]=="f") {$r[$n]="-";}
                                if ($n==7 or $n==8 or $n==11 or $n==12){} else {$base.="<td><abbr title='$r[11]'>$r[$n]</abbr></td>";}}
                        $base.="</tr>";}
                $base.="</table>";
		break;
	case "clients_arh":
                $base.="<table border='0' width='100%' id='z'>
                        <tr style='text-align:center;font-weight:bold;'>
                                <td class='basetd'>№</td>
                                <td class='basetd'>Дата правки</td>
                                <td class='basetd'>Дата ввода</td>
                                <td class='basetd'>Ф.И.О.</td>
                                <td class='basetd'>Телефон</td>
                                <td class='basetd'>Тип</td>
                                <td class='basetd'>Заселен?</td>
                                <td class='basetd'>н/д или н/о</td>
                        </tr>";
                while ($r = pg_fetch_row($result)) {
                        $base.="<tr class='btr' onclick='cl(\"./index.php?p=c&b=4&n=$r[0]&id=$id\")'>";
                        for ($n=0;$n<13;$n++){
                                if ($r[$n]=="t") {$r[$n]="√";} elseif ($r[$n]=="f") {$r[$n]="-";}
                                if ($n==4 or $n==7 or $n==8 or $n==11 or $n==12){} else {$base.="<td><abbr title='$r[11]'>$r[$n]</abbr></td>";}}
                        $base.="</tr>";}
                $base.="</table>";
		break;
	case "agents":
		$base.="<table border='0' width='auto' >
			<tr style='text-align:right;font-weight:bold;'>
			<form action='./index.php' method='get' enctype='text/plain'>
				<input type='hidden' name='p' value='insert'>
				<input type='hidden' id='b' name='b' value='5'>
				<input type='hidden' name='id' value='$id'>
				<td class='basetd'>№</td>
				<td class='basetd'><input type='text' name='num'></td>
				<td class='basetd'><input type='submit' value='+'></td></form></tr>";
		$n=pg_num_rows($result);$i=1;
		while ($r = pg_fetch_row($result)) {
			$base.="<tr><td class='basetd'>$i</td><td class='basetd'>$r[0]</td><td><input type='button' value='-' onclick='cl(\"./index.php?d=1&p=insert&b=5&n=$r[0]&id=$id\")'></td></tr>";$i++;}
		$base.="</table>";
		break;}
	$base.="</div>";
	return($base);}
//--------------------------------------------------end
$f="";	$id="";	$pw="";	$l="";	$top="";	$head="";	$footer="";	$base="";	$root=0;
$today=date('Y-m-d');
//foreach($_REQUEST as $key => $value) {$_REQUEST[$key] = htmlspecialchars($value);}
$page=htmlspecialchars($_REQUEST['p']);
if ($page=="aar" or $page=="ca" or $page=="car") {$page="aak";}
$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=db user=usr password=passwd") or die('connection failed');
//---------------------------------------auth
$login=htmlspecialchars($_REQUEST['l']);
$pass=htmlspecialchars($_REQUEST['pw']);
$id=htmlspecialchars($_REQUEST['id']);
if (strlen($id)==0 and strlen($login)==0) {$page="";}
elseif (strlen($id)==0 and strlen($login)!=0) {
	$query="SELECT pswd,mod FROM mngusrs WHERE name='$login';";
	$rs=pg_query($dbconn,$query);
	$ps=pg_fetch_row($rs);
	if (md5($pass)==$ps[0] and ($ps[1]=="1" or $ps[1]=="7")) {
                $id=md5(rand(0,99999999));
                $query="UPDATE mngusrs SET uid='$id' WHERE name='$login';";
                pg_query($dbconn,$query);
		if ($login=="muhyta" or $login=="admin") {$root=1;}} 
	else {$page="";}}
elseif (strlen($id)==32 and strlen($login)==0) {
	$err=1;
        $query="SELECT name FROM mngusrs WHERE uid='$id';";
        $rs=pg_query($dbconn,$query);
	while($ps=pg_fetch_array($rs)) {
		$login=$ps[0];
		echo "<div class='butshadow' style='position:fixed;right:10px;top:5px;padding:2px 7px 2px 7px;'>$login</div>";
		if (strlen($login)>4) {
			$id=md5(rand(0,99999999));
			$query="UPDATE mngusrs SET uid='$id' WHERE name='$login';";
			pg_query($dbconn,$query);
			if ($login=="muhyta" or $login=="admin") {$root=1;}
			$err=0;break;}
		else {$err=1;}}
	if ($err==1) {$page="";}}
else {$page="";}
//-----------------------------------auth-end

//------
//filter
//------
$b=htmlspecialchars($_REQUEST['b']);
$w = "WHERE 1=1 ";
switch ($b) {
        case "objects":$ins="b=1&id=".$id;$w.="AND nond='f'";break;
        case "objects_arh":$ins="b=2&id=".$id;break;
        case "clients":$ins="b=3&id=".$id;break;
        case "clients_arh":$ins="b=4&id=".$id;break;
	case "agents":$ins="b=1&id=".$id;break;
	case "1":$b="objects";$ins="b=1&id=".$id;$w.="AND nond='f'";break;
	case "2":$b="objects_arh";$ins="b=2&id=".$id;break;
	case "3":$b="clients";$ins="b=3&id=".$id;break;
	case "4":$b="clients_arh";$ins="b=4&id=".$id;break;
	case "5":$b="agents";$ins="b=1&id=".$id;break;
	case "6":$b="objects";$ins="b=1&id=".$id;$w.="AND nond='t'";break;
	default:$b="objects";$ins="b=1&id=".$id;break;}
//var_dump($b,$ins);
$filter=file_get_contents('filter.tmp');
if ($root==1){$filter=str_ireplace("%admin%","<a class='butshadow' style='padding:1px 8px 1px 8px;color:red;' href='./index.php?p=close&id=$id'> Закрыть день </a> <a class='butshadow' style='padding:1px 8px 1px 8px;color:green;' href='./index.php?p=open&id=$id'> Открыть день </a>",$filter);}
	else{$filter=str_ireplace("%admin%","",$filter);}
if ($b <> "agents" or $b <> "5") {$query="SELECT COUNT(*) FROM $b $w AND date_insert='$today';";}
else {$query="SELECT COUNT(*) FROM $b $w;";}
$result=pg_query($dbconn,$query);
$r = pg_fetch_row($result);
$filter = str_ireplace("%col_d%",$r[0],$filter);
$query="SELECT COUNT(*) FROM $b $w;";
$result=pg_query($dbconn,$query);
$r = pg_fetch_row($result);
$filter = str_ireplace("%col_f%",$r[0],$filter);
$query="SELECT * FROM type;";
$result=pg_query($dbconn,$query);
$base="";
while ($r = pg_fetch_row($result)) {$base.="<option value='$r[0]'>$r[0]</option>";}
$filter = str_ireplace("%tipo%",$base,$filter);
$base="";
$query="SELECT * FROM region;";
$result=pg_query($dbconn,$query);
while ($r = pg_fetch_row($result)) {$base.="<option value='$r[0]'>$r[0]</option>";}
$filter = str_ireplace("%regn%",$base,$filter);
$filter = str_ireplace("%insert%",$ins,$filter);
$b="";
$base="";

switch ($page) {
//--------
//open day
//--------
        case "open":
                $query="UPDATE mngusrs SET mod=1 WHERE mod='0';";
                pg_query($dbconn,$query);
                print "<br><br>Успешно открыты все учетные записи.<br><a href='./index.php?p=aak&id=$id'>Переход на главную</a>";
        break;
//---------
//close day
//---------
	case "close":
		$query="UPDATE mngusrs SET mod=0,uid='0' WHERE mod='1';";
		pg_query($dbconn,$query);
		print "<br><br>Успешно закрыты все учетные записи, кроме Вашей.<br><a href='./index.php?p=aak&id=$id'>Переход на главную</a>";
	break;
	
//-------------------
//standart output db
//-------------------
	case "aak": 
		$b=$_REQUEST['b'];
		switch ($b) {
			case "1":$b="objects";$query_o="21";break;
			case "objects_arh":$query_o="ocup_date ASC";break;
			case "2":$b="objects_arh";$query_o="ocup_date ASC";break;
			case "3":$b="clients";$query_o="21";break;
			case "4":$b="clients_arh";$query_o="21";break;
			case "5":$b="agents";$query_o="num";break;
			case "6":$query_o="21";break;
			default:$b="objects";$query_o="21";break;}
			
		$f=$_REQUEST['f'];
		if ($f == "1") {
			$fa=$_REQUEST['by_adr'];if ($fa == "") {} else {$fa="AND adress ~* '.*$fa.*'";} 
			$ftel=$_REQUEST['by_tel'];if ($ftel == "") {} else {$ftel="AND ''||tel ~* '.*$ftel.*'";}
			$fr1=$_REQUEST['filt_reg'];if ($fr1[0] == "" or $fr1[0] == "*") {$fr1[0]="";}
			$fr="AND region ~* '.*($fr1[0]";
			for ($i=1;$i<sizeof($fr1);$i++) {$fr.="|$fr1[$i]";}
			$fr.=").*'";
			$ft1=$_REQUEST['filt_type'];if ($fr1[0] == "" or $fr1[0] == "*") {$fr1[0]="";}
			$ft="AND type_object ~* '.*($ft1[0]";
			for ($i=1;$i<sizeof($ft1);$i++) {$ft.="|$ft1[$i]";}
			$ft.=").*'";
			$query_f="WHERE (1=1 $fa $ftel $fr $ft)";}
			else {$query_f="";}	
		$top=file_get_contents('aak.tmp');
		$top = str_ireplace("%filter%",$filter,$top);
                $top = str_ireplace("%id%",$id,$top);
		$o=htmlspecialchars($_REQUEST['o']);if ($o=="") {$o=$query_o;}
		$base=show_base($b,$query_f,$o,$dbconn,$id);
		break;
//-------------------
//get from insert & insert form
//-------------------
	case "insert":
		$query=""; 
		$b=htmlspecialchars($_REQUEST['b']);
		switch ($b) {
//objects
			case "2":
                        case "1":$b="objects";
		                $date_edit=$today;
	        	        $date_insert=$today;
	                	$type_object=$_REQUEST['tip_obj1'];
		                $mastername=$_REQUEST['mastername1'];
		                $adress=$_REQUEST['address1'];
		                $tax=$_REQUEST['tax1'];
		                $tel=$_REQUEST['tel1'];
	        	        $agent=$_REQUEST['agent1'];if($agent=="1"){$agent="true";}else {$agent="false";}//1=flag
		                $ocup=$_REQUEST['ocup1'];if($ocup=="1"){$ocup=$_REQUEST['ocup_date1'];}else {$ocup="0";}
		                $bron=$_REQUEST['bron1'];if($bron=="1"){$bron=$_REQUEST['bron_date1'];}else {$bron="0";}
		                $comments=$_REQUEST['comments1'];
		                $region=$_REQUEST['region1'];
		                $nond=$_REQUEST['nond1'];if($nond=="1"){$nond="true";}else {$nond="false";}
		                $src=$_REQUEST['src'];
				$top=file_get_contents ('insert.tmp');
		                $top = str_ireplace("%date_edit%",$today,$top);
		                $top = str_ireplace("%date_insert%",$today,$top);
		                $top = str_ireplace("%date_ocup%","",$top);
		                $top = str_ireplace("%date_bron%","",$top);
		                $top = str_ireplace("%id%",$id,$top);
		                $top=str_ireplace("%baza%",$b,$top);
				//hereup
				if (isset($_FILES)) {
					$query="SELECT MAX(obj_id) FROM objects;";
					$result=pg_query($dbconn,$query);
					$up_dir=pg_fetch_row($result);
					var_dump($_FILES['img']);
					$up_dir[0]++;
				foreach ($_FILES['img']['error'] as $key => $error) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES['img']['tmp_name'][$key];
						$name = $_FILES['img']['name'][$key];
						$name = `mkdir ./foto/$up_dir[0]`;
						var_dump($name);
						$name=$key+1;
						move_uploaded_file($tmp_name, "foto/$up_dir[0]/$name.jpg");}}}
		                $query="SELECT * FROM type;";
		                $result=pg_query($dbconn,$query);
		                while ($r1 = pg_fetch_row($result)) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
		                $top = str_ireplace("%type%",$base,$top);
		                $base="";
		                $query="SELECT * FROM region;";
		                $result=pg_query($dbconn,$query);
		                while ($r1 = pg_fetch_row($result)) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
		                $top = str_ireplace("%region%",$base,$top);
		                $base="";
				if ( $ocup == "0" ) {$query="INSERT INTO $b (date_edit, date_insert, type_object, mastername, tax, adress, tel, agent, ocup_date, bron_date, comments, region,nond,src) VALUES ('$date_edit', '$date_insert', '$type_object', '$mastername', '$tax', '$adress', '$tel', '$agent', '$ocup', '$bron', '$comments', '$region','$nond','$src');";}
				else {$b.="_arh";$query="INSERT INTO $b (date_edit, date_insert, type_object, mastername, tax, adress, tel, agent, ocup_date, bron_date, comments, region,nond,src) VALUES ('$date_edit', '$date_insert', '$type_object', '$mastername', '$tax', '$adress', '$tel', '$agent', '$ocup', '$bron', '$comments', '$region','$nond','$src');";}
		                if ($type_object=="" and $mastername=="" and $adress=="" and $tax==0 and $tel=="" and $region=="") {$query="err;";}
				break;
//clients
			case "4":
			case "3":$b="clients";
		                $fio=$_REQUEST['mastername2'];
		                $dog_num=$_REQUEST['d_num2'];
		                $tel=$_REQUEST['tel2'];
		                $type_object=$_REQUEST['tip_obj2'];
		                $min_tax=$_REQUEST['min_tax2'];
		                $max_tax=$_REQUEST['max_tax2'];
		                $ocupat=$_REQUEST['ocup2'];
		                $nond=$_REQUEST['nond2'];if($nond=="1"){$nond="true";}else {$nond="false";}
		                $comments=$_REQUEST['comments2'];
		                $region=$_REQUEST['region2'];
		                $top= file_get_contents('insert.tmp');
		                $top = str_ireplace("%date_edit%",$today,$top);
		                $top = str_ireplace("%date_insert%",$today,$top);
		                $query="SELECT * FROM type;";
		                $result=pg_query($dbconn,$query);
		                while ($r1 = pg_fetch_row($result)) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
		                $top = str_ireplace("%type%",$base,$top);
		                $top = str_ireplace("%id%",$id,$top);
		                $base="";
		                $query="SELECT * FROM region;";
		                $result=pg_query($dbconn,$query);
		                while ($r1 = pg_fetch_row($result)) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
		                $top = str_ireplace("%region%",$base,$top);
		                $base="";
				if ($dog_num=="0") {$b.="_arh";
		                $query="INSERT INTO $b (cli_id, date_edit, date_insert, fio, dog_num, tel, type_object, min_tax, max_tax, ocupat, nond, comments, region) VALUES ((SELECT MAX(cli_id) FROM $b)+1, '$today', '$today', '$fio', '$dog_num', '$tel', '$type_object', '$min_tax', '$max_tax', '$ocupat', '$nond', '$comments', '$region');";}
				else {$query="INSERT INTO $b (cli_id, date_edit, date_insert, fio, dog_num, tel, type_object, min_tax, max_tax, ocupat, nond, comments, region) VALUES ((SELECT MAX(cli_id) FROM $b)+1, '$today', '$today', '$fio', '$dog_num', '$tel', '$type_object', '$min_tax', '$max_tax', '$ocupat', '$nond', '$comments', '$region');";}
		                if ($fio == "" or $tel=="" or $ocupat=="" or $region=="") {$query="err;";}
				break;
			case "5": 
				$b="agents";
				$n=htmlspecialchars($_REQUEST['n']);
				$num=htmlspecialchars($_REQUEST['num']);
				if ($_REQUEST['d'] == "1"){$query="DELETE FROM $b WHERE num='$n';";}
				else {$query="INSERT INTO $b (num) VALUES ('$num');";}
				$top = file_get_contents('aak.tmp');
				$base = str_ireplace("%id%",$id,$base);
				$top = str_ireplace("%id%",$id,$top);
				break;
			default: $query="err;";break;}
		if ($query == "err;") {} else {
			$wtf=write_base($dbconn,$query);
			$query="";
			if ($wtf <> 0) { 
				var_dump($wtf,$query);
				$top=str_ireplace("%result%","style='background:red;'",$top);} 
			else { 	$top=str_ireplace("%result%","style='background:green;'",$top);
				if ($b=="1" or $b=="objects") {
					$query="SELECT MAX(obj_id) FROM objects;";
					$res=pg_query($dbconn,$query);$r=pg_fetch_row($res);
					new_object($dbconn,$r[0],$tax,$region,$type_object);}
				elseif ($b=="2" or $b=="clients") {
					$query="SELECT MAX(cli_id) FROM clients;";
					$res=pg_query($dbconn,$query);$r=pg_fetch_row($res);
					new_client($dbconn,$r[0],$min_tax,$max_tax,$region,$type_object);}}}
				$base=show_base($b,'','21',$dbconn,$id);
				$base = str_ireplace("%id%",$id,$base);
		$query="";
		break;
//-----------------
//fill object edit form
//-----------------
        case "o":
                $n=$_REQUEST['n'];
		$b=$_REQUEST['b'];
                $top = file_get_contents ('edit-obj.tmp');
                $top = str_ireplace("%id%",$id,$top);
		$top = str_ireplace("%baza%",$b,$top);
		switch ($b) {
			case "2":$b="objects_arh";$query_o="ocup_date ASC";break;
			default:$b="objects";$query_o="date_edit DESC";break;}
                $query="SELECT * FROM $b WHERE obj_id=$n;";
                $result=pg_query($dbconn,$query);
                $r=pg_fetch_array($result);
                $top = str_ireplace("%date_edit%",$r['date_edit'],$top);
                $top = str_ireplace("%mastername%",$r['mastername'],$top);
                $top = str_ireplace("%adress%",$r['adress'],$top);
                $top = str_ireplace("%tax%",$r['tax'],$top);
                $top = str_ireplace("%tel%",$r['tel'],$top);
                $top = str_ireplace("%num%",$r['obj_id'],$top);
                $top = str_ireplace("%filter%",$filter,$top);
		$top = str_ireplace("%src%",$r['src'],$top);
		$street = substr($r['adress'],0,strpos($r['adress']," "));
		if ($street == NULL) $street = $r['adress']; 
		$top = str_ireplace("%city%","Тюмень",$top);
		$top = str_ireplace("%street%",$street,$top);
		for ($i=1;$i<=4;$i++) {
			$fname="./foto/".$n."/".$i.".jpg";
			if (file_exists($fname))
				$top = str_ireplace("%image".$i,"<img style='width:49%;height:auto;' src='$fname'>",$top); 
			else $top = str_ireplace("%image".$i,"<img style='width:49%;height:auto;' src='./foto/noimg.jpg'>",$top);}

		$base=show_base($b,$query_f,$query_o,$dbconn,$id);
		$top = str_ireplace("%base%",$base,$top);$base="";

                $top = str_ireplace("%id%",$id,$top);
                if ($r['nond']=="t") {$s="checked";} else {$s="";}				$top = str_ireplace("%nond%",$s,$top);
                if ($r['agent']=="t") {$s="checked";} else {$s="";}				$top = str_ireplace("%agent%",$s,$top);
                if ($r['ocup_date']!="0" or $r['bron_date']!="0") {$s="";} else {$s="checked";}	$top = str_ireplace("%free%",$s,$top);
                if ($r['ocup_date']!="0") {
                        $top = str_ireplace("%ocup%","checked",$top);
                        $top = str_ireplace("%date_ocup%",$r['ocup_date'],$top);}
                else {$top = str_ireplace("%ocup%","",$top);$top = str_ireplace("%date_ocup%","",$top);}
                if ($r['bron_date']!="0") {
                        $top = str_ireplace("%bron%","checked",$top);
                        $top = str_ireplace("%date_bron%",$r['bron_date'],$top);}
                else {$top = str_ireplace("%bron%","",$top);$top = str_ireplace("%date_bron%","",$top);}
                $top = str_ireplace("%comment%",$r['comments'],$top);
                $top = str_ireplace("%date_insert%",$r['date_insert'],$top);
                $query="SELECT * FROM type;";
                $result=pg_query($dbconn,$query);
                while ($r1 = pg_fetch_row($result)) {
                        if ($r1[0]!=$r['type_object']) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
                        else {$base.="<option value='$r1[0]' selected>$r1[0]</option>";}}
                $top = str_ireplace("%type%",$base,$top);
                $base="";
                $query="SELECT * FROM region;";
                $result=pg_query($dbconn,$query);
                while ($r1 = pg_fetch_row($result)) {
                        if ($r1[0]!=$r['region']) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
                        else {$base.="<option value='$r1[0]' selected>$r1[0]</option>";}}
                $top = str_ireplace("%region%",$base,$top);
        break;
//-----------------
//fill client edit form
//-----------------
        case "c":
                $n=$_REQUEST['n'];
		$b=$_REQUEST['b'];
                $top=file_get_contents ('edit-cli.tmp');
                switch ($b) {
			case "clients_arh":break;
                        case "4":$b="clients_arh";break;
                        default:$b="clients";break;}
                $query="SELECT * FROM $b WHERE cli_id=$n;";
                $result=pg_query($dbconn,$query);
                $r=pg_fetch_array($result);
                $top = str_ireplace("%date_edit%",$r['date_edit'],$top);
                $top = str_ireplace("%mastername%",$r['fio'],$top);
                $top = str_ireplace("%min_tax%",$r['min_tax'],$top);
                $top = str_ireplace("%max_tax%",$r['max_tax'],$top);
                $top = str_ireplace("%tel%",$r['tel'],$top);
                $top = str_ireplace("%num%",$r['cli_id'],$top);
                $top = str_ireplace("%filter%",$filter,$top);
                if ($r['nond']=="t") {$s="checked";} else {$s="";}	$top = str_ireplace("%nond%",$s,$top);
                if ($r['ocupat']=="Заселен") {$s1="checked";$s2="";$s3="";}
                        elseif ($r['ocupat']=="Заселен_нами") {$s1="";$s2="checked";$s3="";}
                        elseif ($r['ocupat']=="Незаселен") {$s1="";$s2="";$s3="checked";}
                        else {$s1="";$s2="";$s3="";}
                $top = str_ireplace("%ocup1%",$s1,$top);
                $top = str_ireplace("%ocup2%",$s2,$top);
                $top = str_ireplace("%ocup3%",$s3,$top);
                $top = str_ireplace("%d_num%",$r['dog_num'],$top);
                $top = str_ireplace("%comment%",$r['comments'],$top);
                $top = str_ireplace("%date_insert%",$r['date_insert'],$top);
                $query="SELECT * FROM type;";
                $result=pg_query($dbconn,$query);
                while ($r1 = pg_fetch_row($result)) {
                        if ($r1[0]!=$r['type_object']) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
                        else {$base.="<option value='$r1[0]' selected>$r1[0]</option>";}}
                $top = str_ireplace("%type%",$base,$top);
                $base="";
                $query="SELECT * FROM region;";
                $result=pg_query($dbconn,$query);
                while ($r1 = pg_fetch_row($result)) {
                        if ($r1[0]!=$r['region']) {$base.="<option value='$r1[0]'>$r1[0]</option>";}
                        else {$base.="<option value='$r1[0]' selected>$r1[0]</option>";}}
                $top = str_ireplace("%region%",$base,$top);
		$base = show_base($b,'','date_edit DESC',$dbconn,$id);
		$top = str_ireplace("%base%",$base,$top);$base="";
		$top = str_ireplace("%id%",$id,$top);
		$top = str_ireplace("%baza%",$b,$top);
        break;
//-----------------
//get from edit-obj
//-----------------
        case "eo":
                $n=$_REQUEST['n'];
                $d=$_REQUEST['d'];
		$b=$_REQUEST['b'];
                switch ($b) {
                        case "2":$b="objects_arh";break;
                        default:$b="objects";break;}
                if ($d=="1") {
			$query=`rm -rf ./foto/$n`;
                        $query="DELETE FROM $b WHERE obj_id='$n';";
                        pg_query($dbconn,$query);
                        $base="<br><br><a href='./index.php?p=aak&id=$id&b=$b'>Запись успешно удалена. Вернутся к списку.</a><br>
				<script type='text/javascript'>document.location.href = './index.php?p=aak&id=$id&b=$b';</script>";
                        $top.=$base;
                        break;}
                else {$base="something wrong ".$d." end";}
		$date_insert=$_REQUEST['date_insert'];
                $type_object=$_REQUEST['tip_obj'];
                $mastername=$_REQUEST['mastername'];
                $adress=$_REQUEST['address'];
                $tax=$_REQUEST['tax'];
                $tel=$_REQUEST['tel'];
		$nond=$_REQUEST['nond'];	if($nond=="1"){$nond="true";}else {$nond="false";}
                $agent=$_REQUEST['agent'];	if($agent=="1"){$agent="true";}else {$agent="false";}//1=flag
                $bron=$_REQUEST['bron'];	if($bron=="1"){$bron=$_REQUEST['bron_date'];}else {$bron="0";}
                $comments=$_REQUEST['comments'];
                $region=$_REQUEST['region'];
		$src=$_REQUEST['src'];
                $base="";
		$ocup="1";
                $ocup.=$_REQUEST['ocup'];
		$query2="";
		if($ocup=="11" and $b=="objects_arh") {
			$ocup=$_REQUEST['ocup_date'];
			$query="UPDATE ONLY $b SET date_edit='$today', type_object='$type_object', mastername='$mastername', tax='$tax', adress='$adress', tel='$tel', agent='$agent', ocup_date='$ocup', bron_date='$bron', comments='$comments', region='$region', nond='$nond', src='$src' WHERE obj_id=$n;";}
		elseif ($ocup=="1" and $b=="objects_arh") {
			$ocup="0";
			$query2="DELETE FROM $b WHERE obj_id='$n';";
			$b="objects";
			$query="INSERT INTO $b (date_edit,date_insert,type_object,mastername,tax,adress,tel,agent,ocup_date,bron_date,comments,region,nond,src) VALUES ('$today', '$date_insert','$type_object','$mastername','$tax','$adress','$tel','$agent','$ocup','$bron','$comments','$region','$nond','$src');";}
		elseif ($ocup=="11" and $b=="objects") {
			$ocup=$_REQUEST['ocup_date'];
			$query2="DELETE FROM $b WHERE obj_id='$n';";
			$b="objects_arh";
			$query="INSERT INTO $b (date_edit,date_insert,type_object,mastername,tax,adress,tel,agent,ocup_date,bron_date,comments,region,nond,src) VALUES ('$today', '$date_insert','$type_object','$mastername','$tax','$adress','$tel','$agent','$ocup','$bron','$comments','$region','$nond','$src');";}
		elseif ($ocup=="1" and $b=="objects") {
			$ocup="0";
			$query="UPDATE ONLY $b SET date_edit='$today', type_object='$type_object', mastername='$mastername', tax='$tax', adress='$adress', tel='$tel', agent='$agent', ocup_date='$ocup', bron_date='$bron', comments='$comments', region='$region', nond='$nond', src='$src' WHERE obj_id=$n;";}
                if ($type_object!="" and $mastername!="" and $adress!="" and $tax!=0 and $tel!="" and $region!="") {
                        pg_query($dbconn, "BEGIN WORK");
			$res=pg_query($dbconn,$query);
                        if (!$res) {
                                $wtf=pg_query($dbconn, "ROLLBACK");
var_dump($wtf);
                                $top=str_ireplace("%result%","style='background:red;'",$top);}
                        else {
				$wtf=pg_query($dbconn,$query2);
                                $wtf=pg_query($dbconn, "COMMIT");
                                $top=str_ireplace("%result%","style='background:green;'",$top);
				$query_f="WHERE date_edit='$today'";
				$query_o="obj_id DESC";
				$base=show_base($b,$query_f,$query_o,$dbconn,$id);}}
                $top=file_get_contents('menu.tmp');
                $top.=$base;
        break;
//-----------------
//get from edit-cli
//-----------------
        case "ec":
                $n=$_REQUEST['n'];
                $d=$_REQUEST['d'];
                $b=$_REQUEST['b'];
                switch ($b) {
			case "clients_arh":break;
                        case "4":$b="clients_arh";break;
                        default:$b="clients";break;}
		if ($d=="1") {
                        $query="DELETE FROM $b WHERE cli_id='$n';";
                        pg_query($dbconn,$query);
                        $base="<br><br><a href='./index.php?p=car&id=$id&b=$b'>Запись успешно удалена. Вернутся к списку.</a>";
                        $top.=$base;
                        break;}
                else {$base="something wrong ".$d." end";}
                $type_object=$_REQUEST['tip_obj'];
                $mastername=$_REQUEST['mastername'];
                $min_tax=$_REQUEST['min_tax'];
                $max_tax=$_REQUEST['max_tax'];
                $tel=$_REQUEST['tel'];
                $nond=$_REQUEST['nond'];if($nond=="1"){$nond="true";}else {$nond="false";}//1=flag
                $ocup=$_REQUEST['ocup'];
                $d_num=$_REQUEST['d_num'];
                $comments=$_REQUEST['comments'];
                $region=$_REQUEST['region'];
                $base="";
                $query="UPDATE ONLY $b SET date_edit='$today', type_object='$type_object', fio='$mastername', dog_num='$d_num', min_tax='$min_tax', max_tax='$max_tax', tel='$tel', nond='$nond', ocupat='$ocup', comments='$comments', region='$region' WHERE cli_id='$n';";
                if ($type_object=="" or $mastername=="" or $tel=="" or $region=="") {} else {
                        pg_query($dbconn, "BEGIN WORK");
                        $res=pg_query($dbconn,$query);
                        if (!$res) {
                                pg_query($dbconn, "ROLLBACK");
                                $top=str_ireplace("%result%","style='background:red;'",$top);}
                        else {
                                pg_query($dbconn, "COMMIT");
                                $top=str_ireplace("%result%","style='background:green;'",$top);
				$base=show_base($b,'','date_insert DESC',$dbconn,$id);}}
                $top=file_get_contents('menu.tmp');
                $top.=$base;
        break;
//----------
//login page
//----------
	default: $top=file_get_contents('login.tmp');break;}

$menu=file_get_contents('menu.tmp');
$top = str_ireplace("%menu%",$menu,$top);
$top = str_ireplace("%id%",$id,$top);
$top = str_ireplace("%base%",$base,$top);
$top = str_replace("%filter%","",$top);
$footer="</body> <a href='http://muhyta.ru'>©</a> <a href='mailto:muhyta@gmail.com'>muhyta</a></html>";
echo $top.$footer;

?>
