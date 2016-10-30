<?php

// Get the kernel
require "kernel.php";

// Arrays for results and exclusions (usernames)
$success = []; // Array for successful transfers
$failed = []; // Array for failed downloads
$exclude = []; // Excluded users

// Directory for transfers

// List Accounts
foreach ($servers as $server) {
    $name = $server['name'];
    unset($server['name']);
    $ftp_server = $server['host'];
    $server['host'] = "https://" . $server['host'] . ":2087";
    $transfer = __DIR__ . '/retrieved/' . $name . '/' . date("Y-m-d") . '/';
    if (!is_dir($transfer)) {
        mkdir($transfer, 0755, true);
    }
    $$name = new \Gufy\CpanelPhp\Cpanel($server);
    echo "=======================\nAccounts for {$name}\n=======================\n";
    $accounts = json_decode($$name->listaccts());
    foreach ($accounts->acct as $account) {
        if (!in_array($account, $exclude)) {
            $data = json_decode($$name->execute_action('2', 'Backups', 'listfullbackups', $account->user));
            $total = count($data->cpanelresult->data);
            echo "Processing backup file for {$account->user}...";
            $latest = "";
            $latest_time = 0;

            // Determine the latest backup
            foreach ($data->cpanelresult->data as $file) {
                if ($latest_time < $file->time) {
                    $latest_time = $file->time;
                    $latest = $file->file;
                }
            }

            // Create a temporary FTP account
            $ftp_user = $account->user . "_temp";
            $ftp_password = gen_pass(16);
            $ftp_result = json_decode($$name->execute_action('2', 'Ftp', 'addftp', $account->user, ['user' => $ftp_user, 'pass' => $ftp_password, 'quota' => 0, 'homedir' => '/']));

            // Get main domain
            $main = json_decode($$name->execute_action('3', 'DomainInfo', 'list_domains', $account->user));
            $main = $main->result->data->main_domain;

            // Set proper FTP user
            $ftp_user = $ftp_user . "@" . $main;

            // Sign in to FTP and retrieve the archive
            $ftp_conn = ftp_connect($ftp_server);
            $login = ftp_login($ftp_conn, $ftp_user, $ftp_password);
            ftp_pasv($ftp_conn, true);
            $get_file = $latest;
            $local = $transfer . $latest;
            $fp = fopen($local, "w");
            if (ftp_fget($ftp_conn, $fp, $file, FTP_BINARY, 0)) {
                fclose($fp);
                echo " OK!\n";
                echo "Filesize: " . number_format(filesize($local) / 1048576, 2) . " MB\n\n";
                $success[] = $account->user;
            } else {
                fclose($fp);
                echo " Failed!\n\n";
                $failed[] = $account->user;
            }
            ftp_close($ftp_conn);

            // Delete FTP account
            $$name->execute_action('2', 'Ftp', 'delftp', $account->user, ['user' => $ftp_user, 'destroy' => 0]);
        }
    }
    echo "\n";
}

echo "Done.\n";
$success_count = count($success);
$failed_count = count($failed);
echo "\nTotal: {$counter}\nSuccess: {$success_count}\nFailed: {$failed_count}\n\n";

if (!empty($mail)) {
    $date = date("F j, Y g:i A");
    $tz = date_default_timezone_get();
    $failed_acc = print_r($failed, true);
    $message = <<<MSG
Backup retrieval for {$date} {$tz} completed.<br /><br />
Success: {$success_count}<br />
Failed: {$failed_count}<br /><br />
Failed Accounts:<br /><br />
{$failed_acc}
MSG;
    $mg->sendMessage($mail['domain'], ['from' => $mail['from'], 'to' => $mail['to'], 'subject' => 'Retrieval Complete', 'text' => $message]);
}