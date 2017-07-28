<?php


	
class invoicePDF
{
	public $customer;
	public $seller;
	public $comment;
	public $date_payment;
	public $date_sell;
	public $date_bill;
	public $place_sell;
	public $serial_number;
	public $is_original;
	public $margin_left_text;
	public $margin_right_text;
	public $person_bill;
	public $items;
	public $payment_mode;
	
	
	function Output()
	{
		$font_name = 'Arial';
		$pdf = new extensionFPDP();
		$pdf->SetAuthor("\"ADACH SOFT\" Arkadiusz Adach");
		$pdf->SetDisplayMode('real');
		$pdf->SetUTF8(true);
		
		$pdf->AddFont('Arial', '', 'arial.php');
		$pdf->AddFont('Arial', 'B', 'arialbd.php');
		$pdf->AddPage();
 		
		if( $this->margin_left_text!='' ){
			$pdf->SetFont('Arial', 'B', 20);
    		$pdf->SetTextColor(0xE0, 0xE0, 0xE0);
			$sw = $pdf->GetStringWidth( $this->margin_left_text );
	    	$pdf->RotatedText(6, 295-(295-$sw)/2, $this->margin_left_text, 90);	
		}
		
		if( $this->margin_right_text!='' ){
			$pdf->SetFont('Arial', 'B', 20);
    		$pdf->SetTextColor(0xE0, 0xE0, 0xE0);
			$sw = $pdf->GetStringWidth( $this->margin_right_text );
	    	$pdf->RotatedText(204, (295-$sw)/2, $this->margin_right_text, 270);	
		}
		
		$pdf->SetFillColor(0xD0,0xD0,0xD0);
	    $pdf->SetTextColor(0);
	    $pdf->SetDrawColor(0,0,0);
	    $pdf->SetLineWidth(.2);
		
		
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(130, 7, '', 0, 0, 'L' );
		$pdf->Cell(30, 7, 'Miejsce sprzedaży:', 1, 0, 'L', true );
		$pdf->SetFont($font_name, '', 8);
		$pdf->Cell(30, 7, $this->place_sell, 1, 0, 'L' );
		$pdf->Ln();
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Cell(130, 7, '', 0, 0, 'L' );
		$pdf->Cell(30, 7, 'Data sprzedaży:', 1, 0, 'L', true );
		$pdf->SetFont($font_name, '', 8);
		$pdf->Cell(30, 7, $this->date_sell, 1, 0, 'L' );
		$pdf->Ln();
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Cell(130, 7, '', 0, 0, 'L' );
		$pdf->Cell(30, 7, 'Data wystawienia:', 1, 0, 'L', true );
		$pdf->SetFont($font_name, '', 8);
		$pdf->Cell(30, 7, $this->date_bill, 1, 0, 'L' );
		$pdf->Ln();
		$pdf->Ln();
	
		//--->Sprzedawca, Nabywca
		
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Cell(90, 7, 'Sprzedawca:', 1, 0, 'C', true);
		$pdf->Cell(10, 7, '', 0, 0, 'C');
		$pdf->Cell(90, 7, 'Nabywca:', 1, 0, 'C', true);
		$pdf->Ln();
		
		
		$pdf->SetFont($font_name, '', 8);
		$pdf->Cell(90, 30, '', 1, 0, 'L');
		$pdf->Cell(10, 30, '', 0, 0, 'C');
		$pdf->Cell(90, 30, '', 1, 0, 'L');
		$pdf->Ln();
		//<---
	
		
		
		$pdf->SetFont($font_name, 'B', 16);
		$pdf->Cell(0, 12, "Rachunek $this->serial_number " . ( $this->is_original===true ? 'oryginał' : 'kopia' ), 0, 0, 'C');
		$pdf->Ln();
		
	
		
		
		$header = array('Lp.', 'Nazwa', 'Ilość', 'j.m.', 'Cena', 'Rabat[%]', 'Wartość');
		$pdf->SetFont($font_name, '', 8);
		$pdf->SetFillColor(0xD0,0xD0,0xD0);
		//$this->SetFillColor(224,235,255);
	    $pdf->SetTextColor(0);
	    $pdf->SetDrawColor(0,0,0);
	    $pdf->SetLineWidth(.2);
	    $pdf->SetFont('','B');
		
	    // Column widths
	    $w = array(7, 96, 15, 12, 20, 20, 20);
	    // Header
	    for($i=0;$i<count($header);$i++)
	        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
	    $pdf->Ln();
	    // Data
	    
	    $summary = 0;
	    $i=1;
	    $pdf->SetFont('','');
	    foreach($this->items as $row)
	    {
	    	$discount_amount = $row['count'] * $row['price'] * $row['discount'];
	    	$amount = str_replace('.', ',', sprintf('%.2f', round($row['count'] * $row['price'] - $discount_amount , 2)));
	        $pdf->Cell($w[0], 6, $i, 1, 0, 'L', false);														//Lp
	        $pdf->Cell($w[1], 6, $row['name'], 1, 0, 'L', false);											//Nazwa
	        $pdf->Cell($w[2], 6, str_replace('.', ',', sprintf('%.2f', $row['count'])), 1, 0, 'R', false);	//Ilosc
	        $pdf->Cell($w[3], 6, $row['unit'], 1, 0, 'L', false);											//j.m.
	        $pdf->Cell($w[4], 6, str_replace('.', ',', sprintf('%.2f', $row['price'])), 1, 0, 'R', false);	//Cena
	        $pdf->Cell($w[5], 6, ($row['discount']*100), 1, 0, 'R', false);										//Rabat[%]
	        $pdf->Cell($w[6], 6, $amount, 1, 0, 'R', false);												//Wartość
	        $pdf->Ln();
	        
	        $summary+=round($row['count'] * $row['price'] - $discount_amount, 2);
	        $i++;
	    }
	    // Closing line
	    $pdf->Cell(array_sum($w),0,'','T');
		
		
	    
	    
	    $cx = $pdf->GetX();
		$cy = $pdf->GetY()-2;
		
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Rect(10, $cy + 2, 190, 10);
		$pdf->Text(12, $cy + 5, "Razem do zapłaty: ");
		$pdf->Text(12, $cy + 8, "Słownie: ");
		
		$pdf->SetFont($font_name, '', 8);
		$pdf->Text(42, $cy + 5, str_replace('.', ',', sprintf('%.2f', $summary)) . " PLN");
		//$pdf->Text(42, $cy + 8, "siedemset PLN 0/100");
		$pdf->Text(42, $cy + 8, $this->convert_double2string($summary) );
		
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Text(11, $cy + 24, "Uwagi do dokumentu: $this->comment");
	    
	    $pdf->SetY($cy + 11);
		
		
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Cell(60, 10, 'Forma płatności: ' . $this->payment_mode, 0, 0, 'L' );
		$pdf->Cell(100, 10, 'W terminie: ' . $this->date_payment, 0, 0, 'L' );
		$pdf->Ln();
		
		$pdf->Line(10, $cy + 18, 200, $cy + 18);
		
		
		
	
		
		
		//--->Sprzedawca, Nabywca
		$pdf->Ln();
		$pdf->SetFont($font_name, 'B', 8);
		$pdf->Cell(70, 7, 'Wystawił(a):', 1, 0, 'C', true);
		$pdf->Cell(50, 7, '', 0, 0, 'C');
		$pdf->Cell(70, 7, 'Odebrał(a):', 1, 0, 'C', true);
		$pdf->Ln();
		
		
		$pdf->SetFont($font_name, '', 8);
		$pdf->Cell(70, 20, '', 1, 0, 'L');
		$pdf->Cell(50, 20, '', 0, 0, 'C');
		$pdf->Cell(70, 20, '', 1, 0, 'L');
		$pdf->Ln();
		
		$cy = $pdf->GetY();
		$pdf->SetFont($font_name, '', 8);
		$sw = $pdf->GetStringWidth( $this->person_bill );
		$pdf->Text(10 + (70 - $sw)/2, $cy - 15, $this->person_bill );
		
		//<---
		
		
		
		
		$pdf->SetFont($font_name, '', 8);
		$arr = explode("\n", $this->seller);
		
		$tmpx=11;
		$tmpy=50;
		foreach ($arr as $key => $value) {
			$pdf->Text($tmpx, $tmpy, $value);
			$tmpy+=3;
		}
		
		$arr = explode("\n", $this->customer);
		
		$tmpx=111;
		$tmpy=50;
		foreach ($arr as $key => $value) {
			$pdf->Text($tmpx, $tmpy, $value);
			$tmpy+=3;
		}
		
		//$pdf->Image("podpis1.png", 30, $cy - 20, 663/15, 635/15);
		
		
		/*
		$code='CODE 128';
		$pdf->SetFillColor(0x0,0x0,0x0);
		$pdf->Code128(10, 290, $code, 80, 5);
/*		$pdf->SetXY(10, 290+25);
		$pdf->Write(5,'A set: "'.$code.'"');*/
		
		//End document
		$pdf->Line(10, 295, 200, 295);
		
		$pdf->Output();
	}
	
	private function convert_double2string($d, $currency='PLN')
	{
            $d = $d * 100;
            $i = floor($d/100);
            $res = $d % 100;
            return $this->convert_int2string($i, true) . "" . $currency . " " . $res . "/100";
	}
	
	private function convert_int2string($i, $is_first)
	{
		$str = "";

		if ($is_first && $i == 0) return "zero ";
		
		if ($i < 1000000 && $i >= 1000)
		{
			$res = $i % 1000;
            $d = floor($i / 1000);
			$str = $this->convert_int2string($d, false) . ($d == 1 ? "tysiac" : ($d >= 2 && $d <= 4 ? "tysiace" : "tysiecy"));
			$str .= " " . $this->convert_int2string($res, false);
			return $str;
		}
		
		if ($i < 1000 && $i >= 100)
		{
			$res = $i % 100;
			$d = floor($i / 100);

			switch ($d)
			{
				case 1: $str = "sto"; break;
				case 2: $str = "dwieście"; break;
				case 3: $str = "trzysta"; break;
				case 4: $str = "czterysta"; break;
				case 5: $str = "pięćset"; break;
				case 6: $str = "sześćset"; break;
				case 7: $str = "siedemset"; break;
				case 8: $str = "osiemset"; break;
				case 9: $str = "dziewięćset"; break;
			}

			$str .= " " . $this->convert_int2string($res, false);
			return $str;
		}
		
		if ($i < 100 && $i >= 20)
		{
			$res = $i % 10;
			$d = floor($i / 10);

			switch ($d)
			{
				case 1: $str = "dziesięć"; break;
				case 2: $str = "dwadzieścia"; break;
				case 3: $str = "trzydzieści"; break;
				case 4: $str = "czterdzieści"; break;
				case 5: $str = "pięćdziesiąt"; break;
				case 6: $str = "sześćdziesiąt"; break;
				case 7: $str = "siedemdziesiąt"; break;
				case 8: $str = "osiemdziesiąt"; break;
				case 9: $str = "dziewięćdziesiąt"; break;
			}
			$str .= " " . $this->convert_int2string($res, false);
			return $str;
		}
		
		if ($i < 10 && $i >= 1)
		{
			$res = $i % 1;
			$d = floor($i / 1);

			switch ($d)
			{
				case 1: $str = "jeden"; break;
				case 2: $str = "dwa"; break;
				case 3: $str = "trzy"; break;
				case 4: $str = "cztery"; break;
				case 5: $str = "pięć"; break;
				case 6: $str = "sześć"; break;
				case 7: $str = "siedem"; break;
				case 8: $str = "osiem"; break;
				case 9: $str = "dziewięć"; break;
			}
                $str .= " " . $this->convert_int2string($res, false);
                return $str;
		}
		
		
		return $str;
	}
	
	/*
	private convert_double2string(double d, string currency)
	{
            d = Math.Round(d * 100);
            int i = (int)(d/100);
            int res = (int)(d % 100);
            return convert_int2string(i, true) + "" + currency + " " + res.ToString() + "/100";
        }

        private string convert_int2string(int i)
        {
            return convert_int2string(i, true);
        }

        
        }*/
	
}


?>