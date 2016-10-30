# WHM Auto Backup
A no-nonsense PHP-based utility for backing-up accounts under non-root resellers for cPanel/WHM.

![](https://github.com/liamdemafelix/whmautobackup/raw/master/screenshot.jpg)

[![Donate](https://github.com/liamdemafelix/whmautobackup/raw/master/donate.jpg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GUV2KKLLSGXES)

# Features

* Supports more than one reseller account per instance
* Can run manually or through cron
* Optional Mailgun support for cron alerts
* Uses the [cPanel API 1 Library by mgufrone](https://github.com/mgufrone/cpanel-php)
* Destinations supported: homedir, ftp, scp (cPanel defaults)
* Cleanup script for pruning backup files
* Retrieval scripts for downloading backups

# Known Bugs

* FTP and SCP options sometimes still go through homedir even though explicitly stated in the API to use said options (probably a bug with the deprecated API). Unless a similar feature is made available to the newer cPanel APIs, I'll work on adjusting the code. Until then, try manual retrieval.

Report bugs via the bug tracker, or if it's security-related, send an e-mail to [liam@rack63.com](mailto:liam@rack63.com).

# Usage

Deploy somewhere you can use a terminal. Rename `config.sample.php` to `config.php` and edit your options. Then, on your terminal, execute:

```
php run.php
```

You can also set `run.php` to run in specified intervals using a cron job.

# Cleanup

To prune all accounts of backup files, open your terminal and execute:

```
php cleanup.php
```

**NOTE:** This may take a long time depending on the number of backups. Do this regularly so it only has to process around 1 to 5 files per account.

# Manual Retrieval

This process creates a temporary FTP user and downloads the backup archive to the `retrieved` folder.

```
php retrieve.php
```

> If you have problems, disable Passive mode by commenting-out `ftp_pasv($ftp_conn, true);` in `retrieve.php`.

# Unsafe Manual Retrieval

This will download ALL the **latest** backups for each account to the `retrieved` folder.

> This is **unsafe**. It does not work on accounts whose primary domains are not pointed to the cPanel server it is in. It also messes up bandwidth quotas, as cPanel calculates each retrieval as a *download*, hence adding to the user's bandwidth usage. Lastly, it may expose users to having their backups publicly available (see bullet below) although only temporary.

```
php unsafe_retrieve.php
```

The process will take a long time depending on the number of accounts and the backup file sizes and your internet speed.

* The process moves a user's backup file to the public_html directory where it can be downloaded by using the account's main domain. It *may* pose as a security risk if an attacker were to guess the filename (it is date-based) or if there are no index files that will hide a directory listing. **Use this only as your last resort.**

# License

This script is under the [MIT Open Source License](https://opensource.org/licenses/MIT).
