<?php
require_once "vendor/autoload.php";
require_once "credentials.php";

$db_name = 'jefbli_bett2023';
  
$db = new mysqli($servername, $username, $password, $db_name);
  
if($db->connect_error){
    die("Unable to connect database: " . $db->connect_error);
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
  
$spreadsheet = new Spreadsheet();
$Excel_writer = new Xlsx($spreadsheet);
  
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();
  
$activeSheet->setCellValue('A1', 'Ov-nummer');
$activeSheet->setCellValue('B1', 'Aangemeld');
$activeSheet->setCellValue('C1', 'Afgemeld');
  
$query = $db->query("SELECT * FROM presence");
  
if($query->num_rows > 0) {
    $i = 2;
    while($row = $query->fetch_assoc()) {
        $activeSheet->setCellValue('A'.$i , $row['ID']);
        $activeSheet->setCellValue('B'.$i , $row['DateTimeIn']);
        $activeSheet->setCellValue('C'.$i , $row['DateTimeOut']);
        $i++;
    }
}
  
$filename = 'presence.xlsx';
  
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
?>