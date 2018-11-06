#!/bin/sh

cd /var/www/workflow/backoffice
php cron_check_expire.php
