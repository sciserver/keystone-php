<?php  
  function validate_token($user_token) 
  {
    $ch = curl_init(PORTAL_URL."/keystone/v3/tokens/".$user_token); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    
    try 
    {
      $result = curl_exec($ch);
      $info = curl_getinfo($ch);
      $http_code = (int) $info['http_code'];
      
      /* Successful token validation request should return 
      HTTP status codes: 200 OK or 203 Non-Authoritative Information.
      It's usually 200 OK. Not sure about the second one, but it's 
      in the specification. */
      if ($http_code !== 200 && $http_code !== 203) 
      {
        echo "$result<br/>";
        throw new Exception("Authentication failed.");
      }
      else 
      {
        /* The response is a JSON document. Decode it into an object. */
        return json_decode($result);
      }
    }
    finally 
    {
      curl_close($ch);
    }
  }
?>
