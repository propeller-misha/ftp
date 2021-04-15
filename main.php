<?php
$ftp_server = '178.159.44.200';
$ftp_user_name = '1c_import';
$ftp_user_pass = 'SfS3HEbSTWzdrg';

// установка соединения
$conn_id = ftp_connect($ftp_server);

// вход с именем пользователя и паролем
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);


if ($_POST['saveFile']) {
    $file_name = $_POST['fileName'];
    $file_path = $_POST['filePath'];

    if (ftp_get($conn_id, $file_name, $file_path . '/' . $file_name, FTP_ASCII)) {
        echo 'Файл скачивается';
    } else {
        echo "При скачивании $remote_file в $remote_file произошла проблема\n";
    }
}

if ($_POST['changeDir']) {
    $path = $_POST['changeDir'];

    if (ftp_chdir($conn_id, $path)) {
        fileList($conn_id, ftp_pwd($conn_id), $login_result, $ftp_server);
        echo "Новая текущая директория: " . ftp_pwd($conn_id);
    } else {
        echo "Не удалось сменить директорию";
    }
}

if ($_POST['dirPath']) {
    $path = $_POST['dirPath'];

    fileList($conn_id, $path, $login_result, $ftp_server);
}

function fileList($conn_id, $path, $login_result, $ftp_server)
{
    if ((!$conn_id) || (!$login_result)) {
        echo "<h1>Не удалось установить соединение с FTP-сервером!</h1>";
        exit;
    } else {
        echo "<h1>Установлено соединение с FTP сервером $ftp_server</h1>";
    }
    // получить содержимое текущей директории
    $file_list = ftp_mlsd($conn_id, $path);

    if (is_array($file_list)) {
        echo '<ul>';
        foreach ($file_list as $remote_file) {
            if ($remote_file['type'] !== 'dir') {
                if ($remote_file['type'] !== 'cdir') {
                    if ($remote_file['type'] !== 'pdir') {
                
?>
                <li class="file"><span onClick='loadFile("<?php echo $path;?>", "<?php echo $remote_file['name'] ?>")'><?php echo $remote_file['name'] ?></li>
                <?php
                    }
                }
            } else {
                if (ftp_pwd($conn_id) === '/') {
                    if ($remote_file['type'] !== 'cdir') {
                        if ($remote_file['type'] !== 'pdir') {
                ?>
                            <li class="dir"><span onClick='changeDir("<?php echo $path . '/' . $remote_file['name'] ?>")'><?php echo $remote_file['name'] ?></span></li>
                        <?php
                        }
                    }
                } else {
                    if ($remote_file['type'] !== 'cdir') {
                        ?>
                        <li class="dir"><span onClick='changeDir("<?php echo $path . '/' . $remote_file['name'] ?>")'><?php echo $remote_file['name'] ?></span></li>
                <?php
                    }
                }
                ?>
<?php

            }
        }
        echo '</ul>';
    }
}

ftp_close($conn_id);
?>