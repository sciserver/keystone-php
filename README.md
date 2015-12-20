# keystone-php

You need the following PHP extensions to run this example:
```
php_curl
php_pdo_mysql
```

Create a MySQL database:

```
mysql> CREATE DATABASE keystone;
mysql> USE keystone;
mysql> SOURCE C:/Projects/keystone-php/usersdb.sql;
```

The database is needed to test linking Keystone user accounts to local user accounts. The `usersdb.sql` script will create three local users in the database:
```
kate
mary
john
```
You can enter one of these user names in the "Linked user" field.


Start a local HTTP server:

```
php.exe -S localhost:8000 -t C:\Projects\keystone-php
```
