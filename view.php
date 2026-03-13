<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$server = $_POST['server'] ?? 'gmail';

$servers = [
    'gmail' => ['imap.gmail.com', 993],
    'yahoo' => ['imap.mail.yahoo.com', 993],
    'outlook' => ['imap-mail.outlook.com', 993]
];

$host = $servers[$server][0];
$port = $servers[$server][1];

$mailbox = "{{$host}:{$port}/imap/ssl}INBOX";

$inbox = imap_open($mailbox, $email, $password);

if (!$inbox) {
    die("Gagal konek: " . imap_last_error());
}

$emails = imap_search($inbox, 'ALL');

if (!$emails) {
    $emails = [];
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>YanzMods - Inbox</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; color: #fff; font-family: Arial; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding: 20px; border: 1px solid #333; background: #111; }
        .header h1 { color: #00ff00; font-size: 24px; margin-bottom: 10px; }
        .header p { color: #666; font-size: 12px; }
        .inbox { background: #111; border: 1px solid #333; }
        .email-item { padding: 15px; border-bottom: 1px solid #333; cursor: pointer; }
        .email-item:hover { background: #1a1a1a; }
        .email-item .from { color: #00ff00; font-weight: bold; margin-bottom: 5px; }
        .email-item .subject { color: #fff; margin-bottom: 5px; }
        .email-item .date { color: #666; font-size: 12px; }
        .email-view { background: #111; border: 1px solid #333; margin-top: 20px; padding: 20px; display: none; }
        .email-view .header { margin-bottom: 20px; padding: 0; border: none; text-align: left; }
        .email-view .header h2 { color: #00ff00; margin-bottom: 10px; }
        .email-view .header p { color: #666; margin-bottom: 5px; }
        .email-view .body { color: #fff; line-height: 1.6; }
        .back-btn { margin-bottom: 20px; padding: 10px 20px; background: #00ff00; color: #000; border: none; cursor: pointer; font-weight: bold; }
        .back-btn:hover { background: #00cc00; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>YANZMODS EMAIL VIEWER</h1>
            <p>© RianModss - Quantum V13.0</p>
        </div>
        
        <button class="back-btn" onclick="showInbox()">Kembali ke Inbox</button>
        
        <div id="inbox" class="inbox">
            <?php if (empty($emails)): ?>
                <div style="padding: 20px; text-align: center; color: #666;">
                    Tidak ada email
                </div>
            <?php endif; ?>
            
            <?php foreach(array_reverse($emails) as $email_number): ?>
                <?php 
                $header = imap_headerinfo($inbox, $email_number);
                $from = $header->from[0];
                $from_name = $from->personal ?? $from->mailbox . '@' . $from->host;
                $subject = $header->subject ?? '(No Subject)';
                $date = date('d M Y H:i', $header->udate);
                ?>
                <div class="email-item" onclick="viewEmail(<?php echo $email_number; ?>)">
                    <div class="from"><?php echo htmlspecialchars($from_name); ?></div>
                    <div class="subject"><?php echo htmlspecialchars($subject); ?></div>
                    <div class="date"><?php echo $date; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="emailView" class="email-view"></div>
    </div>
    
    <script>
    function viewEmail(num) {
        document.getElementById('inbox').style.display = 'none';
        document.getElementById('emailView').style.display = 'block';
        
        fetch('get_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=<?php echo urlencode($email); ?>&password=<?php echo urlencode($password); ?>&server=<?php echo $server; ?>&num=' + num
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('emailView').innerHTML = data;
        });
    }
    
    function showInbox() {
        document.getElementById('inbox').style.display = 'block';
        document.getElementById('emailView').style.display = 'none';
    }
    </script>
</body>
</html>
<?php imap_close($inbox); ?>