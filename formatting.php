<?php
  function show_user_info($token_id, $user_info) 
  {
    /* Prints various properties of the user_info
    object, obtained from the token validation request. 
    We also might want to print token_id, which is not
    included in the user_info. */
    $user_name = $user_info->token->user->name;
    $user_id = $user_info->token->user->id;
    $roles_array = $user_info->token->roles;
    $roles = "";
    foreach ($roles_array as &$role) 
    {
      $roles = $roles.$role->name."</br>";
    }
    $domain = $user_info->token->user->domain->name;
    $project = $user_info->token->project->name;
    $issued_at = $user_info->token->issued_at;
    $expires_at = $user_info->token->expires_at;
        
    echo <<<EOT
<table border="1">
  <tr><td>Token ID: </td><td>$token_id</td></tr>
  <tr><td>Issued at: </td><td>$issued_at</td></tr>
  <tr><td>Expires at: </td><td>$expires_at</td></tr>
  <tr><td>User ID: </td><td>$user_id</td></tr>
  <tr><td>User Name: </td><td>$user_name</td></tr>
  <tr><td>Project: </td><td>$project</td></tr>
  <tr><td>Domain: </td><td>$domain</td></tr>
  <tr><td>Roles: </td><td>$roles</td></tr>
</table><br/>
EOT;
  }
?>