<?php
include "config.php";

$result = $conn->query("SELECT * FROM tickers ORDER BY created_at DESC LIMIT 20");
$tickers = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

foreach ($tickers as $row): ?>
    <tr>
        <td><?= strtoupper($row["symbol"]) ?></td>
        <td><?= number_format($row["last_price"], 0, ',', '.') ?></td>
        <td><?= number_format($row["high"], 0, ',', '.') ?></td>
        <td><?= number_format($row["low"], 0, ',', '.') ?></td>
        <td>
            <?php 
                $chg = number_format($row["change_24h"], 2);
                $color = ($chg >= 0) ? "text-success" : "text-danger";
                echo "<span class='$color'>{$chg}%</span>";
            ?>
        </td>
        <td><?= $row["created_at"] ?></td>
    </tr>
<?php endforeach; ?>
