<?php
function getCurrentTimeInUserTimezone($timezone) {
    $dateTime = new DateTime("now", new DateTimeZone($timezone));
    return $dateTime->format('H:i');
}

function getGreetingBasedOnTime($timezone) {
    $hour = (int) (new DateTime("now", new DateTimeZone($timezone)))->format('H');
    if ($hour < 12) {
        return "Good morning";
    } elseif ($hour < 18) {
        return "Good afternoon";
    } else {
        return "Good evening";
    }
}

function getRandomLineFromFile($fileName) {
    $lines = file($fileName, FILE_IGNORE_NEW_LINES);
    return $lines[array_rand($lines)];
}

function getMessageBasedOnTime($greeting) {
    $morningMessages = [
        "I hope you're having a wonderful morning",
        "Wishing you a fantastic start to your day",
        "Hope your morning is as bright as your smile",
        "Good morning! Have a lovely day ahead"
    ];

    $afternoonMessages = [
        "I hope your afternoon is going great",
        "Enjoy your afternoon!",
        "Good afternoon! Keep shining",
        "Hope your day is going well"
    ];

    $eveningMessages = [
        "I hope you had a wonderful day",
        "Good evening! Relax and unwind",
        "Hope you have a peaceful evening",
        "Wishing you a cozy and restful evening"
    ];

    switch ($greeting) {
        case "Good morning":
            return $morningMessages[array_rand($morningMessages)];
        case "Good afternoon":
            return $afternoonMessages[array_rand($afternoonMessages)];
        case "Good evening":
            return $eveningMessages[array_rand($eveningMessages)];
    }
}

function getBackgroundColorBasedOnTime($timezone) {
    $hour = (int) (new DateTime("now", new DateTimeZone($timezone)))->format('H');
    if ($hour < 6) {
        return "#2c3e50"; // Very early morning
    } elseif ($hour < 12) {
        return "#3498db"; // Morning
    } elseif ($hour < 18) {
        return "#f1c40f"; // Afternoon
    } elseif ($hour < 20) {
        return "#e67e22"; // Early evening
    } else {
        return "#34495e"; // Night
    }
}

function getVisitCountFromCookie() {
    if (isset($_COOKIE['visitCount'])) {
        return (int)$_COOKIE['visitCount'];
    }
    return 0;
}

function setVisitCountCookie($visitCount) {
    setcookie('visitCount', $visitCount, time() + (7 * 24 * 60 * 60), "/"); // Cookie expires in 1 week
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $timezone = isset($_POST['timezone']) ? $_POST['timezone'] : 'America/New_York';
    $greeting = getGreetingBasedOnTime($timezone);
    $name = getRandomLineFromFile('names.txt');
    $emoji = getRandomLineFromFile('emojis.txt');
    $message = getMessageBasedOnTime($greeting);
    $backgroundColor = getBackgroundColorBasedOnTime($timezone);
    $currentTime = getCurrentTimeInUserTimezone($timezone);

    $visitCountFromCookie = getVisitCountFromCookie();
    $visitCountFromCookie++;
    setVisitCountCookie($visitCountFromCookie);

    $response = array(
        "greeting" => $greeting,
        "name" => $name,
        "message" => $message,
        "emoji" => $emoji,
        "backgroundColor" => $backgroundColor,
        "timezone" => $timezone,
        "currentTime" => $currentTime,
        "visitCount" => $visitCountFromCookie
    );

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #2c3e50;
            transition: background 0.5s;
            color: white;
        }
        .container {
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 80%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'timezone=' + encodeURIComponent(timezone)
            })
            .then(response => response.json())
            .then(data => {
                document.body.style.background = data.backgroundColor;
                document.querySelector('.container h1').innerText = `${data.greeting}, ${data.name}`;
                document.querySelector('.container p').innerText = `${data.message} ${data.emoji}`;
                document.querySelector('.container .info').innerText = `Current Time: ${data.currentTime} (${data.timezone})`;
                document.querySelector('.container .visits').innerText = `You've visited this site ${data.visitCount} times in the last week. Thank you ❤`;
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Loading...</h1>
        <p></p>
        <p class="info"></p>
        <p class="visits"></p>
    </div>
</body>
</html>
