<?php

// Get the kernel
require "kernel.php";

// Display warning
$warning = <<<EOF
=========================================================
WARNING
=========================================================
This option is in BETA. This is a workaround in case
your current setup does not transfer files to the
remote destination even though it is explicitly specified
in the configs. Do NOT rely on this method.

This may take a VERY long time to finish. Do NOT, in
any circumstance, interrupt the job.
=========================================================
\nPress ENTER to continue, or hit CTRL+C to quit...
EOF;
echo $warning;
$accept = trim(fgets(STDIN));

echo "\n";

$counter = 0;
$success = []; // Array for successful transfers
$failed = []; // Array for failed downloads
$exclude = []; // Excluded users
$transfer = 'retrieved/' . date("Y-m-d") . '/';
if (!is_dir($transfer)) {
    mkdir($transfer, 0755, true);
}

// List accounts
foreach ($servers as $server) {
    $name = $server['name'];
    unset($server['name']);
    $$name = new \Gufy\CpanelPhp\Cpanel($server);
    echo "=======================\nAccounts for {$name}\n=======================\n";
    $accounts = json_decode($$name->listaccts());
    foreach ($accounts->acct as $account) {
        if (!in_array($account, $exclude)) {
            $data = json_decode($$name->execute_action('2', 'Backups', 'listfullbackups', $account->user));
            $total = count($data->cpanelresult->data);
            echo "Processing backup file for {$account->user}...\n";
            $latest = "";
            $latest_time = 0;
            // Determine the latest backup
            foreach ($data->cpanelresult->data as $file) {
                if ($latest_time < $file->time) {
                    $latest_time = $file->time;
                    $latest = $file->file;
                }
            }
            echo "\tRetrieving {$latest}...";
            // Move
            $$name->execute_action('2', 'Fileman', 'fileop', $account->user, ['op' => 'chmod', 'metadata' => '0755', 'sourcefiles' => $latest]);
            $$name->execute_action('2', 'Fileman', 'fileop', $account->user, ['op' => 'move', 'sourcefiles' => $latest, 'destfiles' => 'public_html']);
            // Get main domain
            $main = json_decode($$name->execute_action('3', 'DomainInfo', 'list_domains', $account->user));
            $main = $main->result->data->main_domain;
            if (!@copy("http://" . $main . "/" . $latest, $transfer.$latest)) {
                $failed[] = $account->user;
                echo "Failed to create backup for {$account->user}!\n\n";
            } else {
                echo " OK!\n";
                echo "\tFilesize: " . number_format(filesize($transfer.$latest) / 1048576, 2) . " MB\n\n";
                $success[] = $account->user;
            }
            $counter++;
            // Move back to original
            $$name->execute_action('2', 'Fileman', 'fileop', $account->user, ['op' => 'chmod', 'metadata' => '0600', 'sourcefiles' => "public_html/" . $latest]);
            $$name->execute_action('2', 'Fileman', 'fileop', $account->user, ['op' => 'move', 'sourcefiles' => "public_html/" . $latest, 'destfiles' => '../']);
        }
    }
    echo "\n";
}

echo "Done.\n";
$success_count = count($success);
$failed_count = count($failed);
echo "\nTotal: {$counter}\nSuccess: {$success_count}\nFailed: {$failed_count}";

if (!empty($mail)) {
    $mg->sendMessage($mail['domain'], ['from' => $mail['from'], 'to' => $mail['to'], 'subject' => 'Retrieval Complete', 'text' => 'Backup retrieval for ' . date("F j, Y g:i A") . ' completed. Retrieved ' . $success_count . ' accounts with ' . $failed_count . ' failures, ' . $counter . ' in total.<br /><br />Failed: ' . print_r($failed)]);
}