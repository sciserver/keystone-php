<?php
  function get_service_token()
  {
    $name = SERVICE_NAME;
    $password = SERVICE_PASSWORD;
    $project = SERVICE_PROJECT;
    
    {   
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
        if ($http_code !== 201) 
        {
          echo "$result<br/>";
          throw new Exception("Could not obtain service token.");
        }
        else 
        {
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
  }
  
  function validate_token($user_token, $service_token)
  {
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
      if ($http_code !== 200 && $http_code !== 203) 
      {
        echo "$result<br/>";
        throw new Exception("Authentication failed.");
      }
      else 
      {
        return json_decode($result);
      }
    }
    finally 
    {
      curl_close($ch);
    }
  }
?>