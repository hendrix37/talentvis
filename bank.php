<?php

class BankAccount {
    private $balance;
    private $transactionHistory;

    public function __construct() {
        $this->balance = 0;
        $this->transactionHistory = [];
        $this->initSession();
    }

    private function initSession() {
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user'])) {
            header("Location: login.php");
            exit();
        }

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

    // ... (rest of the class remains unchanged)

}

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
    <!-- ... (head section remains unchanged) -->
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
        <!-- ... (table section remains unchanged) -->
    </table>

</body>
</html>
