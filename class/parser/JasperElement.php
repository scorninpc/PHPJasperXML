<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JasperElements
 *
 * @author adrian
 */
class Jasper_reportElement extends JasperObject {
   
    function getInt($value,$def=0)
    {
       if (!isset($value))
            return $def;
       return (integer)$value;
        
    }
   function getString($value,$def='')
    {
       if (!isset($value))
            return $def;
       return (string)$value;
        
    }


    function getBool($value,$def=false)
    {
        if (!isset($value))
            return$def;
        if (strtolower($value) == 'false') return false;
        if (strtolower($value) == 'no') return false;
        if (empty($value)) return false;
        if ($value)
            return true;
    }
    public $_type;
        protected $_height = 0;
  
    protected $_x = 0;
    protected $_y = 0;
    protected $_width = 0;
        protected $_fill = 0;
         protected $_textcolor = array("r" => 0, "g" => 0, "b" => 0);
        protected $_fillcolor = array("r" => 255, "g" => 255, "b" => 255);
        protected $_stretchoverflow = "true";
        protected $_printoverflow = "false";  
      protected $_printWhenExpression = '';
     function parse($data)
    {
         
        $this->height =  $this->getInt($data->reportElement["height"],0);
         $this->x =  $this->getInt($data->reportElement["x"]);
         $this->y =  $this->getInt($data->reportElement["y"]);
         $this->width =  $this->getInt($data->reportElement["width"]);
        /** allow forground color "forecolor" */
        $this->printWhenExpression = $this->getString($data->reportElement->printWhenExpression);
       //reportElement
       if (isset($data->reportElement["forecolor"])) {
            $this->textcolor = array("r" => hexdec(substr($data->reportElement["forecolor"], 1, 2)), "g" => hexdec(substr($data->reportElement["forecolor"], 3, 2)), "b" => hexdec(substr($data->reportElement["forecolor"], 5, 2)));
        }
        if (isset($data->reportElement["backcolor"])) {
            $this->fillcolor = array("r" => hexdec(substr($data->reportElement["backcolor"], 1, 2)), "g" => hexdec(substr($data->reportElement["backcolor"], 3, 2)), "b" => hexdec(substr($data->reportElement["backcolor"], 5, 2)));
        }
        if ($data->reportElement["mode"] == "Opaque") {
            $this->fill = 1;
        }
        if (isset($data["isStretchWithOverflow"]) && $data["isStretchWithOverflow"] == "true") {
            $this->stretchoverflow = "true";
        }
        if (isset($data->reportElement["isPrintWhenDetailOverflows"]) && $data->reportElement["isPrintWhenDetailOverflows"] == "true") {
            $this->printoverflow = "true";
            $this->stretchoverflow = "false";
        }
        
    }
    
    function layout()
    {
      // if visible
      
    }
}

class Jasper_pen extends JasperObject{
    protected $_lineColor = '';
    protected $_lineStyle = '';
    protected $_lineWidth = -1;
}
class Jasper_box extends Jasper_reportElement
{
      //box
protected $_pen;
protected $_topPen;
protected $_leftPen;
protected $_bottomPen;
protected $_rightPen;

protected $_padding = 0;
protected $_topPadding = 0;
protected $_leftPadding = 0;
protected $_rightPadding = 0;
protected $_bottomPadding = 0;

    function layout()
    { 
      parent::layout();
    } 
   function parse($data)
    {
     parent::parse($data);
     $this->loadValues($data);
     
     $data = $data->box;
         if ($data->getName() == 'box')
         {
// box dom   there is a lot more needed for this dom
        foreach (array('pen', 'topPen', 'leftPen', 'bottomPen', 'rightPen') as $pen) {
           $p = $data->$pen;
           $penname = $p->getName();
            if (!empty($penname)) {
                $penname = '_' . $penname;
                $this->$penname = new Jasper_pen($this);
                $this->$penname->loadValues($p);
            }
        }
    }}
          
}

abstract class Jasper_textElement extends Jasper_box
{

  //textElement       
        protected $_verticalAlignment ;
        protected $_textAlignment; 
          protected $_rotation = "";
               protected $_fontsize = 10;
        protected $_font = "helvetica";
       protected $_fontstyle = '';
   abstract function getText();
   function getTextSize($text)
   {
     
   }
   function parse($data)
    {
     parent::parse($data)       ;

        /// textElement dom 
        if (isset($data->textElement["textAlignment"])) {
            $this->textAlignment = substr($data->textElement["textAlignment"], 0, 1);
        }
        if (isset($data->textElement["verticalAlignment"])) {
            $this->verticalAlignment = substr($data->textElement["verticalAlignment"], 0, 1);
        }
        if (isset($data->textElement["rotation"])) {
            $this->rotation = $data->textElement["rotation"];
        }
        if (isset($data->textElement->font["fontName"])) {
            $this->font = $data->textElement->font["fontName"];
        }

        if (isset($data->textElement->font["pdfFontName"])) {
            $this->font = $data->textElement->font["pdfFontName"];
        }
        if (isset($data->textElement->font["size"])) {
            $this->fontsize = $data->textElement->font["size"];
        }
        if (isset($data->textElement->font["isBold"]) && $data->textElement->font["isBold"] == "true") {
            $fontstyle = $fontstyle . "B";
        }
        if (isset($data->textElement->font["isItalic"]) && $data->textElement->font["isItalic"] == "true") {
            $fontstyle = $fontstyle . "I";
        }
        if (isset($data->textElement->font["isUnderline"]) && $data->textElement->font["isUnderline"] == "true") {
            $fontstyle = $fontstyle . "U";
        }
        $this->fontstyle = $fontstyle;
        if (isset($data->reportElement["key"])) {
            $this->height = $fontsize * $this->adjust;
    }
    }        
}
