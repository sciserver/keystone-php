<?php  
  function get_service_token()
  {
    /* We need to obtain a service (admin) token
    to validate any other user's tokens. Users cannot
    validate their own tokens. Service token is obtained
    just like any other token, by specifying the appropriate 
    credentials: user name, password and project name.*/
    $name = SERVICE_NAME;
    $password = SERVICE_PASSWORD;
    $project = SERVICE_PROJECT;
    
    /* This is the body of the POST request to the Keystone
    REST API to obtain a token. Insert service credentials here. */
    $post_data = <<<EOT
{
  "auth": {
    "identity": {
      "methods": [
        "password"
      ],
      "password": {
        "user": {
          "name": "$name",
          "password": "$password",
          "domain": {
            "id": "default"
          }
        }
      }
    },
    "scope": {
      "project": {
        "name": "$project",
        "domain": {
          "id": "default"
        }
      }
    }
  }
}       
EOT;
    /* Calling Keystone REST API. If the request is successful,
    the token id will be returned in the response header, so 
    we have to make sure that we get the header (CURLOPT_HEADER). */
    $ch = curl_init(KEYSTONE_URL."/v3/auth/tokens");        
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_HEADER, true);         
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    try 
    {
      $result = curl_exec($ch);
      $info = curl_getinfo($ch);
      $http_code = (int) $info['http_code'];
      
      /* Sucessful token request should return HTTP status code: 
      201 Created. */
      if ($http_code !== 201) 
      {
        echo "$result<br/>";
        throw new Exception("Could not obtain service token.");
      }
      else 
      {
        /* Retrieve token id from header by parsing header lines 
        one by one. We need the X-Subject-Token field. */
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $header_list = explode("\r\n", $header); 
        foreach ($header_list as &$str) 
        {
          explode(':', $str, 2);
          list($key, $value) = array_pad(explode(':', $str, 2), 2, null);
          if ($key === "X-Subject-Token") 
          {
            return trim($value);
          }
        }
      }
    }
    finally 
    {
      curl_close($ch);
    }
  }
  
  function validate_token_via_proxy($user_token) {
    $ch = curl_init(KEYSTONE_PROXY."/v2.0/tokens/".$user_token); 
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
      in the specifucation. */
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
  
  function validate_token($user_token, $service_token)
  {
    /* Token validation requires a service (admin) token. 
    See get_service_token() for details. */
    $ch = curl_init(KEYSTONE_URL."/v3/auth/tokens"); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, 
      array(
        "X-Auth-Token: $service_token",
        "X-Subject-Token: $user_token"
      ));
        
    try 
    {
      $result = curl_exec($ch);
      $info = curl_getinfo($ch);
      $http_code = (int) $info['http_code'];
      
      /* Successful token validation request should return 
      HTTP status codes: 200 OK or 203 Non-Authoritative Information.
      It's usually 200 OK. Not sure about the second one, but it's 
      in the specifucation. */
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