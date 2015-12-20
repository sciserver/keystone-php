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

Start a local HTTP server:

```
php.exe -S localhost:8000 -t C:\Projects\keystone-php
```
