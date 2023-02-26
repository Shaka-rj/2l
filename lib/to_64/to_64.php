<?php

class to{
    public $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
    
    public $alphabet_length = 64;
    
    public function encode($number){
        $text = '';
        while ($number > ($this->alphabet_length - 1)){
            $remainder = $number % $this->alphabet_length;
            $text = $this->alphabet[$remainder] . $text;
            $number = floor($number / ($this->alphabet_length));
        }
        $text = $this->alphabet[$number] . $text;
        return $text;
    }
    
    public function decode($string){
        $j = strlen($string)-1;
        $k = $j;
        for ($i = 0; $i <= $k; $i++){
            $character = $string[$j];
            $orni = strpos($this->alphabet, $character);
            $number += $orni * pow($this->alphabet_length, $i);
            $j --;
        }
        
        return $number;
    }
    
    public function random(int $length){
        $text = "";
        for ($i = 0; $i <= $length; $i++) $text .= $this->alphabet[mt_rand(0, $this->alphabet_length - 1)];
        return $text;
    }
}
