<?php

header('Content-Type: application/json; charset=utf-8');

if(!empty($POST)){
  echo '';
  exit;
}

//アクセストークンの取得
$access_token  = "";
$refresh_token = trim(@file_get_contents('./auth/refresh_token.txt'));

if(empty($refresh_token)){
  $grantType = "authorization_code";
}else{
  //リフレッシュトークンがあればそれを使って認証（３０日が限度）
  $grantType = "refresh_token";
}

$api = new baseApiMethod();
$access_token = $api->getAccessToken($grantType);

if(empty($access_token)){
  echo '';
  exit;
}

//商品情報取得
$headers = array(
  'Authorization: Bearer ' . $access_token,
);
$request_options = array(
  'http' => array(
    'ignore_errors' => true,
    'method' => 'GET',
    'header' => implode("\r\n", $headers),
  ),
);
$context = stream_context_create($request_options);
$response_body = file_get_contents('https://api.thebase.in/1/items?limit=100&offset=0&visible=1', false, $context);
$response_array = json_decode($response_body);
if(isset($items['error'])){
  echo '';
  exit;
}

//加工
$response = array();
$items = $response_array->items;
foreach ($items as $key => $itemObject) {
  $response[$itemObject->item_id] = $itemObject->stock;
}

//値を返す
echo json_encode($response);
exit;

class baseApiMethod {

  public function getAccessToken($grantType){
    $auth = parse_ini_file("./auth/setting.ini");
    if($grantType == "authorization_code"){
      //リフレッシュトークンが無ければ、認証コードでアクセストークンを取得
      $code = (string)trim(file_get_contents("./auth/code.txt"));
      $params = array(
        'client_id'     => $auth['id'],
        'client_secret' => $auth['secret'],
        'code'          => $code,
        'grant_type'    => $grantType,
        'redirect_uri'  => $auth['uri']
      );
      $response_array = $this->postBaseapiToken($params);
      //リフレッシュトークンを保存
      if(!empty($response_array->refresh_token)){
        file_put_contents('./auth/refresh_token.txt',$response_array->refresh_token);
      }
    }else{
      //リフレッシュトークンが残っていれば、リフレッシュトークンを使ってアクセストークンを取得
      $refresh_token = (string)trim(file_get_contents('./auth/refresh_token.txt'));
      $params = array(
        'client_id'     => $auth['id'],
        'client_secret' => $auth['secret'],
        'refresh_token' => $refresh_token,
        'grant_type'    => $grantType,
        'redirect_uri'  => $auth['uri']
      );
      $response_array = $this->postBaseapiToken($params);
    }
    return isset($response_array->access_token) ? $response_array->access_token : null;
  }

  private function postBaseapiToken($params){
    $headers = array('Content-Type: application/x-www-form-urlencoded');
    $request_options = array(
      'http' => array(
        'ignore_errors' => true,
        'method'  => 'POST',
        'content' => http_build_query($params),
        'header'  => implode("\r\n", $headers),
      ),
    );
    $context = stream_context_create($request_options);
    $response_body = file_get_contents('https://api.thebase.in/1/oauth/token',false,$context);
    return json_decode($response_body);
  }
}

?>
