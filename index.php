<html>
  <head>
    <title>Home Page</title>
    <?php require 'constants.php'; ?>
    <?php require 'keystone.php'; ?>
    <?php require 'formatting.php'; ?>
    
    <script type="text/javascript">
      function sign_in()
      {
        /* Redirect to the login portal and specify the callback page URL
        in the query string (in this case it's simply the current page).
        Make sure that the callback URL is properly encoded. Remove any existing
        tokens from the request parameters.*/
        window.location.href = "<?php echo PORTAL_URL; ?>/?callbackUrl=" 
          + encodeURIComponent(document.URL.replace(/token=[a-z0-9]*/g,""));
      }
      
      function sign_out()
      {
        /* Signing out is also managed by the login portal. There is no
        callback in this case, just the logout=true parameter. Make sure
        we erase the cookie before redirecting to the portal.*/
        document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        window.location.href = "<?php echo PORTAL_URL; ?>/?logout=true";
      }
    </script>
  </head>
  <body>
    <?php
      /* If login is successful, the portal will return the Keystone token as
      a query string parameter. We will also check for a previously stored cookie. */
      try {
        if (empty($_GET['token']) && empty($_COOKIE['token'])) 
        {
          /* If no token has been found, just throw an exception. 
          We will take care of it later. */
          throw new Exception('You are not signed in.');
        }
        else 
        {
          $token_id = "";
          if (!empty($_GET['token'])) // Request parameter has precedence over cookies.
          {
            $token_id = $_GET['token'];
          }      
          else // if (!empty($_COOKIE['token']))
          {
            $token_id = $_COOKIE['token'];
          }
          
          /* Try to validate the token. */
          try 
          {
            $user_info = validate_token($token_id, get_service_token());
          }
          catch (Exception $e) 
          {
            /* If there was a cookie, erase it. Otherwise, we'll keep getting this
            error over and over again with every page reload! */
            setcookie("token", null, 0); 
            
            /* Rethrow the exception. Now it will only show the error message once.
            Reload the page to start again. */
            throw $e;
          }
          
          /* If we got this far, then we have a valid token. 
          Store it in a cookie and display user's info. */
          setcookie("token", $token_id, time() + 60*60*24); // Cookie expires in 1 day.
          show_user_info($token_id, $user_info);
          
          /* Also, add the "Sign Out" button. */
          echo '<input type="button" onclick="sign_out()" value="Sign Out"/>';
        }
      }
      catch (Exception $e) 
      {
        /* If something went wrong, display the error message and the "Sign In" button. */
        echo '<p>'.$e->getMessage().'</p>';
        echo '<input type="button" onclick="sign_in()" value="Sign In" />';
      }
    ?> 
  </body>
</html>