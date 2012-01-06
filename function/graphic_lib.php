<?php


function thumb_create($src, $W, $H, $sam_dir){
	global $image_type;
  // 画像の幅と高さとタイプを取得
  $size = GetImageSize($src);
  //var_dump($size);
  switch ($size[2]) {
    case 1 : return false; break;
    case 2 : $im_in = ImageCreateFromJPEG($src); break;
    case 3 : $im_in = ImageCreateFromPNG($src);  break;
  }
  
  	if(!$im_in){
		$filename = substr($src, strrpos($src,"/")+1);
  		$filename = substr($filename, 0, strrpos($filename,"."));
  		if($image_type == 1){
  			copy($src, $sam_dir.$filename.".jpg");
  		}else{
  			copy($src, $sam_dir.$filename.".png");
  		}
		return false; break;
		//sub_msg("","","Jpegデコードエラー","そのJpegファイルは正しくありません。");
	}
  
  // リサイズ
  if ($size[0] > $W || $size[1] > $H) {
    $key_w = $W / $size[0];
    $key_h = $H / $size[1];
    ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
    $out_w = $size[0] * $keys;
    $out_h = $size[1] * $keys;
  } else {
    $out_w = $size[0];
    $out_h = $size[1];
  }
  // 出力画像（サムネイル）のイメージを作成
  // $im_out = ImageCreate($out_w, $out_h);
  $im_out = ImageCreateTrueColor($out_w, $out_h);
  // 元画像を縦横とも コピーします。
  ImageCopyResized($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  // サムネイル画像をブラウザに出力、保存
  $filename = substr($src, strrpos($src,"/")+1);
  $filename = substr($filename, 0, strrpos($filename,"."));
  if($image_type == 1){
  	ImageJPEG($im_out, $sam_dir.$filename.".jpg");
  }else{
  	ImagePNG($im_out, $sam_dir.$filename.".png");
  }
  // 作成したイメージを破棄
  ImageDestroy($im_in);
  ImageDestroy($im_out);
}





?>