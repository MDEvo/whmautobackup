# WHM Auto Backup
A no-nonsense PHP-based utility for backing-up accounts under non-root resellers for cPanel/WHM.

![](https://github.com/liamdemafelix/whmautobackup/raw/master/screenshot.jpg)

# Features

* Supports more than one reseller account per instance
* Can run manually or through cron
* Optional Mailgun support for cron alerts
* Uses the [cPanel API 1 Library by mgufrone](https://github.com/mgufrone/cpanel-php)
* Destinations supported: homedir, ftp, scp (cPanel defaults)

# Known Bugs

* FTP and SCP options sometimes still go through homedir even though explicitly stated in the API to use said options (probably a bug with the deprecated API). Unless a similar feature is made available to the newer cPanel APIs, I'll work on adjusting the code.

Report bugs via the bug tracker, or if it's security-related, send an e-mail to [liam@rack63.com](mailto:liam@rack63.com).

# Usage

Deploy somewhere you can use a terminal. Rename `config.sample.php` to `config.php` and edit your options. Then, on your terminal, execute:

```
php run.php
```

You can also set `run.php` to run in specified intervals using a cron job.

# License

This script is under the [MIT Open Source License](https://opensource.org/licenses/MIT).
