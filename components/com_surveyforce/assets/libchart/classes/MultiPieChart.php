<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class MultiPieChart extends PieChart {

	function MultiPieChart($width = 600, $height = 250, $sections)
	{
    	parent::PieChart($width, $height);
		
		$this->sections = $sections;
		
		$this->setMargin(3);
		$this->setLabelMarginLeft(20);
		$this->setLabelMarginRight(20);
		$this->setLabelMarginTop(40);
		$this->setLabelMarginBottom(20);
		$this->setLabelMarginCenter(20);

		$this->setPieRatio(0.50);

		$this->labelBoxWidth = 15;
		$this->labelBoxHeight = 15;
	}	
	
	function addPoint($i, $point)
	{
		if ( !isset($this->points[$i]) )
			$this->points[$i] = array();
		array_push($this->points[$i], $point);
	}
	
	function createImage2()
	{
		//parent::createImage();	
		$pieColors = array(
				array(2, 78, 0),
				array(148, 170, 36),
				array(233, 191, 49),
				array(240, 127, 41),
				
				array(243, 63, 34),
				array(190, 71, 47),
				array(135, 81, 60),
				array(128, 78, 162),
				
				array(121, 75, 255),
				array(142, 165, 250),
				array(162, 254, 239),
				array(137, 240, 166),
				
				array(104, 221, 71),
				array(98, 174, 35),
				array(93, 129, 1)
		);
	
		$this->pieColor = array();
		$this->pieShadowColor = array();
		$shadowFactor = 0.5;
	
		foreach($pieColors as $colorRGB)
		{
				list($red, $green, $blue) = $colorRGB;
	
				$color = new Color($red, $green, $blue);
				$shadowColor = new Color($red * $shadowFactor, $green * $shadowFactor, $blue * $shadowFactor);
	
				array_push($this->pieColor, $color);
				array_push($this->pieShadowColor, $shadowColor);
		}
	
		$this->axisColor1 = new Color(201, 201, 201);//C9C9C9
		$this->axisColor2 = new Color(158, 158, 158);//9E9E9E	
	
		$this->aquaColor1 = new Color(242, 242, 242);//F2F2F2
		$this->aquaColor2 = new Color(231, 231, 231);//E7E7E7
		$this->aquaColor3 = new Color(239, 239, 239);//EFEFEF
		$this->aquaColor4 = new Color(253, 253, 253);//FDFDFD
	
		// Legend box
	
		$this->outlinedBox($this->pieTLX, $this->pieTLY, $this->pieBRX, $this->pieBRY);
	
		// Aqua-like background
	
		$aquaColor = Array($this->aquaColor1, $this->aquaColor2, $this->aquaColor3, $this->aquaColor4);
	
		for($i = $this->pieTLY + 2; $i < $this->pieBRY - 1; $i++)
		{
				$color = $aquaColor[($i + 3) % 4];
				$this->primitive->line($this->pieTLX + 2, $i, $this->pieBRX - 2, $i, $color);
		}
	}
		
	function computeLabelMargin($n)
	{
		$graphWidth = $this->width - $this->margin * 2 - $this->labelMarginLeft - $this->labelMarginCenter - $this->labelMarginRight;
		
		$this->pieTLX = $this->margin + $this->labelMarginLeft;
		$this->pieTLY = intval(($n-1)*$this->height/$this->sections) + $this->margin + $this->labelMarginTop;
		$this->pieBRX = $this->pieTLX + $graphWidth * $this->pieRatio;
		$this->pieBRY = intval($n*$this->height/$this->sections) - $this->margin - $this->labelMarginBottom;
		
		$this->pieCenterX = $this->pieTLX + ($this->pieBRX - $this->pieTLX) / 2;
		$this->pieCenterY = $this->pieTLY + ($this->pieBRY - $this->pieTLY) / 2;
		
		$this->pieWidth = round(($this->pieBRX - $this->pieTLX) * 4 / 5);
		$this->pieHeight = round(($this->pieBRY - $this->pieTLY) * 3.7 / 5);
		$this->pieDepth = round($this->pieWidth * 0.05);
		
		$this->labelTLX = $this->pieBRX + $this->labelMarginCenter;
		$this->labelTLY = $this->pieTLY;
		$this->labelBRX = $this->pieTLX + $this->labelMarginCenter + $graphWidth;
		$this->labelBRY = $this->pieBRY;
		
		$this->pieLegendWidth = round($graphWidth * $this->pieRatio);
	}
	
	function printTitle($n)
	{
			if ($n == 1)
				$this->text->printCentered2($this->img, 4 + ($this->labelMarginTop + $this->margin) / 2, $this->textColor, $this->title, $this->text->fontCondensedBold);
			else
				$this->text->printCentered2($this->img, intval(($n-1)*$this->height/$this->sections) - 5 + ($this->labelMarginTop + $this->margin) / 2, $this->textColor, $this->title, $this->text->fontCondensedBold);
				
	}
	
	function renderSection($n) {
	
		//$this->computeBound();
		$this->computeLabelMargin($n);
		$this->computePercent();
		
		
		
		if ( $n == 1 ) {		
			list($llx, $lly, $lrx, $lry, $urx, $ury, $ulx, $uly) = @imageftbbox(8, 0, $this->text->fontCondensedBold,  $this->maintitle, array("linespacing" => 1));
			
			$textWidth = $lrx - $llx;
			if ($textWidth > $this->width) {
				$text = sf_SafeSplit($this->maintitle, intval($this->width/7));
				list($llx, $lly, $lrx, $lry, $urx, $ury, $ulx, $uly) = @imageftbbox(8, 0, $this->text->fontCondensedBold,  $text, array("linespacing" => 1));	
			}
			
			$textHeight = $lry - $ury;

			$this->pieTLY = $this->pieTLY + $textHeight;
			$this->pieBRY = $this->pieBRY + $textHeight;
			$this->height = $this->height + $textHeight;
			$this->labelMarginTop = $this->labelMarginTop + $textHeight;
			
			$this->computeLabelMargin($n);
		
			$this->createImage();
			$this->text->printText($this->img, 0, 0, $this->textColor, $this->maintitle, $this->text->fontCondensedBold, 0, $this->width);
		}
		else {
			$this->createImage2();
		}
		$bottomY = $this->printLabel();
		
		if ($n == 1 && $bottomY > $this->height) {		
			$this->height += ($bottomY - $this->height + $this->labelMarginBottom);
			
			$this->createImage();
			$this->text->printText($this->img, 0, 0, $this->textColor, $this->maintitle, $this->text->fontCondensedBold, 0, $this->width);
			
			$this->printLabel();
		}
			
		$this->printLogo();
		$this->printTitle($n);
		$this->printPie();
        
	}
	
	function render($fileName = null)
	{	
		for ($i = 1; $i <= $this->sections; $i++) {
			$this->point = $this->points[$i];
			$this->usr_answer = $this->usr_answers[$i];
			$this->setTitle($this->titles[$i]);
			$this->renderSection($i);
			
		}

		if(isset($fileName))
			imagepng($this->img, $fileName);
		else
			imagepng($this->img);
	}
}
?>