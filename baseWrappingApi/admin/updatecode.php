<?php
if(isset($_POST['updatecode'])) {
  if($_POST['hidRand']){
    $auth = parse_ini_file("../auth/setting.ini");
    header('Location: '.trim($auth['coderefreshurl']));
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex,nofollow">
  <title>KSR BASE API UPDATECODE</title>
</head>
<body>
  <form action="updatecode.php" method="post">
    <input type="hidden" name="hidRand" value="<?php echo rand(); ?>" >
    <input type="submit" value="CODE REFRESH" name="updatecode">
  </form>
</body>
</html>
