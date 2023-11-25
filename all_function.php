<?php

class BankAccount
{
    private $balance;
    private $transactionHistory;
    private $count;

    public function __construct()
    {
        $this->balance = 0;
        $this->transactionHistory = [];
        $_SESSION['count'] = isset($_SESSION['count']) ? $_SESSION['count']++ : 0;
        $this->initSession();
    }

    private function calculateInitialCount()
    {
        // Implement your logic here
        // Example: return count($this->transactionHistory); 
        // echo $_SESSION['count'];
        if (!empty($this->transactionHistory)) {
            $_SESSION['count'] = count($this->transactionHistory);
        } else {
            $_SESSION['count'] = 0;
        }
    }

    private function initSession()
    {
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
            $this->calculateInitialCount(); // Calculate count based on transaction history

        }
    }

    public function deposit($amount)
    {
        $this->balance += $amount;
        $this->updateTransactionHistory('Deposit', $amount);
        $this->updateSession();
        // echo "Deposited $amount. ";
        $this->displayBalance();
    }

    public function withdraw($amount)
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->updateTransactionHistory('Withdraw', $amount);
            $this->updateSession();

            $msg = "Withdrawn $amount. ";
            $this->displayMessage($msg);
        } else {

            $msg = "Error: Your balance is insufficient. ";
            $this->displayMessage($msg);
        }
        $this->displayBalance();
    }

    public function checkBalance()
    {
        $this->displayBalance();
    }

    public function getTransactionHistory()
    {
        return array_reverse($this->transactionHistory[$this->getUser()]);
    }

    private function updateTransactionHistory($action, $amount)
    {
        $_SESSION['count'] = $_SESSION['count'] + 1; // Increment count for each transaction

        $balanceAfterTransaction = $this->balance;
        $timestamp = date('Y-m-d H:i:s'); // Get current date and time
        $this->transactionHistory[$this->getUser()][] = [
            'timestamp' => $timestamp,
            'action' => $action,
            'amount' => $amount,
            'balance_after_transaction' => $balanceAfterTransaction,
            'description' => $action . ' : ' . $amount,
        ];
    }
    public function transfer_to($amount, $transfer_to)
    {
        $_SESSION['count'] = $_SESSION['count'] + 1; // Increment count for each transaction

        $balanceAfterTransaction = $this->balance;
        $timestamp = date('Y-m-d H:i:s'); // Get current date and time

        $this->transactionHistory[$transfer_to][] = [
            'timestamp' => $timestamp,
            'action' => 'Transfer',
            'amount' => $amount,
            'balance_after_transaction' => $balanceAfterTransaction,
            'description' => 'Transfer From ' . $this->getUser() . ' : ' . $amount,
        ];

        $this->transactionHistory[$this->getUser()][] = [
            'timestamp' => $timestamp,
            'action' => 'Transfer',
            'amount' => $amount,
            'balance_after_transaction' => $balanceAfterTransaction,
            'description' => 'Transfer to ' . $transfer_to . ' : ' . $amount,
        ];
    }

    public function transfer($amount, $transfer_to)
    {
        if ($transfer_to == null) {
            $msg = 'Please, Select Transfer to';
            $this->displayMessage($msg);
            return;
        }
        if ($transfer_to == $this->getUser()) {
            $msg = 'The recipient cannot be the same as the sender.';
            $this->displayMessage($msg);
            return;
        }

        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            // $this->updateTransactionHistory('Transfer', $amount);
            $this->transfer_to($amount, $transfer_to);
            $this->updateSession();


            $msg = "Transfer $amount. ";
            $this->displayMessage($msg);
        } else {

            $msg = "Error: Your balance is insufficient. ";
            $this->displayMessage($msg);
        }
        $this->displayBalance();
    }

    public function getUser()
    {
        return $_SESSION['user']['username'];
    }

    public function getUserDisplay()
    {
        echo $_SESSION['user']['username'];
    }


    private function updateSession()
    {
        $_SESSION['bank_account']['balance'] = $this->balance;
        $_SESSION['bank_account']['transaction_history'] = $this->transactionHistory;
    }

    private function displayBalance()
    {
        echo "Current Balance: Rp. \n" . number_format($this->balance);
    }

    private function displayMessage($msg)
    {
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    public function logout()
    {
        session_start();
        unset($_SESSION["user"]);
        header("Location: login.php");
        exit();
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
    } elseif (isset($_POST['transfer'])) {
        $amount = (int)$_POST['amount'];
        $transfer_to = $_POST['transfer_to'];
        $account->transfer($amount, $transfer_to);
    } elseif (isset($_POST['logout'])) {
        $account->logout();
    }
}
