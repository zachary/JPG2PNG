<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Title</title>
</head>
<body>
<div>
<form action="" method="post" enctype="multipart/form-data">
 <input type="file" name="file">
 <input type="submit" value='download' name="submit" >
</form>
</div>
</body>
</html>
<?php
if(isset($_POST['submit'])){
  $imageFileType = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
  $fn = basename($_FILES['file']['name'],'.'.$imageFileType);
  if($imageFileType != "jpg" && $imageFileType != "JPG" ) {
    echo "Sorry, only JPG files are allowed.";
    exit;
  }
  $Image = new Imagick($_FILES['file']['tmp_name']);
  $BackgroundColors = array(
    'TopLeft' => array(1, 1),
    'TopRight' => array($Image->getimagewidth(), 1),
    'BottomLeft' => array(1, $Image->getimageheight()),
    'BottomRight' => array($Image->getimagewidth(), $Image->getimageheight())
  );

  foreach ($BackgroundColors as $Key => $BG) {
    $pixel = $Image->getImagePixelColor($BG[0], $BG[1]);
    $colors = $pixel->getColor();
    //$ExcludedColors[] = rgb2hex(array_values($colors));
    $Image->floodfillPaintImage('none', 9000, $pixel, $BG[0] - 1, $BG[1] - 1, false);
    //Comment the line above and uncomment the below line to achieve the effects of the second Vette
    $Image->transparentPaintImage($pixel, 0, 9000, false);
  }
  $fn.=".png";
  $Image->writeImage($fn);
  //ob_start();
  //$png=$Image->getImageBlob();
  //ob_end_clean();
  //echo "<img src='data:image/png;base64,".base64_encode($png)."' />";

  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: public");
  header("Content-Description: File Transfer");
  header("Content-Type: application/octet-stream");
  header('Content-Disposition: attachment; filename='.basename($fn));
  header("Content-Transfer-Encoding: binary");
  header('Content-Length: '.filesize($fn)."\n\n");
  ob_clean();
  flush();
  readfile($fn);
  exit;
}
