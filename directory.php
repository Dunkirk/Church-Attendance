<?php

require('fpdf.php');

class PDF extends FPDF {

	// Cell (width, height, text, border, line return, alignment, fill, link)

	function Header() {
		//$this->Image ('wop.jpg', 10, 8, 33);
		$widths = array (20, 30, 70, 20, 50);
		$this->SetFont ('Arial', 'B', 16);
		$this->Cell (80);
		$this->Cell (30, 10, 'The World of Pentecost Directory', 0, 0, 'C');
		$this->Ln (10);
		$height = 4;
		$this->SetFillColor(0, 0, 0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128, 128, 128);
		$this->SetLineWidth(.3);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell ($widths[0], $height, "Last Name", 1, 0, 'C', 1);
		$this->Cell ($widths[1], $height, "First Name", 1, 0, 'C', 1);
		$this->Cell ($widths[2], $height, "Address", 1, 0, 'C', 1);
		$this->Cell ($widths[3], $height, "Phone", 1, 0, 'C', 1);
		$this->Cell ($widths[4], $height, "Others", 1, 0, 'C', 1);
		$this->Ln();
	}

	function Footer() {
		//Position at 1.5 cm from bottom
		$this->SetY (-10);
		$this->SetFont ('Arial', 'I', 8);
		$this->Cell (0, 8, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
	}
}

//Instanciation of inherited class
$pdf=new PDF();
$pdf->SetAutoPageBreak(true, 10);
$pdf->AliasNbPages();
$pdf->AddPage();

?>

<html>

<head>
<title>WoP Directory</title>
</head>

<body>

<div style='text-align: center'>

<?php include ('header.html'); ?>

<h2>Directory</h2>

<table align=center border=1>
<tr><th>Last Name<th>First Name
<th>Address<th>Phone<th>Others</tr>

<?php

	// Cell (width, height, text, border, line return, alignment, fill, link)
	// Set $widths above as well...
	$widths = array (20, 30, 70, 20, 50);
	$height = 4;
	$pdf->SetTextColor(0);
	$pdf->SetFont('Arial', '', 7);

	include ('connect.php');

	$sql = "SELECT r.id as residence_id, p.id as person_id, r.address1, " .
		"r.address2, r.city, r.state, p.member_type, " .
		"r.zip, r.phone, p.first_name, p.last_name FROM " .
		"residences r, people p WHERE p.residence = r.id AND " .
		"p.member_type = 'Head' AND p.first_name <> 'Placeholder' " .
	   	"ORDER BY p.last_name";
		
	$residence_result = pg_exec ($db, $sql);
	
	$fill = true;
	while ($row = pg_fetch_object ($residence_result)) {

		if ($fill) {
			$pdf->SetFillColor(240, 240, 240);
			echo "<tr bgcolor='#CCCCCC'>";
		} else {
			$pdf->SetFillColor(255, 255, 255);
			echo "<tr bgcolor='white'>";
		}
		$fill = !$fill;
			
		echo "<td>" . $row->last_name;

		$first_names = $row->first_name;
		$first_names_html = "<a href='people.php?person_id=" . $row->person_id .
			"&choose_person=Choose'>" . $row->first_name . "</a>";

		$sql = "SELECT id, first_name FROM people WHERE head = " .
			$row->person_id . " AND member_type = 'Spouse'";
		$spouse_result = pg_exec ($db, $sql);
		$spouse = pg_fetch_object ($spouse_result);
		if ($spouse->first_name <> "") {
#echo "<p>$sql</p>\n";
			$first_names = $first_names . " & " . $spouse->first_name;
			$first_names_html = $first_names_html . " & <a href='people.php?person_id=" .
				$spouse->id . "&choose_person=Choose'>" . $spouse->first_name . "</a>";
		}
		echo "<td>$first_names_html";

		$address = $row->address1;
		if ($row->address2 <> "") {
			$address .= ", " . $row->address2;
		}
		$address .= " -- " . $row->city . ", " . $row->state . " " . $row->zip;
		echo "<td><a href='residences.php?residence_id=" . $row->residence_id .
			"&choose_residence=Choose'>" . $address . "</a>";

		if (strlen ($row->phone) > 7) {
			$phone = substr ($row->phone, 0, 3) . "-" . substr ($row->phone, 3, 3) .
			   "-" . substr ($row->phone, 6, 4);
		} elseif (strlen ($row->phone) > 0) {
			$phone = substr ($row->phone, 0, 3) . "-" . substr ($row->phone, 3, 4); 
		} else {
			$phone = "(unlisted)";
		}
		echo "<td>$phone";
		
		$sql = "SELECT id, first_name, last_name FROM people WHERE head = " .
			$row->person_id . " AND member_type = 'Dependent' ORDER BY birthdate ASC";
		$others_result = pg_exec ($db, $sql);
		// Flag to check if we've already done an "other" so that we get 
		// the commas right
		if (pg_num_rows ($others_result) > 0) {
			$flag = false;
			while ($person = pg_fetch_object ($others_result)) {
				if ($flag == false) {
					if ($person->last_name == $row->last_name) {
						$others_html = "<a href='people.php?person_id=" . $person->id .
							"&choose_person=Choose'>" . $person->first_name . "</a>";
						$others = $person->first_name;
					} else {
						$others_html = "<a href='people.php?person_id=" . $person->id .
							"&choose_person=Choose'>" . $person->first_name . " " .
							$person->last_name . "</a>";
						$others = $person->first_name . " " . $person->last_name;
					}
					$flag = true;
				} else {
					if ($person->last_name == $row->last_name) {
						$others_html = $others_html . ", <a href='people.php?person_id=" .
						   $person->id . "&choose_person=Choose'>" . $person->first_name . "</a>";
						$others = $others . ", " . $person->first_name;
					} else {
						$others_html = $others_html . ", <a href='people.php?person_id=" .
						   $person->id . "&choose_person=Choose'>" . $person->first_name . " " .
						   $person->last_name . "</a>";
						$others = $others . ", " . $person->first_name . " " . $person->last_name;
					}
				}
			}
		} else {
			$others_html = "&nbsp;";
			$others = "";
		}
		echo "<td>$others_html";

		echo "</tr>\n";

		/*
		if ($row->city <> "") {
			$city = $row->city;
			$city_html = $city;
		} else {
			$city = "";
			$city_html = "&nbsp;";
		}
		echo "<tr><td>$city_html";

		if ($row->state <> "") {
			$state = $row->state;
			$state_html = $state;
		} else {
			$state = "";
			$state_html = "&nbsp;";
		}
		echo "<td>$state_html";

		if ($row->zip <> "") {
			$zip = $row->zip;
			$zip_html = $zip;
		} else {
			$zip = "";
			$zip_html = "&nbsp;";
		}
		echo "<td>$zip_html";

		echo "</tr>\n";
*/

		$pdf->Cell ($widths[0], $height, $row->last_name, 1, 0, 'L', 1);
		$pdf->Cell ($widths[1], $height, $first_names, 1, 0, 'L', 1);
		$pdf->Cell ($widths[2], $height, $address, 1, 0, 'L', 1);
		$pdf->Cell ($widths[3], $height, $phone, 1, 0, 'R', 1);
		$pdf->Cell ($widths[4], $height, $others, 1, 0, 'L', 1);
		$pdf->Ln();
		/*
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Ln (4);
		$pdf->Cell ($widths[0] + $widths[1]);
		$pdf->Cell ($widths[2], $height, $city, 1, 0, 'L', 1);
		$pdf->Cell ($widths[3], $height, $state, 1, 0, 'L', 1);
		$pdf->Cell ($widths[4], $height, $zip, 1, 0, 'L', 1);
		*/

	}
	
	$pdf->Output("directory.pdf");

?>

</table>

<p>You can download a <a href="directory.pdf">PDF-formatted version</a> of this directory.</p>

</div>

</body>

</html>	
