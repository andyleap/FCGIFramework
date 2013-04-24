#!/bin/bash

spawn-fcgi -F 5 -s /tmp/phpfcgi.sock -M 0666 -- /usr/bin/php index.php
