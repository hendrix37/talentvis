<?php

class BankAccount
{
    private $balance;
    private $transactionHistory;

    public function __construct()
    {
        $this->balance = 0;
        $this->transactionHistory = [];
        $this->initSession();
    }

    private function initSession()
    {
        if (!isset($_SESSION['bank_account'])) {
            $_SESSION['bank_account'] = [
                'balance' => $this->balance,
                'transaction_history' => $this->transactionHistory,
            ];
        } else {
            $this->balance = $_SESSION['bank_account']['balance'];
            $this->transactionHistory = $_SESSION['bank_account']['transaction_history'];
        }
    }

    public function deposit($amount)
    {
        $this->balance += $amount;
        $this->updateTransactionHistory('Deposit', $amount);
        $this->updateSession();
        echo "Deposited $amount. ";
        $this->displayBalance();
    }

    public function withdraw($amount)
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->updateTransactionHistory('Withdraw', $amount);
            $this->updateSession();
            echo "Withdrawn $amount. ";
        } else {
            echo "Error: Your balance is insufficient. ";
        }
        $this->displayBalance();
    }

    public function checkBalance()
    {
        $this->displayBalance();
    }

    public function getTransactionHistory()
    {
        return array_reverse($this->transactionHistory);
    }

    private function updateTransactionHistory($action, $amount)
    {
        $balanceAfterTransaction = $this->balance;
        $timestamp = date('Y-m-d H:i:s'); // Get current date and time
        $this->transactionHistory[] = [
            'timestamp' => $timestamp,
            'action' => $action,
            'amount' => $amount,
            'balance_after_transaction' => $balanceAfterTransaction,
        ];
    }

    private function updateSession()
    {
        $_SESSION['bank_account']['balance'] = $this->balance;
        $_SESSION['bank_account']['transaction_history'] = $this->transactionHistory;
    }

    private function displayBalance()
    {
        echo "Current Balance: $this->balance\n";
    }
}

session_start();

$account = new BankAccount();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deposit'])) {
        $amount = (int)$_POST['amount'];
        $account->deposit($amount);
    } elseif (isset($_POST['withdraw'])) {
        $amount = (int)$_POST['amount'];
        $account->withdraw($amount);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Account</title>

    <?php
    include('style.php');
    ?>

</head>

<body>

    <h1>Bank Account</h1>

    <div class="action-form">
        <form method="post">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" required>
            <button type="submit" name="deposit">Deposit</button>
            <button type="submit" name="withdraw">Withdraw</button>
        </form>
    </div>

    <div class="balance">
        <?php
        $account->checkBalance();
        ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Type</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($account->getTransactionHistory() as $transaction) {
                echo "<tr>";
                echo "<td>" . date('F j, Y, g:i a', strtotime($transaction['timestamp'])) . "</td>"; // Display formatted date
                echo "<td>{$transaction['action']}</td>";
                echo "<td>{$transaction['amount']}</td>";
                echo "<td>"; // Add logic to display credit (if applicable)
                echo ($transaction['action'] === 'Deposit') ? '-' : $transaction['amount'];
                echo "</td>";
                echo "<td>{$transaction['balance_after_transaction']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>

</html>