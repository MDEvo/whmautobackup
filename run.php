<?php

// Get the kernel
require "kernel.php";

$counter = 0;

// List accounts
foreach ($servers as $server) {
    $name = $server['name'];
    unset($server['name']);
    $server['host'] = "https://" . $server['host'] . ":2087";
    $$name = new \Gufy\CpanelPhp\Cpanel($server);
    echo "=======================\nAccounts for {$name}\n=======================\n";
    $accounts = json_decode($$name->listaccts());
    foreach ($accounts->acct as $account) {
        echo "Backing up {$account->user}...";
        $data = $$name->execute_action('1', 'Fileman', 'fullbackup', $account->user, $destination);
        echo " OK!\n";
        $counter++;
    }
    echo "\n";
}

// Done.
echo "Done! Moved {$counter} accounts to destination.\n\n";

if (!empty($mail)) {
    $mg->sendMessage($mail['domain'], ['from' => $mail['from'], 'to' => $mail['to'], 'subject' => 'Backup Complete', 'text' => 'Backup for ' . date("F j, Y g:i A") . ' completed. Backed up ' . $counter . ' accounts in total.']);
}