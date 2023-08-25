<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class GenerateImg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_img';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成小说图片';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $redis = Redis::connection()->client();
        $content = $redis->get('changdu_content');

        $arr = json_decode($content, true);
        $letter = [];

        $bg = imagecreate(750, 13402);
        imagecolorallocate($bg, 255, 255, 255);
        $black = imagecolorallocate($bg, 0, 0, 0);
        foreach ($arr as $item) {
            $letter = array_merge($letter, mb_str_split($item));
        }
        $content = '';
        foreach ($letter as $l) {
            $teststr = $content.$l;
            $testbox = imagettfbbox(41, 0, '/var/www/simsun.ttc', $teststr);
            if (($testbox[2] > 750) && ($content !== "")) {
                $content .= PHP_EOL;
            }
            $content .= $l;
        }
//        dd($content);

//        foreach ($content as $value) {
//            $this->textalign($bg, $value, 693, 1000, 25);
//        }

        imagettftext($bg, 41, 0, 105, 55, $black, "/var/www/simsun.ttc", $content);

        imagepng($bg, '/var/www/GD.png');

        imagedestroy($bg);

        dd('done');
    }

    function textalign($card, $str, $width, $x,$y,$fontsize,$fontfile,$color,$rowheight,$maxrow)
    {
        $tempstr = "";
        $row = 0;
        for ($i = 0; $i < mb_strlen($str, 'utf8'); $i++) {
            if($row >= $maxrow) {
                break;
            }
            //第一 获取下一个拼接好的宽度 如果下一个拼接好的已经大于width了，就在当前的换行 如果不大于width 就继续拼接
            $tempstr = $tempstr.mb_substr($str, $i, 1, 'utf-8');//当前的文字
            $nextstr = $tempstr.mb_substr($str, $i+1, 1, 'utf-8');//下一个字符串
            $nexttemp = imagettfbbox($fontsize, 0, $fontfile, $nextstr);//用来测量每个字的大小
            $nextsize = ($nexttemp[2]-$nexttemp[0]);
            if($nextsize > $width-10) {//大于整体宽度限制 直接换行 这一行写入画布
                $row = $row+1;
//                $card->imageText($tempstr,$fontsize,$color,$x,$y,$width,1);
                imagettftext($card, $fontsize, 0, $x, $y, $color, $fontfile, $tempstr);
                $y = $y+$fontsize+$rowheight;
                $tempstr = "";
            } else if($i+1 == mb_strlen($str, 'utf8') && $nextsize<$width-10){
//                $card->imageText($nextstr,$fontsize,$color,$x,$y,$width,1);
                imagettftext($card, $fontsize, 0, $x, $y, $color, $fontfile, $tempstr);
            }
        }
        return true;
    }
}
