<?php
/**
 * MSCaptcha by DaVchezt
 **/
if (!defined("IN_ESOTALK")) exit;

Class MSCaptchaController extends ETController {
	protected $width = 110;
	protected $height = 45;
	protected $font = '/font/League_Gothic.ttf';
	protected $font_size = 20;
	protected $font_colors = array(0, 50, 100, 150, 200);
	
	public function action_index()
	{
		// ttf font
		$this->font = dirname(__FILE__) . $this->font;
		// Create the image
		$src = imagecreatetruecolor($this->width, $this->height);
		// Create white color
		$white = imagecolorallocate($src, 255, 255, 255);
		imagefill($src, 0, 0, $white);
		//Prevent the spider use the same number as captcha to register automaticly.
		$cc = ET::$session->get('inputmscaptha');
        $errnum = ET::$session->get('capthaerrnum');
        if(!$errnum) $errnum = 0;
		while (true){
			// Get the code
            $x = array_rand(array("+","*"),1);
            if($x == 0){
				// Genrate random number
				$a = rand(10, 50);
				$b = rand(10, 50);
				$c = $a + $b;
            }else{
                $a = rand(5, 10);
				$b = rand(5, 10);
                $c = $a * $b;
            }
            //增大恶意刷注册撞中的难度
            if($errnum > 5){
                $x = 1;
                $a = rand(5, 20);
				$b = rand(5, 20);
                $c = $a * $b;
            }
            if($errnum > 15){
                $x = 1;
                $a = rand(50, 99);
				$b = rand(50, 99);
                $c = $a * $b;
            }
			if ($c != $cc) break;
		}
		// Genrate session of code
		ET::$session->store('mscaptcha', $c);
		
        $arr = array($a, $x==0?'+':' x ', $b, '=', '?');
		// Create Image from code
		for($i = 0; $i < count($arr); $i++) {
			$color = imagecolorallocatealpha(
				$src,
				$this->font_colors[rand(0, count($this->font_colors) - 1)],
				$this->font_colors[rand(0, count($this->font_colors) - 1)],
				$this->font_colors[rand(0, count($this->font_colors) - 1)],
				rand(0, 50)
			);
			imagettftext(
				$src,
				$this->font_size,
				0,
				($this->font_size * ($i + 1)) - 10,
				(($this->height * 2) / 3) + 3,
				$color,
				$this->font,
				$arr[$i]
			);
		}
		header("Content-type: image/png");
		imagepng($src);
		imagedestroy($src);
	}
	
}