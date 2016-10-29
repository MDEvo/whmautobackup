<?php

// Get the kernel
require "kernel.php";

$counter = 0;
$files = 0;

// List accounts
foreach ($servers as $server) {
    $name = $server['name'];
    unset($server['name']);
    $$name = new \Gufy\CpanelPhp\Cpanel($server);
    echo "=======================\nAccounts for {$name}\n=======================\n";
    $accounts = json_decode($$name->listaccts());
    foreach ($accounts->acct as $account) {
        $data = json_decode($$name->execute_action('2', 'Backups', 'listfullbackups', $account->user));
        $total = count($data->cpanelresult->data);
        echo "Processing {$total} backup files for {$account->user}...\n";
        foreach ($data->cpanelresult->data as $file) {
            // Delete
            $$name->execute_action('2', 'Fileman', 'fileop', $account->user, ['op' => 'unlink', 'sourcefiles' => $file->file]);
            echo "\tFile {$file->file} deleted.\n";
            $files++;
        }
        echo "Cleanup for account {$account->user} done.\n\n";
        $counter++;
    }
    echo "\n";
}

echo "Done. Processed {$counter} accounts and {$files} backup files.\n\n";

if (!empty($mail)) {
    $mg->sendMessage($mail['domain'], ['from' => $mail['from'], 'to' => $mail['to'], 'subject' => 'Cleanup Complete', 'text' => 'Backup cleanup for ' . date("F j, Y g:i A") . ' completed. Backed up ' . $counter . ' accounts and ' . $files . ' files in total.']);
}