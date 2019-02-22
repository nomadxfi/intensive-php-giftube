<?php
require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'functions.php';

/* BEGIN STATE 01 */
$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");
/* END STATE 01 */

/* BEGIN STATE 02 */
$mailer = new Swift_Mailer($transport);
/* END STATE 02 */

/* BEGIN STATE 03 */
$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
/* END STATE 03 */

/* BEGIN STATE 04 */
$sql = "SELECT id, title, show_count, path FROM gifs g WHERE MONTH(dt_add) = MONTH(NOW()) "
     . "AND YEAR(dt_add) = YEAR(NOW()) ORDER BY show_count DESC LIMIT 3";

$res = mysqli_query($link, $sql);
/* END STATE 04 */

/* BEGIN STATE 05 */
if ($res && mysqli_num_rows($res)) {
    $gifs = mysqli_fetch_all($res, MYSQLI_ASSOC);

    /* BEGIN STATE 06 */
    $res = mysqli_query($link, "SELECT email, name FROM users");

    if ($res && mysqli_num_rows($res)) {
        $users = mysqli_fetch_all($res, MYSQLI_ASSOC);
        /* BEGIN STATE 07 */
        $recipients = [];

        foreach ($users as $user) {
            $recipients[$user['email']] = $user['name'];
        }
        /* END STATE 07 */

        /* BEGIN STATE 08 */
        $message = new Swift_Message();
        $message->setSubject("Самые горячие гифки за этот месяц");
        $message->setFrom(['keks@phpdemo.ru' => 'GifTube']);
        $message->setBcc($recipients);
        /* END STATE 08 */

        /* BEGIN STATE 09 */
        $msg_content = include_template('month_email.php', ['gifs' => $gifs]);
        $message->setBody($msg_content, 'text/html');
        /* END STATE 09 */

        /* BEGIN STATE 10 */
        $result = $mailer->send($message);
        /* END STATE 10 */

        /* BEGIN STATE 11 */
        if ($result) {
            print("Рассылка успешно отправлена");
        }
        else {
            print("Не удалось отправить рассылку: " . $logger->dump());
        }
        /* END STATE 11 */
    }
    /* END STATE 06 */
}
/* END STATE 05 */