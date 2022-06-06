<?php
class management extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
	function import() {
	}
	function importsave() {
        $format1 = "Reference No,Guest Name,Email,Mobile,Channel Name,Booking Status,Booking Date,Checkin Date,Checkout Date,Room Type,Plan Type,Total Pax(adult(s)+child(s),Rooms,Total Nights,Total Amount,Tax Amount,Payment Status,Inclusion";
        //$col1 = array("Reference_No","Guest_Name","Email","Mobile","Channel_Name","Booking_Status","Booking_Date","Checkin_Date","Checkout_Date","Room_Type","Plan_Type","Persons","Rooms","Total_Nights","Total_Amount","Tax_Amount","Payment_Status","Inclusion");
        //$col2 = array("Reference_No","Guest_Name","Email","Mobile","Hotel_Name","Booking_Status","Booking_Date","Checkin_Date","Checkout_Date","Room_Type","Rooms","Total_Nights","Total_Amount","Paid_Amount","Comission_Amount","Hotelier_Amount");
        $col1 = "Reference_No,Guest_Name,Email,Mobile,Channel_Name,Booking_Status,Booking_Date,Checkin_Date,Checkout_Date,Room_Type,Plan_Type,Persons,Rooms,Total_Nights,Total_Amount,Tax_Amount,Payment_Status,Inclusion";
        $col2 = "Reference_No,Guest_Name,Email,Mobile,Hotel_Name,Booking_Status,Booking_Date,Checkin_Date,Checkout_Date,Room_Type,Rooms,Total_Nights,Total_Amount,Paid_Amount,Comission_Amount,Hotelier_Amount";
        if (@$_FILES['file']) {
            $handle = fopen($_FILES['file']['tmp_name'], "r");
            $headcol = "";
            if ($handle) {
                $header = 1;
                while (($line = fgets($handle)) !== false) {
                    if ($header==1) {
                        $line = str_replace('"','',$line);
                        $line = trim(preg_replace('/\s\s+/', ' ', $line));
                        if ($format1 == $line) {
                            $headcol = $col1;
                        } else {
                            $headcol = $col2;
                        }
                    } else {
                        $this->pr($headcol);
                        $line = str_replace('"', '', $line);
                        $cols = explode(",", $line);
                        $line = '"' . implode('","', $cols) . '"';
                        $sql = "INSERT INTO {$this->prefix}import ($headcol) VALUES ($line)";
                        $this->pr($sql);
                        exit;
                    }
                    ++$header;
                }
                fclose($handle);
            }
        } else {
            echo "no file ";
        }
        exit;
	}
    function reconcile() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-01");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $sql = "SELECT id_taxmaster AS id, tax_per AS name FROM {$this->prefix}taxmaster";
        $tax = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("tax", $tax);

        $sql = "SELECT * FROM {$this->prefix}reservation WHERE (depature_date >= '$sdate' AND depature_date <= '$edate') ORDER BY depature_date";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }	
	function report() {
	}
}
?>