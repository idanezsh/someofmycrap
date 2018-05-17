#!/usr/bin/php
<?
$qmail = file("/etc/rc.pineapp/rc.qmail");
foreach ($qmail as $c) {
	if (preg_match("/QMAIL_ADMIN=/i", $c)) {
		$QMAIL_ADMIN = trim($c);
		$QMAIL_ADMIN = explode("=",$QMAIL_ADMIN);
		$QMAIL_ADMIN = substr(trim($QMAIL_ADMIN[1]), 1, -1);
	}
}
/*
function quoted_printable_encode_pa($input, $line_max = 75) {
	$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
	$lines = preg_split("/(?:\n|\r|\n)/", $input);
	$linebreak = "=\n";
	$line_max = $line_max - strlen($linebreak);
	#escape = "=";
	$output = "";
	$cur_conv_line = "";
	$length = 0;
	$whitespace_pos = 0;
	$addtl_chars = 0;

	// iterate lines
	for ($j=0; $j<count($lines); $j++) {
		$line = $lines[$j];
		$linlen = strlen($line);

		// iterate chars
		for ($i = 0; $i < $linlen; $i++) {
			$c = substr($line, $i, 1);
			$dec = ord($c);

			$length++;

			if ($dec == 32) {
				// space occurring at end of line, need to encode
				if (($i == ($linlen - 1))) {
					$c = "=20";
					$length += 2;
				}

				$addtl_chars = 0;
				$whitespace_pos = $i;
			} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
				$h2 = floor($dec/16); $h1 = floor($dec%16);
				$c = $escape . $hex["$h2"] . $hex["$h1"];
				$length += 2;
				$addtl_chars += 2;
			}

			// length for wordwrap exceeded, get a newline into the text
			if ($length >= $line_max) {
				$cur_conv_line .= $c;

				// read only up to the whitespace for the current line
				$whitesp_diff = $i - $whitespace_pos + $addtl_chars;

				if (($i + $addtl_chars) > $whitesp_diff) {
					$output .= substr($cur_conv_line, 0, (strlen($cur_conv_line) -
							$whitesp_diff)) . $linebreak;
					$i =  $i - $whitesp_diff + $addtl_chars;
				} else {
					$output .= $cur_conv_line . $linebreak;
				}

				$cur_conv_line = "";
				$length = 0;
				$whitespace_pos = 0;
			} else {
				// length for wordwrap not reached, continue reading
				$cur_conv_line .= $c;
			}
		} // end of for

		$length = 0;
		$whitespace_pos = 0;
		$output .= $cur_conv_line;
		$cur_conv_line = "";

		if ($j<=count($lines)-1) {
			$output .= "\n";
		}
	} // end for
	return trim($output);
} */

function findTemplate($type,$Owner) {
	$_SERVER["DOCUMENT_ROOT"] = "/srv/www/htdocs";
	require_once($_SERVER["DOCUMENT_ROOT"]."/manage/main_incs/pg_connect.php");
	if(!isset($dbcon)) $dbcon = pg_connect_DB();
	unset($sql);unset($row);unset($res);
	$sql = "SELECT * FROM cust.customer WHERE cust_id = $Owner";
	$res = pg_query($sql);
	while ($row = pg_fetch_assoc($res)) {
		$cust_id 		= $row['cust_id'];
		$owner_id 		= $row['owner_id'];
			
		//$cust_template 			= $row['cust_template'];
		$cust_company_name 		= $row['cust_company_name'];
		$cust_product_name 		= $row['cust_product_name'];
		$cust_notify_addr 		= $row['cust_notify_addr'];
		$cust_company_url 		= $row['cust_company_url'];
		$cust_support_email 	= $row['cust_support_email'];
		//$cust_web_faq 			= $row['cust_web_faq'];
		//$cust_logo 				= $row['cust_logo'];
		$cust_daily_logo 		= $row['cust_daily_report_logo'];
		//$cust_ico 				= $row['cust_ico'];
		$cust_oem				= $row['cust_oem'];
		//$cust_ico_external_link = $row['cust_ico_external_link'];
		//$owner_login_bg			= $row['owner_login_bg'];
		//$owner_logo				= $row['owner_logo'];
	}
	switch ($type) {
		case "companyname":
			if(trim($cust_company_name)!="" && $_SESSION['cust_oem____'] && $cust_oem=='t') {
				$_SESSION['UI_COMPANY_NAME'] = $cust_company_name;
				return;
			} else if($cust_id=="1" && $owner_id=="1") {
				$_SESSION['UI_COMPANY_NAME'] = $cust_company_name;
				return;
			} else
				findTemplate($type,$owner_id);
			break;
		case "companyproductname":
			if(trim($cust_product_name)!="" && $_SESSION['cust_oem____'] && $cust_oem=='t') {
				$_SESSION['UI_PROD_NAME'] = $cust_product_name;
				return;
			} else if($cust_id=="1" && $owner_id=="1") {
				$_SESSION['UI_PROD_NAME'] = $cust_product_name;
				return;
			} else
				findTemplate($type,$owner_id);
			break;
		case "notifyemail":
			if(trim($cust_notify_addr)!="" && $_SESSION['cust_oem____'] && $cust_oem=='t') {
				$_SESSION['UI_NOTIFY_EMAIL'] = $cust_notify_addr;
				return;
			} else if($cust_id=="1" && $owner_id=="1") {
				$_SESSION['UI_NOTIFY_EMAIL'] = $cust_notify_addr;
				return;
			} else
				findTemplate($type,$owner_id);
			break;
		case "companyurl":
			if(trim($cust_company_url)!="" && $_SESSION['cust_oem____'] && $cust_oem=='t') {
				$_SESSION['UI_WEB_LINK'] = $cust_company_url;
				return;
			} else if($cust_id=="1" && $owner_id=="1") {
				$_SESSION['UI_WEB_LINK'] = $cust_company_url;
				return;
			} else
				findTemplate($type,$owner_id);
			break;
		case "supportemail":
			if(trim($cust_support_email)!="" && $_SESSION['cust_oem____'] && $cust_oem=='t') {
				$_SESSION['UI_SUPPORT_EMAIL'] = $cust_support_email;
				return;
			} else if($cust_id=="1" && $owner_id=="1") {
				$_SESSION['UI_SUPPORT_EMAIL'] = $cust_support_email;
				return;
			} else
				findTemplate($type,$owner_id);
			break;
		case "daily_logo":
			if(trim($cust_daily_logo)!="" && $_SESSION['cust_oem____'] && $cust_oem=='t') {
				$_SESSION['UI_DAILY_LOGO'] = $cust_daily_logo;
				return;
			} else if($cust_id=="1" && $owner_id=="1") {
				$_SESSION['UI_DAILY_LOGO'] = $cust_daily_logo;
				return;
			} else
				findTemplate($type,$owner_id);
			break;
	}
	@pg_close($dbcon);
}
function findFirstFalseFlag($Owner) {
	$_SERVER["DOCUMENT_ROOT"] = "/srv/www/htdocs";
	require_once($_SERVER["DOCUMENT_ROOT"]."/manage/main_incs/pg_connect.php");
	if(!isset($dbcon)) $dbcon = pg_connect_DB();

	unset($sql);unset($row);unset($rs);
	$sql = "SELECT cust_id,cust_oem,owner_id FROM cust.customer WHERE cust_id=$Owner AND delete_ind='f'";
	$rs = pg_query($sql);
	while ($row=pg_fetch_array($rs)) {
		$cust_id____ 		= $row["cust_id"];
		$cust_oem____ 		= $row["cust_oem"];
		$owner_id____ 		= $row["owner_id"];
	}
	if($cust_oem____=='f') {
		findTemplate('daily_logo',$owner_id____);
		findTemplate('companyname',$owner_id____);
		findTemplate('companyproductname',$owner_id____);
		findTemplate('notifyemail',$owner_id____);
		findTemplate('companyurl',$owner_id____);
		findTemplate('supportemail',$owner_id____);
		$_SESSION['cust_oem____'] = false;
	}
	if($cust_id____==1) {
		findTemplate('daily_logo',1);
		findTemplate('companyname',1);
		findTemplate('companyproductname',1);
		findTemplate('notifyemail',1);
		findTemplate('companyurl',1);
		findTemplate('supportemail',1);
	}
	if($cust_oem____!='f' && $cust_id____!=1)
		findFirstFalseFlag($owner_id____);
	@pg_close($dbcon);
}
#########
$_SESSION['cust_oem____'] = true;
findFirstFalseFlag(1);
if($_SESSION['cust_oem____']) {
	findTemplate('daily_logo',1);
	findTemplate('companyname',1);
	findTemplate('companyproductname',1);
	findTemplate('notifyemail',1);
	findTemplate('companyurl',1);
}
#########

$temp = explode(";base64,",$_SESSION['UI_DAILY_LOGO']);
$temp1 = explode("/",$temp[0]);
$image_ext = trim($temp1[1]);
if(strtolower($image_ext)=="jpeg") {
	$image_ext = "jpg";
}
$temp2 = explode(":",$temp[0]);
$image_mime = trim($temp2[1]);
$_SERVER["DOCUMENT_ROOT"] = "/srv/www/htdocs";
require_once($_SERVER["DOCUMENT_ROOT"]."/manage/db.fuctions/db.connector.class.php");
$DB_CONNECT_DAILY_RULES = new ActionsDbHandler();
require_once($_SERVER["DOCUMENT_ROOT"]."/manage/main_incs/pg_connect.php");
if(!isset($dbcon)) $dbcon = pg_connect_DB();

unset($rez_);unset($r_);
unset($status);

// last day
$sql = "SELECT * FROM log.msg_info_daily_report WHERE date_trunc('day',\"time\") = date_trunc('day',now())";
$rez_ = pg_query($sql);
$num_res = pg_num_rows($rez_);
while ($r_ = pg_fetch_assoc($rez_)) {
	$status[] = trim($r_['status']);
}



//print_r($status);

$un_status 		= 0;
$allow_status 	= 0;
$block_status 	= 0;
$del_status 	= 0;
$park_status 	= 0;
for($i=0;$i<sizeof($status);$i++) {
	switch ($status[$i]) {
		case "-1":
			$un_status++;
			break;
		case "66304":
		case "0":
		case "7":
		case "67584":
			$allow_status++;
			break;
		case "2":
			$block_status++;
			break;
		case "3":
			$del_status++;
			break;
		case "1":
		case "66305":
			$park_status++;
			break;
	}
}

require($_SERVER["DOCUMENT_ROOT"].'/manage/fpdf/diag.php');
$pdf = new PDF_Diag();
$pdf->AddPage();

$data = array(	'Passed' => $allow_status, 
			'Blocked' => $block_status, 
			'Deleted' => $del_status,
			'Parked' => $park_status,
			'Unknown' => $un_status
		
);
$tempimagetype_ = explode(";base",$_SESSION['UI_DAILY_LOGO']);
$tempimagetype_1 = explode("/",$tempimagetype_[0]);
$tempimagetype = $tempimagetype_1[1];
$image1 = $_SESSION['UI_DAILY_LOGO'];
//$pdf->Image($image1,10,10);
$pdf->Image($image1,null, null, 0, 0, $tempimagetype);
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 0, $_SESSION['UI_COMPANY_NAME'].' '.$_SESSION['UI_PROD_NAME'], 0, 1);
$pdf->Ln(8);

$pdf->Ln(8);
$pdf->Ln(8);

//Pie chart
$pdf->SetFont('Arial', 'BU', 16);
$pdf->Cell(0, 5, 'Email\'s Traffic Report - '.date('Y-m-d'), 0, 1);
$pdf->Ln(8);

$pdf->SetFont('Arial', '', 12);
$valX = $pdf->GetX();
$valY = $pdf->GetY();

$pdf->Cell(30, 5, 'Emails Passed:');
$pdf->Cell(15, 5, $data['Passed'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Emails Blocked:');
$pdf->Cell(15, 5, $data['Blocked'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Emails Deleted:');
$pdf->Cell(15, 5, $data['Deleted'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Emails Parked:');
$pdf->Cell(15, 5, $data['Parked'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Status Unknown:');
$pdf->Cell(15, 5, $data['Unknown'], 0, 0, 'R');
$pdf->Ln();


$pdf->Ln(8);

$pdf->SetXY(20, $valY+50);
$col1=array(14,159,14);
$col2=array(221,21,21);
$col3=array(249,177,36);
$col4=array(29,81,250);
$col5=array(181,4,225);

$pdf->PieChart(150, 335, $data, '%l (%p)', array($col1,$col2,$col3,$col4,$col5));
$pdf->SetXY($valX, $valY + 40);

$pdf->Ln(8);


unset($rez_);unset($r_);
unset($status);

// last day
$sql = "SELECT * FROM log.msg_info_daily_report WHERE date_trunc('day',\"time\") <= date_trunc('day',now()) AND date_trunc('day',\"time\")> date_trunc('day',now()- interval '3 day') ORDER BY \"time\" DESC";
$rez_ = pg_query($sql);
$num_res_last = pg_num_rows($rez_);
while ($r_ = pg_fetch_assoc($rez_)) {
	$status[] = trim($r_['status']);
}

@pg_close($dbcon);

//print_r($status);

$un_status1 	= 0;
$allow_status1 	= 0;
$block_status1 	= 0;
$del_status1 	= 0;
$park_status1 	= 0;
for($i=0;$i<sizeof($status);$i++) {
	switch ($status[$i]) {
		case "-1":
			$un_status1++;
			break;
		case "66304":
		case "0":
		case "7":
		case "67584":
			$allow_status1++;
			break;
		case "2":
			$block_status1++;
			break;
		case "3":
			$del_status1++;
			break;
		case "1":
		case "66305":
			$park_status1++;
			break;
	}
}

//require($_SERVER["DOCUMENT_ROOT"].'/manage/fpdf/diag.php');
//$pdf = new PDF_Diag();
$pdf->AddPage();

$data = array(	'Passed' => $allow_status1,
		'Blocked' => $block_status1,
		'Deleted' => $del_status1,
		'Parked' => $park_status1,
		'Unknown' => $un_status1

);

$pdf->Ln(8);
$pdf->Ln(8);
$pdf->Ln(8);
//Pie chart
$pdf->SetFont('Arial', 'BU', 14);
$pdf->Cell(0, 5, 'Email\'s Traffic Report (last 3 days)', 0, 1);
$pdf->Ln(8);

$pdf->SetFont('Arial', '', 12);
$valX = $pdf->GetX();
$valY = $pdf->GetY();

$pdf->Cell(30, 5, 'Emails Passed:');
$pdf->Cell(15, 5, $data['Passed'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Emails Blocked:');
$pdf->Cell(15, 5, $data['Blocked'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Emails Deleted:');
$pdf->Cell(15, 5, $data['Deleted'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Emails Parked:');
$pdf->Cell(15, 5, $data['Parked'], 0, 0, 'R');
$pdf->Ln();

$pdf->Cell(30, 5, 'Status Unknown:');
$pdf->Cell(15, 5, $data['Unknown'], 0, 0, 'R');
$pdf->Ln();


$pdf->Ln(8);

$pdf->SetXY(20, $valY+50);
$col1=array(14,159,14);
$col2=array(221,21,21);
$col3=array(249,177,36);
$col4=array(29,81,250);
$col5=array(181,4,225);

$pdf->PieChart(150, 335, $data, '%l (%p)', array($col1,$col2,$col3,$col4,$col5));
$pdf->SetXY($valX, $valY + 40);

$pdf->Ln(150);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 0, $_SESSION['UI_COMPANY_NAME'].' '.utf8_decode('©').' '.date('Y').', All Rights Reserved.', 0, 0); 

`cat /var/data/multilog/smtp/current | tai64nlocal | gawk -F= '{ print $3","$0 }' | sed 's/././' > /tmp/SMTP.txt`;
$dir = "/tmp/";
$filename = "SMTP.txt";
//df->Output($dir.$filename,'F');
//$pdf->Output(date('Y-m-d').'.pdf','D');

if($num_res_last>0 || $num_res>0) {
	$file_path = $dir.$filename;
	$file = fopen($file_path,'rb');
	$data = fread($file,filesize($file_path));
	fclose($file);
	$content = chunk_split(base64_encode($data));
	//echo $content;exit;
	
	$style = '<style>';
	//$style.= '.title{background-color: #f1f1f1;text-align: '.$text_align.';font-family: Arial;font-size: 100%;}';
	$style.= '.tdtext{font-family: Arial;font-size: 100%;}';
	$style.= '.footertext{font-family: Arial;font-size: 80%;}';
	//$style.= '.toptext{text-align: '.$text_align.';font-family: Arial;font-size: 100%;background-color:#fff;color:#000;}';
	//$style.= '.word-wrap {white-space: pre-wrap;      /* CSS3 */   white-space: -moz-pre-wrap; /* Firefox */    white-space: -pre-wrap;     /* Opera <7 */   white-space: -o-pre-wrap;   /* Opera 7 */    word-wrap: break-word;}';
	$style.= '</style>';
	
	$str = "";
	$str.= '<html>';
	$str.= '<head>';
	$str.= '<title>Administrator Daily Report</title>';
	$str.= '<meta charset="utf-8">';
	$str.= '</head>';
	$str.=$style;
	$str.='<body>';
	$str.= '<div><img src="cid:dailyreport.'.$image_ext.'" border="0"></div><br><br>';
	#	$str.= '<div class="tdtext" style="margin:5px;">Passed: '.$allow_status.'</div>';
	#	$str.= '<div class="tdtext" style="margin:5px;">Blocked: '.$block_status.'</div>';
	#	$str.= '<div class="tdtext" style="margin:5px;">Deleted: '.$del_status.'</div>';
	#	$str.= '<div class="tdtext" style="margin:5px;">Parked: '.$park_status.'</div>';
	#	$str.= '<div class="tdtext" style="margin:5px;">Unknown: '.$un_status.'</div>';
	$str.= '<br><br><div class="tdtext" style="margin:5px;">SMTP file attached</div>';
	$str.= '<br><br><br>';

	$str.='<div class="footertext">© '.date('Y').' '.trim($_SESSION['UI_COMPANY_NAME']).', All Rights Reserved</div>';
	
	//$str.='<a href="'.trim($_SESSION['UI_WEB_LINK']).'" target="_blank" style="text-decoration:none;"'.trim($_SESSION['UI_COMPANY_NAME']).'</a> ';
	//$str.='</div>';
	$str.='</body>';
	$str.='</html>';
	
	
	
	function mail_file( $to, $subject, $messagehtml, $from, $fileatt, $replyto="" ) {
		global $image_ext,$image_mime;
		echo "Sending to: ".$to."\n";
		//$from_list = explode("#",$from);
		// handles mime type for better receiving
		//$ext = strrchr( $fileatt , '.');
		//$ftype = "";
		/*if ($ext == ".doc") $ftype = "application/msword";
		if ($ext == ".jpg") $ftype = "image/jpeg";
		if ($ext == ".gif") $ftype = "image/gif";
		if ($ext == ".zip") $ftype = "application/zip";
		if ($ext == ".pdf") $ftype = "application/pdf";*/
		//if ($ftype=="") $ftype = "application/octet-stream";
		$ftype = "plain/text";
		 
		// read file into $data var
		$file = fopen($fileatt, "rb");
		$data = fread($file,  filesize( $fileatt ) );
		fclose($file);
	
		// split the file into chunks for attaching
		$content = chunk_split(base64_encode($data));
		$uid_m = md5(uniqid(time()))."m";
		$uid_a = md5(uniqid(time()))."a";
		$uid_r = md5(uniqid(time()))."r";
	
		// build the headers for attachment and html
		$h = "From: ".$from."\n";
		if ($replyto) $h .= "Reply-To: ".$replyto."\n";
		$h .= "MIME-Version: 1.0\n";
		$h .= "Content-Type: multipart/mixed; boundary=\"".$uid_m."\"\n\n";
		//$h .= "This is a multi-part message in MIME format.\n";
		$h .= "--".$uid_m."\n";
		$h .= "Content-Type: multipart/alternative; boundary=\"".$uid_a."\"\n\n";
		//$h .= "--".$uid_a."\n";
		$h .= "Content-type:text/plain; charset=\"utf-8\"; format=\"flowed\"\n";
		$h .= "Content-Transfer-Encoding: 7bit\n\n";
		$h .= "--".$uid_a."\n";
		
		$h .= "Content-Type: multipart/related; boundary=\"".$uid_r."\"\n\n";
		$h .= "--".$uid_r."\n";
		
		$h .= 'Content-type:text/html; charset="utf-8"'."\n";
		$h .= "Content-Transfer-Encoding: 7bit\n\n";
		$h .= $messagehtml."\n\n";
		$h .= "--".$uid_r."\n";
		
		$databig = explode("base64,",$_SESSION['UI_DAILY_LOGO']);
		$h .='Content-Type: '.$image_mime."\n";
		$h .='Content-Transfer-Encoding: base64'."\n";
		$h .='Content-Disposition: inline'."\n";
		//$h .='Content-Location: dailyreport.png'."\n";
		$h .='Content-Id: <dailyreport.'.$image_ext.'>'."\n\n".chunk_split($databig[1], 68, "\n");
		
		$h .= "--".$uid_m."\n";
		
		$h .= "Content-Type: plain/text; name=\"".basename($fileatt)."\"\n";
		$h .= "Content-Transfer-Encoding: base64\n";
		$h .= "Content-Disposition: attachment; filename=\"".basename($fileatt)."\"\n\n";
		$h .= $content."\n";
		//$h .= "--".$uid_m."\n";
		//echo $h;exit;
		// send mail
		return mail( $to, $subject, strip_tags($messagehtml), $h );
	}
	
	
	$email_subject = "SMTP Report";
	//$flag = mail_file( $QMAIL_ADMIN, $email_subject, $str, $_SESSION['UI_NOTIFY_EMAIL'], $dir.$filename, $replyto="" );
	//if($flag=="1")
	//	echo "Sent successfully\n";
	//else 
	//	echo "Sent Fails\n";
	mail_file( "idan@pineapp.com", $email_subject, $str, $_SESSION['UI_NOTIFY_EMAIL'], $dir.$filename, $replyto="" );
	
	@system('rm '.$dir.$filename);
}
?>
