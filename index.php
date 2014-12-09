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
        in the query string (in this case it's simple the current page).
        Make sure that the callback URL is properly encoded. */
        window.location.href = "<?php echo PORTAL_URL; ?>/?callbackUrl=" + encodeURIComponent(document.URL);
      }
      
      function sign_out()
      {
        /* Signing out is also managed by the login portal. There is no
        callback in this case, just the logout=true parameter. */
        window.location.href = "<?php echo PORTAL_URL; ?>/?logout=true";
      }
    </script>
  </head>
  <body>
    <?php
      /* If login is successful, the portal will return the Keystone token as
      a query string parameter. */
      if (empty($_GET['token'])) 
      {
        /* If the token parameter is empty, then we are not signed in. 
        Display the "Sign In" button. */
        echo '<input type="button" onclick="sign_in()" value="Sign In" />';
      }
      else 
      {
        /* Otherwise, validate the token and display user info. */
        $token_id = $_GET['token'];
        $user_info = validate_token($token_id, get_service_token());
        show_user_info($token_id, $user_info);
        
        /* Also, add the "Sign Out" button. */
        echo '<input type="button" onclick="sign_out()" value="Sign Out"/>';
      }
    ?> 
  </body>
</html>