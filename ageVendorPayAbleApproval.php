<?php 
include("./includes/config.inc.php");

if($loginDesignation=="Chairman & Managing Director"){$extraSqlU=" ,approved=1 ";$extraSqlI=" ,approved ";$valSql=",1";}

$theVid=$vid; // get vendor id from the post method user input

$db = mysqli_connect($SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS,$SESS_DBNAME);
 
$grand_amount=0;
$jjj=0; //for serial no.

if($_POST['total_row'])
{
	// print_r($_POST);
	$all_row=$_POST['total_row'];
	$location=$_POST[location];	
		
	for($kkk=1;$kkk<=$all_row;$kkk++)
	{
		$vid_temp=$_POST["vid_".$kkk];
		$amount=$_POST["approved_amount_".$kkk];
		$posl=$_POST["posl_".$kkk];
		
if(!$posl)continue;
		$seven=$_POST['seven'.$kkk];
		$fifteen=$_POST['fifteen'.$kkk];
		$thirty=$_POST['thirty'.$kkk];
		$sixty=$_POST['sixty'.$kkk];
		$ninty=$_POST['ninty'.$kkk];
		$ninetyone=$_POST['nintyone'.$kkk];
		
	
		

		
	if($_POST['approved']=='Approved')
		if($amount)
		{
			// echo "select * from vendorPaymentApproval where posl='$posl' and vid='$vid_temp'";
			mysqli_query($db, "select * from vendorPaymentApproval where posl='$posl' and vid='$vid_temp'");
			if(mysqli_affected_rows($db)<1 && $extraSqlU)
				 $approval_sql="insert into vendorPaymentApproval (
								`vid` ,
								`amount` ,
								`location` ,
								`posl` ,
								`7` ,
								`15` ,
								`30` ,
								`60` ,
								`90` ,
								`91`$extraSqlI) value ('$vid_temp','$amount','$location','$posl','$seven','$fifteen','$thirty','$sixty','$ninty','$ninetyone' $valSql)";
			
			else
			     $approval_sql="update vendorPaymentApproval set
								
								`amount`='$amount' ,
								`7`= '$seven',
								`15`= '$fifteen',
								`30`= '$thirty',
								`60`= '$sixty',
								`90`= '$ninty',
								`91`='$ninetyone' $extraSqlU where vid='$vid_temp' and posl='".$posl."'";
            

			mysqli_query($db, $approval_sql);
            // echo $approval_sql . " ".mysqli_affected_rows($db)." >> $posl";
		}//end of if amount
		
	if($_POST['d_approved']=='Discard Approved')
	{
		$approval_sql="delete from vendorPaymentApproval where location='$location' and vid='$vid_temp'";
		mysqli_query($db, $approval_sql);
	}
		
}	//end of for
}//total row


function check_theValue($posl,$vid,$column, $SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS, $SESS_DBNAME)
{
	$db = mysqli_connect($SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS,$SESS_DBNAME);
	 
	
	$sql="select * from vendorPaymentApproval where posl='$posl' and vid='$vid'";
	$res=mysqli_query($db, $sql);
	$row=mysqli_fetch_array($res);
	//return $row;
// 	echo $sql;
	if($row['amount'])
		return $row;
	else
		return 0;
}

$r=0;	
?>

	<script type="text/javascript">
		the_row_data_switch = new Array();
		the_row_data_switch2 = new Array();
	</script>

	<form name="searchBy" action="./index.php?keyword=aged+vendor+payable+approval" method="post">

	<table width="600" align="center" class="ablue">
		<tr>
			<td colspan="3" align="right" class="ablueAlertHd">vendor payment</td>
		</tr>
<?
	if($Status=='Short by date') $r1='checked';
	else if($Status=='Short by vendor') $r2='checked'; 
	else if($Status=='Short by PO') $r3='checked';
?>
			<tr>
				
						 <td colspan="3">
		 Shrot by: PO No.<input type="radio"  name="Status" <? echo !$r1 || !$r2 ? ' checked ' : "";?>  value="Short by PO"/> 
		 Date<input type="radio"  name="Status" <? echo $r1;?>  value="Short by date"/> 
		 Vendor<input type="radio"  name="Status" <? echo $r2;?> value="Short by vendor" />
		 </td>
			</tr>
		
<tr>
	<th width="200">Select Project</th>
	<td>
		<select name="pcode" onChange="location.href='./index.php?keyword=aged+vendor+payable+approval&pcode='+searchBy.pcode.options[document.searchBy.pcode.selectedIndex].value";>
		<option value="">All Project</option>
		<?
		$sqlp = "SELECT `pcode`,pname from `project`";
		if($loginDesignation=="Procurement Executive")
		$sqlp.=" where pcode='$loginProject' ";
		$sqlp.=" ORDER by pcode ASC";
		//echo $sqlp;
		$sqlrunp= mysqli_query($db, $sqlp);
		while($typel= mysqli_fetch_array($sqlrunp))
		{ 
			echo "<option value='".$typel[pcode]."'";
			if($pcode==$typel[pcode])  echo " SELECTED";
			if(vendorApprovalCounterProjectPayment($typel[pcode],"cr")>0)echo " style=\"background:#ddd; color:red; font-size:14px;\" ";
			echo ">$typel[pcode]--$typel[pname]--(".vendorApprovalCounterProjectPayment($typel[pcode],"cr")." nos)  </option>  ";
		}
		?>
		</select>
	</td>
</tr>

<tr>
	<th width="200">Select Vendor</th>
	<td >
		<select name="vid">
		<option value="">All Vendor</option>
		<?
		include("./includes/config.inc.php");
		$db = mysqli_connect($SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS,$SESS_DBNAME);

		$sqlp = "SELECT distinct vendor.vid,vendor.vname,porder.vid, porder.location 
																from `vendor`,porder 
																WHERE vendor.vid=porder.vid and porder.location = $pcode
																ORDER by vendor.vname ASC ";
		// echo $sqlp;
		$sqlrunp= mysqli_query($db, $sqlp);

		while($typel= mysqli_fetch_array($sqlrunp))
		{
			echo "<option value='".$typel[vid]."'";
			if(vendor_payable_approved_counterPayment($typel[vid],$_POST["pcode"])>0 && $loginDesignation=="Chairman & Managing Director")echo " style=\"background:#ddd; color:red; font-size:14px;\" ";
			if($vid==$typel[vid]) echo " SELECTED ";
			echo ">$typel[vname] ";
			if($loginDesignation=="Chairman & Managing Director")
			echo "--(".vendor_payable_approved_counterPayment($typel[vid],$_POST["pcode"])." nos) ";
			echo "</option>  ";
		}
		?>
		</select>
	</td>
	<td rowspan="2"><input type="submit" name="search" value="Search" style="height:50px;width:100"></td>
</tr>

<br>

		
		
<!-- 		
			<tr>
				<th width="200" height="10"></th>
				<td>


				</td>
				<td rowspan="2"><input type="submit" name="search" value="Search" style="height:50px;width:100; margin:5px;"></td>



			</tr> -->
			

	</table>
</form>

	<? if($search){
if($vid=='') {$theVid=$vid='%';}
if($pcode=='') $pcode='%';
?>
		<form action="" method="post">
			<input type="hidden" name="location" value="<?php echo $pcode;?>" /> <input type="hidden" name="seven28" value="1" />
			<table class="ablue" width="90%" border="1" cellpadding="0" cellspacing="0">
				<tr class="ablueAlertHd" align="center">
					<td height="30">Vendor</td>
					<td><7 days </td>
					<td>8-15 days</td>
					<td>16-30 days</td>
					<td>31-60 days</td>
					<td>61-90 days</td>
					<td>>91 days</td>
					<td>Progress Payable
					<br>from Site</td>
					<td>Approved Amount</td>
				</tr>
<!-- 		<tr>
					<td colspan=9><center><h2>
					Cash Purchase Approval
					</h2></center></td>
				</tr> -->
					
<!-- 		============================================================================	cc=1 credit purchase		 -->	
				
<!-- 				<tr>
				<td colspan=9><center><h2>
					Credit Purchase Approval
					</h2></center></td>
				</tr> -->
		<style>
		 .pdf_class{padding-left: 5px; padding-right: 5px;   background: #fff;text-decoration: none; border: none; transition:background .5s; margin-left:5px;}
		 .pdf_class:hover{background:#c1c1c1; border:none;}
		 
		 .invoiceList p{margin: 0; padding-left: 10px;}
		 .invoiceList p:nth-child(odd){background:#e5e5e5;}
		 .invoiceList p {
				height: 30%;
				margin-bottom: 2px;
			}
		 .invoiceList p span{color:#00f;}
		 
		 td.invoiceList{vertical-align:bottom;}
		 a.pdf_class img {
					width: 16px;
			}
		 .verifyClass{
background: #077900;color: #fff !important;display: inline-block;padding: 1px;border-radius: 5px; font-size: 12px; margin-top:2px;
			}
		 .verifyBTN{
			 margin-left:5px;
			 color:#f00;
		 }
		 .closedClass{
    background: #00f !important;
    color: #fff;
    display: inline-block;
    padding: 1px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 800;
    margin-right: 10px !important;
		 }
	 </style>

<? 
$todat=todat();
$sdate[1][0]=$todat;
$sdate[1][1]=date("Y-m-d",(strtotime($todat)-(8*24*3600)));
$sdate[2][0]=date("Y-m-d",(strtotime($todat)-(9*24*3600)));
$sdate[2][1]=date("Y-m-d",(strtotime($todat)-(16*24*3600)));
$sdate[3][0]=date("Y-m-d",(strtotime($todat)-(17*24*3600)));
$sdate[3][1]=date("Y-m-d",(strtotime($todat)-(30*24*3600)));
$sdate[4][0]=date("Y-m-d",(strtotime($todat)-(31*24*3600)));
$sdate[4][1]=date("Y-m-d",(strtotime($todat)-(60*24*3600)));
$sdate[5][0]=date("Y-m-d",(strtotime($todat)-(61*24*3600)));
$sdate[5][1]=date("Y-m-d",(strtotime($todat)-(90*24*3600)));
$sdate[6][0]=date("Y-m-d",(strtotime($todat)-(91*24*3600)));
$sdate[6][1]='0000-00-00';

//print_r($sdate);
//AND posl like 'PO_142_00302_208'

$sql="SELECT DISTINCT posl,location,vid,poType,activeDate,cc,closedTxt from porder 
WHERE location LIKE '$pcode' 
AND vid LIKE '$theVid' AND posl NOT Like 'EP_%' and (porder.cc='1' or porder.cc='') ";


if($Status=="Short by vendor")$sql="SELECT DISTINCT porder.posl,porder.location,porder.vid,porder.poType,porder.activeDate from porder left join vendor on porder.vid=vendor.vid  WHERE porder.location LIKE '$pcode' 
 AND porder.posl NOT Like 'EP_%'  and (porder.cc='1' or porder.cc='') ";
	
	$sql.=" and porder.posl in (select posl from verify_vendor_payable group by posl) ";

if($Status=="Short by vendor")$sql.=" ORDER by field(porder.vid, '99','85') asc ,vendor.vname ASC";
if($Status=="Short by PO")$sql.=" ORDER by porder.posl asc";
elseif($Status=="Short by date")$sql.="  ORDER BY `porder`.`activeDate` ASC";
else $sql.=" ORDER by porder.posl ASC"; //if($Status=="Short by date")
 //  echo $sql.'<br>';
$sqlq=mysqli_query($db, $sql);
$ii=1;
while($mr=mysqli_fetch_array($sqlq)){
if(isFullpaid($mr[posl])) continue; //if po amount full paid.
// echo isFullpaid($mr[posl]);
 $potype=poType($mr[posl]);  //get po type
$data=array();

$tt=1;
 $location=$mr[location]; 
 $vid=$mr[vid]; 
 $posl=$mr[posl]; 
 $poType=$mr[poType]; 

// echo "<br>===================$posl=======================<br>"; 
 
$dat=poInvoiceDate($posl); // get all invoice date into $dat array
// 	print_r($dat);
$fromDate='0000-00-00';
$this_item_is_not_complete=null;
for($i=1,$k=0;$i<=sizeof($dat);$i++,$k++){ // all invoice date in loop
// 	echo $i."------<br><br>";

	
	
if($poType=='2'){
$pdate=$dat[$i];
	
// 	echo poPayableAmount($posl,$pdate,$pototalAmount,$paidAmount,$exfor,$poType);
// 	echo "<br>";

$diff=(strtotime($todat)-strtotime($pdate))/86400;

 $data[$i][0]=$pdate;
 $data[$i][1]=eqpoActualReceiveAmount_date($posl,$fromDate,$dat[$i]);
//  echo "=======". $data[$i][1]."========"; 
 

 if($diff>=91) $st6+=$data[$i][1]; 
 elseif($diff>=61) $st5+=$data[$i][1];
  elseif($diff>=31) $st4+=$data[$i][1];
    elseif($diff>=16) $st3+=$data[$i][1];
	  elseif($diff>=8) $st2+=$data[$i][1];
	    else $st1+=$data[$i][1];
 $fromDate=$pdate;
}else{
 $invtemp=scheduleReceiveperInvoice($posl,$dat[$i]);
//   echo "<br><br>=====".$posl."=====invouce temp:==";
//    print_r($invtemp);
//   echo "==========<br><br>";

 $pdate='0000-00-00';
 for($j=1;$j<=sizeof($invtemp);$j++){ // check all receive service
 $itemPOArray[$j-1][]="";
 if($invtemp[$j][1]==0) continue;
//  echo $invtemp[$j][0];
//  echo '='.$invtemp[$j][1];
 if($poType=='1')$rdate=mat_receive($invtemp[$j][0],$invtemp[$j][1],$posl,$location);
 if($poType=='3')$rdate=sub_Receive_Po($invtemp[$j][0],$invtemp[$j][1],$posl,$location);

//  print_r($rdate);
// echo "<br>$posl == ".mat_receive($invtemp[$j][0],$invtemp[$j][1],$posl,$location)."<br>";
// echo "========poType=$poType // sub_Receive_Po===".$rdate."===========<br><br>";

 if($rdate=='0000-00-00'){
 	$this_item_is_not_complete[]=$invtemp[$j][0]; //item code
 	//$invtemp[$j][1]; //po quantity
	//echo "==error receiving===".$posl." // ".$pdate."=====";
 	break;
 }
// echo '='.$rdate; 
 if($rdate>$pdate)$pdate=$rdate; //return the final date of receive service.
//echo '='.$pdate; 
//echo "<br><br>";
 }//for j

 if(count($this_item_is_not_complete)>0){
     $item_receiving_completation=0; //flag say item qty is not receiving completed.
 }else{
      $item_receiving_completation=1;
 }
 
// 	echo "<br>posl=$posl   rdate=$rdate item_receiving_completation=$item_receiving_completation && $pdate<br>";

if($pdate!='0000-00-00'){ //if receive some things then pay.
// 	print_r($dat); 
// 	echo "<br>$pdate && $item_receiving_completation --> $posl <br>";
  $diff=(strtotime($todat)-strtotime($pdate))/86400;
	$receiveAmount=subWorkTotalReceive_Po($posl);
	$haveReceiveAmount=scheduleReceiveperInvoiceAmount($posl,$dat[$i]);
	$allInvoiceAmount[$pdate]=$haveReceiveAmount;
}
	
 $itemPOArray[$j-1][]=$item_receiving_completation;
 $itemPOArray[$j-1][]=$haveReceiveAmount;
 $itemPOArray[$j-1][]=$pdate;
	
if($item_receiving_completation==1){
 $data[$i][0]=$pdate;
 $data[$i][1]=$haveReceiveAmount; 
// echo "=====scheduleReceiveperInvoiceAmount===<<".$data[$i][1]."====".$data[$i][0]."====>>";

 if($diff>=91) $st6+=$data[$i][1];
 elseif($diff>=61) $st5+=$data[$i][1];
  elseif($diff>=31) $st4+=$data[$i][1];
    elseif($diff>=16) $st3+=$data[$i][1];
	  elseif($diff>=8) $st2+=$data[$i][1];
	    else $st1+=$data[$i][1]; 
}
}//else
	
	

//  echo "=====================$diff=====================<br><br>";
//   echo $data[$i][0].' = '.$data[$i][1];
// echo "<br>==========================================<br>";
}//for i
//print_r($data);
 $poPaidAmount=poPaidAmount($posl);
//   echo "<br>poPaidAmount=$poPaidAmount; posl=$posl<br>";
 if($poPaidAmount>0){
 if($st6>0){
	if( $poPaidAmount>=$st6) {$poPaidAmount-=$st6;$st6=0;}
	 else{$st6-=$poPaidAmount;$poPaidAmount=0;}
 }//if($st6>0)
  if($st5>0 AND $poPaidAmount>0){
	if( $poPaidAmount>=$st5) {$poPaidAmount-=$st5;$st5=0;}
	 else  {$st5-=$poPaidAmount;$poPaidAmount=0;}
 }//if($st5>0)
  if($st4>0 AND $poPaidAmount>0){
	if( $poPaidAmount>=$st4) {$poPaidAmount-=$st4;$st4=0;}
	 else  {$st4-=$poPaidAmount;$poPaidAmount=0;}
 }//if($st4>0)
  if($st3>0 AND $poPaidAmount>0){
	if( $poPaidAmount>=$st3) {$poPaidAmount-=$st3;$st3=0;}
	 else  {$st3-=$poPaidAmount;$poPaidAmount=0;}
 }//if($st3>0)
  if($st2>0 AND $poPaidAmount>0){
	if( $poPaidAmount>=$st2) {$poPaidAmount-=$st2;$st2=0;}
	 else  {$st2-=$poPaidAmount;$poPaidAmount=0;}
 }//if($st2>0)
  if($st1>0 AND $poPaidAmount>0){
	if( $poPaidAmount>=$st1) {$poPaidAmount-=$st1;$st1=0;}
	 else  {$st1-=$poPaidAmount;$poPaidAmount=0;}
 }//if($st1>0)
}
  $r++; //added by suvro

?>


<tr><td colspan="9" height="10" bgcolor="#FFFFE8"></td></tr>
<tr><td colspan="9" height="2" bgcolor="#0099FF"></td></tr>
<tr>
 <td> 
	 
<? echo $ii.")";?> [<a target="_blank" href="./planningDep/printpurchaseOrder1.php?posl=<? echo $mr[posl];?>"><? echo viewPosl($mr[posl]);?></a>]
 
<?
  $vtemp=vendorName($vid);
  echo $vtemp[vname]; ?>
  [<a target="_blank" href="./print/print_poLedger.php?posl=<? echo $mr[posl];?>&project=<? echo $location;?>&potype=<? echo $poType;?>&vname=<? echo $vtemp[vname];?>">Detail</a>]
<?php
	
// 			if(is_vendor_payable_approved($posl) && ($_SESSION["loginDesignation"]=="Accounts Executive" || $_SESSION["loginDesignation"]=="Chairman & Managing Director")){
// 				echo "<a href='".vendor_payable_approval_pdf($posl)."' target='_blank' class='pdf_class'><img src='./images/pdf.png'></a>";
// 				echo "<div style='background: #077900;color: #fff;display: inline-block;padding: 3px;border-radius: 5px;float: right;font-size: 12px; margin-top:2px;'>Verified</div>";
// 			}
	
	?>

   
 <br>PO Amount: <? echo  number_format(poTotalAmount($mr[posl]),2).' dated '.mydate($mr[activeDate]); ?>
 <br>
<?
	if($poType!=2){
		$a=explode('_',$posl);
		$project= $a[1];
		if(is_po_sch_fail($project,$posl)==1){
			?>
				<div style="
				display: inline-block;
				padding: 4px;
				background: #f00;
				color: #fff;
				border-radius: 5px;
				font-size: 10px;
				">
					Po schedule fail
				</div>
			<?
		}
	}
?>
<div class="invoiceList">
<?php
$sqlp1="SELECT s.itemCode,sum(s.qty) qty,p.rate rate, sum(s.qty)*sum(p.rate) total,s.sdate, s.invoice,p.advanceType FROM poschedule s, porder p  where p.posl='$posl' and s.posl=p.posl and p.itemCode=s.itemCode group by s.sdate  ORDER by s.sdate,s.itemCode DESC";
// echo "$sqlp1";
$sqlrunp1= mysqli_query($db, $sqlp1);
$fromDate="0000-00-00";
$totalInvoiceAmount=0;
$itemCodeAmount=0;
$blankRowData="<p>&nbsp;</p>";
$st1=$st2=$st3=$st4=$st5=$st6=$poPaidAmount=$totalPoPaidAmount=null;
$totalPoPaidAmount=$poPaidAmount=poPaidAmount($posl);
$total_invoice_count = countPoSchedule_invoice($posl);
$i=1;
$invoice_amount_array = invoice_amount_by_posl($posl);
// print_r($invoice_amount_array);
	//advance info
	$poAdvanceArr=getPOadvanceinfo($posl);
	$advancePayableAmount=$poAdvanceArr["amount"]-$totalPoPaidAmount;
	// print_r($poAdvanceArr);
	if($poAdvanceArr["parcent"]>0){
		$InvoiceDiff=(strtotime($todat)-strtotime($mr[activeDate]))/86400;
		$visualDate=mydate($mr[activeDate]);
		$poAdvanceParcent=$poAdvanceArr[parcent];

		if($poPaidAmount>=$poAdvanceArr["amount"]){
			$poPaidAmount-=$poAdvanceArr["amount"]; //remove advance amount from po paid amount
			$current_invoice_adv_adj_amount = invoice_per_month_amount_by_posl($posl,$poPaidAmount,$poAdvanceArr["amount"]);
			$poPaidAmount+=$current_invoice_adv_adj_amount;
		}
		
		//echo "<p>Advance <font color='#00f'>$poAdvanceParcent%</font>: Raised on <span>$visualDate</span></p>";
		echo "<p>Inv $i: Adv <span>$visualDate</span></p>";


		$posl = $mr['posl'];
		$typell=is_po_closed($posl);
		if($typell)$flag=1;
		else
		$flag=0;
		if($flag==1){
			if($advancePayableAmount>0)
			$advancePayableAmount="<p><font color='#00f'>F.C</font></p>";
		}
		else
		$advancePayableAmount=number_format($advancePayableAmount,2);

		$RowData=$advancePayableAmount>0 ? "<p><font color='#00f'>$advancePayableAmount</font></p>" : "<p>&nbsp;0.00</p>";
		
if($InvoiceDiff>=91) {$st6.=$RowData;	$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
 elseif($InvoiceDiff>=61) {$st5.=$RowData;
$st6.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
  elseif($InvoiceDiff>=31) {$st4.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
    elseif($InvoiceDiff>=16) {$st3.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
	  elseif($InvoiceDiff>=8) {$st2.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st1.=$blankRowData;}
	    else {$st1.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;}
	}
	//advance info end
	
	
	
	

$poIsClosedQty=poIsClosedQty($posl);
$isClosingVerified=isClosingVerified($posl,true);

$is_advance_activated = 0;
if($poAdvanceArr["parcent"]>0){
	$is_advance_activated=$i=1;
}
else
	$i=0;

while($typel2=mysqli_fetch_array($sqlrunp1)){
	$invoiceAmount=0;
	//$i=0;
	$edate=$typel2[sdate];
	$indate=$mr[activeDate]; 
	$itemCode=$typel2[itemCode]; 
	$vendorHiddenRow="<input type=\"hidden\" name=\"vid_".$r."\" value=\"".$vid."\" />";
	
//po check start
	$posl = $mr['posl'];
	$typell=is_po_closed($posl);
		if($typell)
		 	$flag=1;
		else
			$flag=0;

		//  if($posl == "PO_220_00031_1405"){
		// 	 print_r($typell);
		// 	 exit;
		//  }
//po check end

  $old_InvoiceDiff = $InvoiceDiff=(strtotime($todat)-strtotime($edate))/86400;
//   echo "$InvoiceDiff = (strtotime($todat)-strtotime($edate))";
	if($poType==1 || $poType==3){
		$InvoiceDiff-=creditFacilityDays($posl);
		$i++;
		$visualDate=date("d-m-Y",strtotime($edate));
		
		// echo "$poType poType";
		// print_r($itemPOArray);
		$current_invoice_adv_adj_amount = invoice_per_month_amount_by_posl($posl,$invoice_amount_array[$i-$is_advance_activated-1],$poAdvanceArr["amount"]);

		foreach($itemPOArray as $itemPOa){
			$invoiceAmount+=$itemPOa[1];
			// print_r($itemPOa[1].">");
		}
		unset($itemPOArray);


		$totalRecieive = totalReceiveA($poType,$posl,$project);
		if($totalRecieive>=($totalInvoiceAmount+$invoiceActualAmount)){
			$invoiceActualAmount=$invoice_amount_array[$i-$is_advance_activated-1];//-$current_invoice_adv_adj_amount;//round(floatval($invoiceAmount),2);
		}
		// print_r($invoiceActualAmount);
		
		
		
		if($poIsClosedQty>0 && $poType==1)
			$itemCodeAmount+=po_mat_receive($itemCode,$posl,$location);

		

			// echo "$poPaidAmount>=$invoiceActualAmount";

if($poPaidAmount>0){
	if($invoiceActualAmount>0){
		// with advance	
		if( $poPaidAmount>=$invoiceActualAmount) {$poPaidAmount-=$invoiceActualAmount;$invoiceActualAmount=0;}
		 else{$invoiceActualAmount-=$poPaidAmount;$poPaidAmount=0;}
	}
}
// echo "$poPaidAmount>=$invoiceActualAmount";


if($old_InvoiceDiff<0)$invoiceActualAmount=0;

// echo "invoiceActualAmount = $invoiceActualAmount. >> << ";
$old_invoiceActualAmount = $invoiceActualAmount;

if($poAdvanceParcent>0){
	$number_of_invoice = countPoSchedule_invoice($posl);
	$current_invoice_payable=$poAdvanceParcent>0 ? pOAdvanceAdjustment($invoiceActualAmount,$poAdvanceParcent,$poAdvanceArr["amount"],$typel2[advanceType],null,($i-$is_advance_activated /* return the real invoice number */),$number_of_invoice,$current_invoice_adv_adj_amount) : 0; //advance adjustment
	$invoiceActualAmount=$current_invoice_payable;
	// echo "advance adjustment $poAdvanceParcent";
}

$totalInvoiceAmount+=$invoiceActualAmount; //total amount collection


echo "<p>Inv $i: Rng <span>$visualDate</span>";
		
		
		
vendorpayable_approved_function($posl,$indate,$mr,$location);
		
		
		
$formatedInvoiceActualAmount=number_format($invoiceActualAmount,2);
		
$RowData="<p><font id='".$r."7' color='#00f' ";
		
if($poIsClosedQty<1)
$RowData.=" onclick=\"add_this_in_function(".str_replace(',','',number_format($invoiceActualAmount,2)).",$r,the_row_data_switch[$r$i],$r$i,7)\"";



if($flag==1){
	if($formatedInvoiceActualAmount>0)
	$RowData.=">F.C</font></p>
	<input type=\"hidden\" name=\"posl_$r\" value=\"$mr[posl]\" />";
	else{
		$RowData.="></font></p><input type=\"hidden\" name=\"posl_$r\" value=\"$mr[posl]\" />";
	}
}
else
$RowData.=">$formatedInvoiceActualAmount</font></p><input type=\"hidden\" name=\"posl_$r\" value=\"$mr[posl]\" />";

$get_row_data=check_theValue($posl,$vid,'7',$SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS,$SESS_DBNAME);
$RowData.="<input type=\"hidden\" value=\"$get_row_data[5]\" name=\"'seven$r\" id=\"".$r."__7\" />";
	

if($InvoiceDiff>=91) {$st6.=$RowData;	$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
 elseif($InvoiceDiff>=61) {$st5.=$RowData;
$st6.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
  elseif($InvoiceDiff>=31) {$st4.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
    elseif($InvoiceDiff>=16) {$st3.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
	  elseif($InvoiceDiff>=8) {$st2.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st1.=$blankRowData;}
	    else {$st1.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;}

	}elseif($poType==2){
		if($poIsClosedQty>0)
			$itemCodeAmount+=eqpoActualReceiveAmountItemCode($posl,$itemCode);
		
		while($indate<$edate){
			$i++;
			$indateTemp=strtotime($indate);
			$indate=date("Y-m-d",mktime(0, 0, 0, date("m",$indateTemp)+1, '01',  date("Y",$indateTemp)));
			$visualDate=date("d-m-Y",strtotime($indate));
			if(strtotime($todat)<strtotime($indate))continue; //if end date exceed the current date
			$invoiceActualAmount=round(eqpoActualReceiveAmount_date($posl,$fromDate,$indate),2);
			echo "<p>Inv $i: Rng <span>$visualDate</span>"; 
// 			echo $invoiceActualAmount>0 ? "<font style='float:right'>Tk. <font color='#00f'>$invoiceActualAmount</font></font></p>" : "</p>";

 if($poPaidAmount>0){
	 if($invoiceActualAmount>0){
		if( $poPaidAmount>=$invoiceActualAmount){$poPaidAmount-=$invoiceActualAmount;$invoiceActualAmount=0;}
		 else{$invoiceActualAmount-=$poPaidAmount;$poPaidAmount=0;}
	 }
}
		// echo "invoiceActualAmount = $invoiceActualAmount. >> << ";
$old_invoiceActualAmount = $invoiceActualAmount;
$number_of_invoice = countPoSchedule_invoice($posl);
$current_invoice_adv_adj_amount = invoice_per_month_amount_by_posl($posl,$invoiceActualAmount,$poAdvanceArr["amount"]);
$current_invoice_payable=$poAdvanceParcent>0 ? pOAdvanceAdjustment($invoiceActualAmount,$poAdvanceParcent,$poAdvanceArr["amount"],$typel2[advanceType],null,($i-$is_advance_activated /* return the real invoice number */),$number_of_invoice,$current_invoice_adv_adj_amount) : 0; //advance adjustment
$invoiceActualAmount=$current_invoice_payable;
		// echo "advance adjustment $poAdvanceParcent";

$totalInvoiceAmount+=$invoiceActualAmount; //total amount collection
vendorpayable_approved_function($posl,$indate,$mr,$location);

			$formatedInvoiceActualAmount=number_format($invoiceActualAmount,2);
			if($invoiceActualAmount>0){
				$RowData="<p><font id='".$r."7' color='#00f' ";
if($poIsClosedQty<1)
$RowData.=" onclick=\"add_this_in_function(".str_replace(',','',number_format($invoiceActualAmount,2)).",$r,the_row_data_switch[$r$i],$r$i,7)\"";
	


if($flag==1){
	if($formatedInvoiceActualAmount>0)
	$RowData.=">F.C</font></p>
	<input type=\"hidden\" name=\"posl_$r\" value=\"$mr[posl]\" />";
}
else
$RowData.=">$formatedInvoiceActualAmount</font></p>
<input type=\"hidden\" name=\"posl_$r\" value=\"$mr[posl]\" />";

$get_row_data=check_theValue($posl,$vid,'7',$SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS,$SESS_DBNAME);
$RowData.="<input type=\"hidden\" value=\"$get_row_data[5]\" name=\"'seven$r\" id=\"".$r."__7\" />";
}else 
	$RowData.="<p>&nbsp;</p>";


$InvoiceDiff=(strtotime($todat)-strtotime($indate))/86400;
$InvoiceDiff-=creditFacilityDays($posl);

if($InvoiceDiff>=91) {$st6.=$RowData;	$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
 elseif($InvoiceDiff>=61) {$st5.=$RowData;
$st6.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
  elseif($InvoiceDiff>=31) {$st4.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
    elseif($InvoiceDiff>=16) {$st3.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st2.=$blankRowData;$st1.=$blankRowData;}
	  elseif($InvoiceDiff>=8) {$st2.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st1.=$blankRowData;}
	    else {$st1.=$RowData;
$st6.=$blankRowData;$st5.=$blankRowData;$st4.=$blankRowData;$st3.=$blankRowData;$st2.=$blankRowData;}
			
			$RowData="";
			$fromDate=$indate;
		}//while
	}
	$actualPoAmount=actualPOreceiveAmount($posl,$poType,$location);
	$closingAmount=$actualPoAmount-$totalPoPaidAmount-$poAdvanceArr["amount"];
	$FinalAmount=($closingAmount) > 0 ? $closingAmount : "<font color='#f00'>($closingAmount)</font>";
}
?>
</div>
	</td>
 <td align="right" class="invoiceList"><? if($st1)echo $st1;echo $vendorHiddenRow;?></td>
 <td align="right" class="invoiceList"><? if($st2)echo $st2;?></td>
 <td align="right" class="invoiceList"><? if($st3)echo $st3;?></td>
 <td align="right" class="invoiceList"><? if($st4)echo $st4;?></td>
 <td align="right" class="invoiceList"><? if($st5)echo $st5;?></td>
 <td align="right" class="invoiceList"><? if($st6)echo $st6;?></td>
	<td align="right">
<?
$sqlp = "SELECT * from `popayments` WHERE (posl LIKE '". $mr[posl]."') AND (paidAmount < totalAmount) order by popID ASC ";
//echo $sqlp;
$sqlrunp= mysqli_query($db, $sqlp);
//echo mysql_error();
while($re_s=mysqli_fetch_array($sqlrunp)){


if($pcode AND $vid){


$potype=poType($re_s[posl]);
$t=explode('_',$re_s[posl]);
if($potype==1) $receiveAmount=poTotalreceive($re_s[posl],$t[1]);
if($potype==2) $receiveAmount=eqpoActualReceiveAmount($re_s[posl]);
if($potype==3) $receiveAmount=subWorkTotalReceive_Po($re_s[posl]);
$paid=poPaidAmount($re_s[posl]);

	$currentPayable=foodinfAmount($re_s[totalAmount],$receiveAmount,$paid,$re_s[posl]); 
   $currentPayable=round($currentPayable,2);
  
  } 
  

 echo number_format($currentPayable,2)."<br>";
   }?>
						</td>
 	<td align="right">
		<input type="text" style="text-align:right" id="approved_amount_<?php echo $r; ?>" name="approved_amount_<?php echo $r; ?>" value="<?php if($get_row_data['amount']){echo $get_row_data['amount'];$grand_amount=$get_row_data['amount']+$grand_amount;} else echo 0;?>"
 <?php //if(!is_vendor_payable_approved($posl) && !$isClosingVerified)echo "readonly"; ?> class="approved_amount" />
	</td>
</tr>


<?
//po_force_close (Safa)
include("./includes/config.inc.php");
$db = mysqli_connect($SESS_DBHOST, $SESS_DBUSER,$SESS_DBPASS,$SESS_DBNAME);

$posl = $mr['posl'];
$a = explode('_',$posl);
$p = $a[1];

$sqlp = "SELECT * FROM `po_force_close_approval` WHERE is_complete = 1 and posl = '$posl'";
//echo $sqlp;
$sqlrunp= mysqli_query($db, $sqlp);
 while($typel= mysqli_fetch_array($sqlrunp)){
	 $i++;
	 if($typel){
	?>
			<tr align="left" class="invoiceList">
				<td>
					<? echo "<p>Inv $i: Fnl <span>$typel[edate]</span>"; ?>
				</td>
				<td colspan="6" style='border-right:none'>
					<?
					echo "<p>$typel[text]"; 
					echo "<br>";

					$poType=$mr['poType']; 
					$a=explode('_',$posl);
	                $project= $a[1];
					$poTotalRecieve = number_format(totalReceiveA($poType,$posl,$project),2);
					$poPaidAmount =number_format(poPaidAmount($posl),2);
					
					echo "<p>Recieve amount : <span>$poTotalRecieve</span> , Paid amount : <span>$poPaidAmount</span> ";
					//echo "<p>Paid amount : <span>$poPaidAmount</span>";
					?>
				</td>
				<td colspan="2" style='border-left:none;border-right:none'>
					<?
					$poType=$mr['poType']; 
					$a=explode('_',$posl);
	                $project= $a[1];
					$poTotalRecieve = totalReceiveA($poType,$posl,$project);
					$poPaidAmount = poPaidAmount($posl);
					$amountt = number_format(($poTotalRecieve-$poPaidAmount),2);
					echo "<p>Force close amount : <span>$amountt</span>"; 
					
					?>
				</td>
			</tr>
<?	    
    }  
}
?>


<!-- 
<tr>
	<td colspan="6" align="right">	
<?php
//Total Amount Information	
	
	// if($poIsClosedQty>0 && ($itemCodeAmount>0 || $poType==3)){
		
	// if($poType==3){
	// 	$itemCodeAmount+=subWorkTotalReceive_Po($posl);
	// }
	// 	$sql_aux="select * from auxiliary_vendorpayment where posl like '$posl'";
	// 	$q_aux=mysqli_query($db,$sql_aux);
	// 	$row_aux=mysqli_fetch_array($q_aux);


	
	//	echo "<p class='closedClass'>Force Close</p>"; //closing row
	// 	if($mr[closedTxt])echo "<b>Reason: </b><i>$mr[closedTxt]</i>";
	// 		echo "<br>";
	// 		if($row_aux["amount"])
	// 			echo "F.C amount: ".number_format($row_aux["amount"],2)." ";
			
	// 		if($row_aux["edate"])
	// 			echo "F.C Date: ".date("d/m/Y",strtotime($row_aux["edate"]));
	// 	$FinalAmountRow="<p><b><font color='#00f' ";
		
	// 	if($isClosingVerified){
	// 		$i++;
	// 		vendorpayable_approved_function($posl,"",$mr,$location,"c");	$FinalAmountRow.=" onclick=\"add_this_in_function(".str_replace(',','',number_format($FinalAmount,2)).",$r,the_row_data_switch[$r$i],$r$i,7)\"";
	// 	}
		
	// 	$FinalAmountRow.=">".number_format($FinalAmount,2)."</font></b></p>";
	// }else{
	// 	$FinalAmountRow=$blankRowData;
	// } ?></td>
	<td align=right><?php //	echo $FinalAmountRow; ?></td>
	<td></td>
	<td></td>
	</tr> -->



	<script type="text/javascript">

						
<?php if($st1+$st2+$st3+$st4+$st5+$st6==0)
		{ 
		$all_js='
		document.getElementById("ro_'.($r-1).'").style.display="none";'.$all_js;
	 }
						
						?>



						function add_this_in_function_load(row, get_the_switch_value, the_row_and_col, col) {
							document.getElementById(the_row_and_col).style.background = "#ccc";
							the_row_data_switch[the_row_and_col] = 1;
							document.getElementById(row + '__' + col).value = 1;
}
<?php

			 if($get_row_data['7']==1)
	 	echo "add_this_in_function_load(".$r.',0,'.$r.'7'.',7'.");";
	 if($get_row_data['15']==1)
	 	echo "add_this_in_function_load(".$r.',0,'.$r.'15'.',15'.");";
	 if($get_row_data['30']==1)
	 	echo "add_this_in_function_load(".$r.',0,'.$r.'30'.',30'.");";
	 if($get_row_data['60']==1)
	 	echo "add_this_in_function_load(".$r.',0,'.$r.'60'.',60'.");";
	 if($get_row_data['90']==1)
	 	echo "add_this_in_function_load(".$r.',0,'.$r.'90'.',90'.");";
	 if($get_row_data['91']==1)
	 	echo "add_this_in_function_load(".$r.',0,'.$r.'91'.',91'.");";
		
		
	?>
					</script>



<? $st1=$st2=$st3=$st4=$st5=$st6=0;$poPaidAmount=0;$ii++;
 }//while?>

						<tr>
							<td colspan="6"></td>
							<td align="right"><input type="submit" value="Discard Approved" name="d_approved" id="d_approved" onclick="document.getElementById('approved_button').value='';" /></td>
							<td align="right"><input type="submit" value="Approved" id="approved_button" name="approved" <?php //if($grand_amount>0)echo 'disabled="disabled"'; ?> onclick="document.getElementById('d_approved').value='';" />
								<input type="hidden" value="<?php echo $r; ?>" name="total_row" />
							</td>
							<td>&nbsp;<span>Total:</span><strong style="color:#FF0000; text-align:center; margin-left:50px; text-align:right" id="final_total_amount"><?php if($grand_amount)echo $grand_amount; else echo 0;?></strong></td>
						</tr>
						<script type="text/javascript">

								try {							<?php

echo $all_js;
?>
									
								}catch(err) {
	
}


							function add_this_in_function(amount, row, get_the_switch_value, the_row_and_col, col) {
			$(document).ready(function(){
								if (get_the_switch_value == 1) {
									$("#"+the_row_and_col).css({"background" : "#fff"});
									the_row_data_switch[the_row_and_col] = 0;
									$('input#approved_amount_' + row).val((parseFloat($('input#approved_amount_' + row).val()) - parseFloat(amount)).toFixed(2));
									document.getElementById(row + '__' + col).value = 0;
								}else{
									$("#"+the_row_and_col).css({"background" : "#ccc"});
									the_row_data_switch[the_row_and_col] = 1;
									$('input#approved_amount_' + row).val((parseFloat(amount) + parseFloat($('input#approved_amount_' + row).val())).toFixed(2));
									document.getElementById(row + '__' + col).value = 1;
								}
								final_amount = 0;
								for (nn = 1; nn <= <?php echo $r;?>; nn++) {
									final_amount = parseFloat(document.getElementById('approved_amount_' + nn).value) + final_amount;
								}
								document.getElementById('final_total_amount').innerHTML = final_amount.toFixed(2);
				});
							}
							
							function manualInputInAmount(){
								final_amount = 0;
								for (nn = 1; nn <= <?php echo $r;?>; nn++) {
									final_amount = parseFloat(document.getElementById('approved_amount_' + nn).value) + final_amount;
								}
								document.getElementById('final_total_amount').innerHTML = final_amount.toFixed(2);
							}
							
							
							$(document).ready(function(){
								$("input.approved_amount").change(function(){
									manualInputInAmount();
								});
							});
							
							
						</script>
			</table>
		</form>
		<? }//if?>