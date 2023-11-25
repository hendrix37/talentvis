<?php
include('all_function.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Account</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <header>
        <nav>
            <ul>
                <li>

                    <form method="post">
                        <button type="submit" name="logout">
                            Logout
                            <b>
                                <?php
                                    $account->getUserDisplay();
                                ?>
                            </b>
                        </button>
                    </form>
                <li>

            </ul>
        </nav>
    </header>
    <h1>Bank Account</h1>

    <div class="action-form form">
        <form method="post">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" required>
            <label for="transfer_to">Transfer To</label>
            <select name="transfer_to" id="transfer_to">
                <option value="">Select Option</option>
                <option value="Feon">Feon</option>
                <option value="Vira">Vira</option>
            </select>
            <button type="submit" name="deposit">Deposit</button>
            <button type="submit" name="withdraw">Withdraw</button>
            <button type="submit" name="transfer">Transfer</button>
        </form>
    </div>

    <div class="balance">
        <?php
        $account->checkBalance();
        ?>
    </div>
    <div style="overflow-x:auto;">

        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                    <th>Description</th>
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
                    echo "<td>{$transaction['description']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>