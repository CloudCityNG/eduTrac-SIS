<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Core Functions
 *
 * @license GPLv3
 *         
 * @since 3.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
define('CURRENT_RELEASE', '6.2.0');
define('RELEASE_TAG', trim(_file_get_contents(BASE_PATH . 'RELEASE')));

$app = \Liten\Liten::getInstance();
use \League\Event\Event;
use \PHPBenchmark\HtmlView;
use \PHPBenchmark\Monitor;
use \PHPBenchmark\MonitorInterface;

/**
 * Retrieves eduTrac site root url.
 *
 * @since 4.1.9
 * @uses $app->hook->apply_filter() Calls 'base_url' filter.
 *      
 * @return string eduTrac SIS root url.
 */
function get_base_url()
{
    $app = \Liten\Liten::getInstance();
    $url = url('/');
    return $app->hook->apply_filter('base_url', $url);
}

/**
 * Custom make directory function.
 *
 * This function will check if the path is an existing directory,
 * if not, then it will be created with set permissions and also created
 * recursively if needed.
 *
 * @since 6.1.00
 * @param string $path
 *            Path to be created.
 * @return string
 */
function _mkdir($path)
{
    if ('' == _trim($path)) {
        $message = _t('Invalid directory path: Empty path given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    if (!is_dir($path)) {
        if (!mkdir($path, 0755, true)) {
            etsis_monolog('core_function', sprintf(_t('The following directory could not be created: %s'), $path), 'addError');

            return;
        }
    }
}

/**
 * Displays the returned translated text.
 *
 * @since 1.0.0
 * @param type $msgid
 *            The translated string.
 * @param type $domain
 *            Domain lookup for translated text.
 * @return string Translated text according to current locale.
 */
function _t($msgid, $domain = '')
{
    if ($domain !== '') {
        return d__($domain, $msgid);
    } else {
        return d__('edutrac-sis', $msgid);
    }
}

function getPathInfo($relative)
{
    $app = \Liten\Liten::getInstance();
    $base = basename(BASE_PATH);
    if (strpos($app->req->server['REQUEST_URI'], DS . $base . $relative) === 0) {
        return $relative;
    } else {
        return $app->req->server['REQUEST_URI'];
    }
}

/**
 * Custom function to use curl, fopen, or use file_get_contents
 * if curl is not available.
 *
 * @since 5.0.1
 * @param string $filename
 *            Resource to read.
 * @param bool $use_include_path
 *            Whether or not to use include path.
 * @param bool $context
 *            Whether or not to use a context resource.
 */
function _file_get_contents($filename, $use_include_path = false, $context = true)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Filter the boolean for include path.
     *
     * @since 6.2.4
     * @var bool $use_include_path
     * @return bool
     */
    $use_include_path = $app->hook->apply_filter('trigger_include_path_search', $use_include_path);

    /**
     * Filter the context resource.
     *
     * @since 6.2.4
     * @var bool $context
     * @return bool
     */
    $context = $app->hook->apply_filter('resource_context', $context);

    $opts = [
        'http' => [
            'timeout' => 360.0
        ]
    ];

    /**
     * Filters the stream context create options.
     *
     * @since 6.2.4
     * @param array $opts Array of options.
     * @return mixed
     */
    $opts = $app->hook->apply_filter('stream_context_create_options', $opts);

    if ($context === true) {
        $context = stream_context_create($opts);
    } else {
        $context = null;
    }

    $result = file_get_contents($filename, $use_include_path, $context);

    if ($result) {
        return $result;
    } else {
        $handle = fopen($filename, "r", $use_include_path, $context);
        $contents = stream_get_contents($handle);
        fclose($handle);
        if ($contents) {
            return $contents;
        } else
        if (!function_exists('curl_init')) {
            return false;
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $filename);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 360);
            $output = curl_exec($ch);
            curl_close($ch);
            if ($output) {
                return $output;
            } else {
                return false;
            }
        }
    }
}

/**
 * Bookmarking initialization function.
 *
 * @since 1.1.3
 */
function benchmark_init()
{
    if (!file_exists(BASE_PATH . 'config.php')) {
        return false;
    }
    if (_h(get_option('enable_benchmark')) == 1) {
        Monitor::instance()
            ->init()
            ->addListener(Monitor::EVENT_SHUT_DOWN, function(Event $evt, MonitorInterface $monitor) {
                $htmlView = new HtmlView();
                echo $htmlView->getView($monitor);
            });
    }
}
if (!function_exists('imgResize')) {

    function imgResize($width, $height, $target)
    {
        // takes the larger size of the width and height and applies the formula. Your function is designed to work with any image in any size.
        if ($width > $height) {
            $percentage = ($target / $width);
        } else {
            $percentage = ($target / $height);
        }

        // gets the new value and applies the percentage, then rounds the value
        $width = round($width * $percentage);
        $height = round($height * $percentage);
        // returns the new sizes in html image tag format...this is so you can plug this function inside an image tag so that it will set the image to the correct size, without putting a whole script into the tag.
        return "width=\"$width\" height=\"$height\"";
    }
}

// An alternative function of using the echo command.
if (!function_exists('_e')) {

    function _e($string)
    {
        echo $string;
    }
}

if (!function_exists('clickableLink')) {

    function clickableLink($text = '')
    {
        $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
        $ret = ' ' . $text;
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
        $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
        $ret = substr($ret, 1);
        return $ret;
    }
}

/**
 * Hide menu links by functions and/or by
 * permissions.
 *
 * @since 4.0.4
 */
function hl($f, $p = NULL)
{
    if (function_exists($f)) {
        return ' style="display:none !important;"';
    }
    if ($p !== NULL) {
        return ae($p);
    }
}

/**
 * Function used to check the installation
 * of a particular module.
 * If module exists,
 * unhide it's links throughout the system.
 */
function ml($func)
{
    if (!function_exists($func)) {
        return ' style="display:none !important;"';
    }
}

/**
 * When enabled, appends url string in order to give
 * benchmark statistics.
 *
 * @since 1.0.0
 */
function bm()
{
    if (get_option('enable_benchmark') == 1) {
        return '?php-benchmark-test=1&display-data=1';
    }
}

function _bool($num)
{
    switch ($num) {
        case 1:
            return 'Yes';
            break;
        case 0:
            return 'No';
            break;
    }
}

function translate_class_year($year)
{
    switch ($year) {
        case 'FR':
            return 'Freshman';
            break;

        case 'SO':
            return 'Sophomore';
            break;

        case 'JR':
            return 'Junior';
            break;

        case 'SR':
            return 'Senior';
            break;

        case 'GR':
            return 'Grad Student';
            break;

        case 'PhD':
            return 'PhD Student';
            break;
    }
}

function translate_addr_status($status)
{
    switch ($status) {
        case 'C':
            return 'Current';
            break;

        case 'I':
            return 'Inactive';
            break;
    }
}

function translate_addr_type($type)
{
    switch ($type) {
        case 'H':
            return 'Home';
            break;

        case 'P':
            return 'Permanent';
            break;

        case 'B':
            return 'Business';
            break;
    }
}

/**
 * Function to help with SQL injection when using SQL terminal
 * and the saved query screens.
 */
function strstra($haystack, $needles = array(), $before_needle = false)
{
    $chr = array();
    foreach ($needles as $needle) {
        $res = strstr($haystack, $needle, $before_needle);
        if ($res !== false)
            $chr[$needle] = $res;
    }
    if (empty($chr))
        return false;
    return min($chr);
}

function print_gzipped_page()
{
    global $HTTP_ACCEPT_ENCODING;
    if (headers_sent()) {
        $encoding = false;
    } elseif (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false) {
        $encoding = 'x-gzip';
    } elseif (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false) {
        $encoding = 'gzip';
    } else {
        $encoding = false;
    }

    if ($encoding) {
        $contents = ob_get_contents();
        ob_end_clean();
        header('Content-Encoding: ' . $encoding);
        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
        $size = strlen($contents);
        $contents = gzcompress($contents, 9);
        $contents = substr($contents, 0, $size);
        print($contents);
        exit();
    } else {
        ob_end_flush();
        exit();
    }
}

function percent($num_amount, $num_total)
{
    $count1 = $num_amount / $num_total;
    $count2 = $count1 * 100;
    $count = number_format($count2, 0);
    return $count;
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout eduTrac to allow for both string or array
 * to be merged into another array.
 *
 * @since 6.2.0
 * @param string|array $args
 *            Value to merge with $defaults
 * @param array $defaults
 *            Optional. Array that serves as the defaults. Default empty.
 * @return array Merged user defined values with defaults.
 */
function etsis_parse_args($args, $defaults = '')
{
    if (is_object($args)) {
        $r = get_object_vars($args);
    } elseif (is_array($args)) {
        $r = $args;
    } else {
        etsis_parse_str($args, $r);
    }

    if (is_array($defaults)) {
        return array_merge($defaults, $r);
    }

    return $r;
}

function head_release_meta()
{
    echo "<meta name='generator' content='eduTrac SIS " . CURRENT_RELEASE . "'>\n";
}

function foot_release()
{
    if (CURRENT_RELEASE != RELEASE_TAG) {
        $release = "r" . CURRENT_RELEASE . ' (t' . RELEASE_TAG . ')';
    } else {
        $release = "r" . CURRENT_RELEASE;
    }
    return $release;
}

/**
 * Hashes a plain text password.
 *
 * @since 6.2.0
 * @param string $password
 *            Plain text password
 * @return mixed
 */
function etsis_hash_password($password)
{
    if ('' == _trim($password)) {
        $message = _t('Invalid password: empty password given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    // By default, use the portable hash from phpass
    $hasher = new \app\src\PasswordHash(8, FALSE);

    return $hasher->HashPassword($password);
}

/**
 * Checks a plain text password against a hashed password.
 *
 * @since 6.2.0
 * @param string $password
 *            Plain test password.
 * @param string $hash
 *            Hashed password in the database to check against.
 * @param int $person_id
 *            Person ID.
 * @return mixed
 */
function etsis_check_password($password, $hash, $person_id = '')
{
    $app = \Liten\Liten::getInstance();
    // If the hash is still md5...
    if (strlen($hash) <= 32) {
        $check = ($hash == md5($password));
        if ($check && $person_id) {
            // Rehash using new hash.
            etsis_set_password($password, $person_id);
            $hash = etsis_hash_password($password);
        }
        return $app->hook->apply_filter('check_password', $check, $password, $hash, $person_id);
    }

    // If the stored hash is longer than an MD5, presume the
    // new style phpass portable hash.
    $hasher = new \app\src\PasswordHash(8, FALSE);

    $check = $hasher->CheckPassword($password, $hash);

    return $app->hook->apply_filter('check_password', $check, $password, $hash, $person_id);
}

/**
 * Used by etsis_check_password in order to rehash
 * an old password that was hashed using MD5 function.
 *
 * @since 6.2.0
 * @param string $password
 *            Person password.
 * @param int $person_id
 *            Person ID.
 * @return mixed
 */
function etsis_set_password($password, $person_id)
{
    $app = \Liten\Liten::getInstance();
    $hash = etsis_hash_password($password);
    $q = $app->db->person();
    $q->password = $hash;
    $q->where('personID = ?', $person_id)->update();
}

/**
 * Prints a list of timezones which includes
 * current time.
 *
 * @return array
 */
function generate_timezone_list()
{
    static $regions = array(
        \DateTimeZone::AFRICA,
        \DateTimeZone::AMERICA,
        \DateTimeZone::ANTARCTICA,
        \DateTimeZone::ASIA,
        \DateTimeZone::ATLANTIC,
        \DateTimeZone::AUSTRALIA,
        \DateTimeZone::EUROPE,
        \DateTimeZone::INDIAN,
        \DateTimeZone::PACIFIC
    );

    $timezones = array();
    foreach ($regions as $region) {
        $timezones = array_merge($timezones, \DateTimeZone::listIdentifiers($region));
    }

    $timezone_offsets = array();
    foreach ($timezones as $timezone) {
        $tz = new \DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
    }

    // sort timezone by timezone name
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach ($timezone_offsets as $timezone => $offset) {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate('H:i', abs($offset));

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

        $t = new \DateTimeZone($timezone);
        $c = new \DateTime(null, $t);
        $current_time = $c->format('g:i A');

        $timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
    }

    return $timezone_list;
}

/**
 * Get age by birthdate.
 *
 * @param string $birthdate
 *            Person's birth date.
 * @return mixed
 */
function getAge($birthdate = '0000-00-00')
{
    if ($birthdate == '0000-00-00')
        return 'Unknown';

    $bits = explode('-', $birthdate);
    $age = date('Y') - $bits[0] - 1;

    $arr[1] = 'm';
    $arr[2] = 'd';

    for ($i = 1; $arr[$i]; $i ++) {
        $n = date($arr[$i]);
        if ($n < $bits[$i])
            break;
        if ($n > $bits[$i]) {
            ++$age;
            break;
        }
    }
    return $age;
}

/**
 * Converts a string into unicode values.
 *
 * @since 4.3
 * @param string $string            
 * @return mixed
 */
function unicoder($string)
{
    $p = str_split(trim($string));
    $new_string = '';
    foreach ($p as $val) {
        $new_string .= '&#' . ord($val) . ';';
    }
    return $new_string;
}

/**
 * Checks against certain keywords when the SQL
 * terminal and saved query screens are used.
 * Helps
 * against database manipulation and SQL injection.
 *
 * @since 1.0.0
 * @return boolean
 */
function forbidden_keyword()
{
    $array = [
        "create",
        "delete",
        "drop table",
        "alter",
        "insert",
        "change",
        "convert",
        "modifies",
        "optimize",
        "purge",
        "rename",
        "replace",
        "revoke",
        "unlock",
        "truncate",
        "anything",
        "svc",
        "write",
        "into",
        "--",
        "1=1",
        "1 = 1",
        "\\",
        "?",
        "'x'",
        "loop",
        "exit",
        "leave",
        "undo",
        "upgrade",
        "html",
        "script",
        "css",
        "x=x",
        "x = x",
        "everything",
        "anyone",
        "everyone",
        "upload",
        "&",
        "&amp;",
        "xp_",
        "$",
        "0=0",
        "0 = 0",
        "X=X",
        "X = X",
        "mysql",
        "'='",
        "XSS",
        "mysql_",
        "die",
        "password",
        "auth_token",
        "alert",
        "img",
        "src",
        "drop tables",
        "drop index",
        "drop database",
        "drop column",
        "show tables in",
        "show databases",
        " in ",
        "slave",
        "hosts",
        "grants",
        "warnings",
        "variables",
        "triggers",
        "privileges",
        "engine",
        "processlist",
        "relaylog",
        "errors",
        "information_schema",
        "mysqldump",
        "hostname",
        "root",
        "use",
        "describe",
        "flush",
        "privileges",
        "mysqladmin",
        "set",
        "quit",
        "-u",
        "-p",
        "load data",
        "backup table",
        "cache index",
        "change master to",
        "commit",
        "drop user",
        "drop view",
        "kill",
        "load index",
        "load table",
        "lock",
        "reset",
        "restore",
        "rollback",
        "savepoint",
        "show character set",
        "show collation",
        "innodb",
        "show table status"
    ];
    return $array;
}

/**
 * The myeduTrac welcome message filter.
 *
 * @since 4.3
 */
function the_myet_welcome_message()
{
    $app = \Liten\Liten::getInstance();
    $welcome_message = get_option('myet_welcome_message');
    $welcome_message = $app->hook->apply_filter('the_myet_welcome_message', $welcome_message);
    $welcome_message = str_replace(']]>', ']]&gt;', $welcome_message);
    return $welcome_message;
}

/**
 * Returns the template header information
 *
 * @since 6.0.00
 * @param
 *            string (optional) $template_dir loads templates from specified folder
 * @return mixed
 */
function get_templates_header($template_dir = '')
{
    $templates_header = [];
    if ($handle = opendir($template_dir)) {

        while ($file = readdir($handle)) {
            if (is_file($template_dir . $file)) {
                if (strpos($template_dir . $file, '.template.php')) {
                    $fp = fopen($template_dir . $file, 'r');
                    // Pull only the first 8kiB of the file in.
                    $template_data = fread($fp, 8192);
                    fclose($fp);

                    preg_match('|Template Name:(.*)$|mi', $template_data, $name);
                    preg_match('|Template Slug:(.*)$|mi', $template_data, $template_slug);

                    foreach (array(
                    'name',
                    'template_slug'
                    ) as $field) {
                        if (!empty(${$field}))
                            ${$field} = trim(${$field}[1]);
                        else
                            ${$field} = '';
                    }
                    $template_data = array(
                        'filename' => $file,
                        'Name' => $name,
                        'Title' => $name,
                        'Slug' => $template_slug
                    );
                    $templates_header[] = $template_data;
                }
            } else
            if ((is_dir($template_dir . $file)) && ($file != '.') && ($file != '..')) {
                get_templates_header($template_dir . $file . '/');
            }
        }

        closedir($handle);
    }
    return $templates_header;
}

/**
 * Returns the layout header information
 *
 * @since 6.0.00
 * @param
 *            string (optional) $layout_dir loads layouts from specified folder
 * @return mixed
 */
function get_layouts_header($layout_dir = '')
{
    $layouts_header = [];
    if ($handle = opendir($layout_dir)) {

        while ($file = readdir($handle)) {
            if (is_file($layout_dir . $file)) {
                if (strpos($layout_dir . $file, '.layout.php')) {
                    $fp = fopen($layout_dir . $file, 'r');
                    // Pull only the first 8kiB of the file in.
                    $layout_data = fread($fp, 8192);
                    fclose($fp);

                    preg_match('|Layout Name:(.*)$|mi', $layout_data, $name);
                    preg_match('|Layout Slug:(.*)$|mi', $layout_data, $layout_slug);

                    foreach (array(
                    'name',
                    'layout_slug'
                    ) as $field) {
                        if (!empty(${$field}))
                            ${$field} = trim(${$field}[1]);
                        else
                            ${$field} = '';
                    }
                    $layout_data = array(
                        'filename' => $file,
                        'Name' => $name,
                        'Title' => $name,
                        'Slug' => $layout_slug
                    );
                    $layouts_header[] = $layout_data;
                }
            } else
            if ((is_dir($layout_dir . $file)) && ($file != '.') && ($file != '..')) {
                get_layouts_header($layout_dir . $file . '/');
            }
        }

        closedir($handle);
    }
    return $layouts_header;
}

/**
 * Subdomain as directory function uses the subdomain
 * of the install as a directory.
 *
 * @since 6.0.05
 * @return string
 */
function subdomain_as_directory()
{
    $subdomain = '';
    $domain_parts = explode('.', $_SERVER['SERVER_NAME']);
    if (count($domain_parts) == 3) {
        $subdomain = $domain_parts[0];
    } else {
        $subdomain = 'www';
    }
    return $subdomain;
}

/**
 * Returns the directory based on subdomain.
 *
 * @return mixed
 */
function cronDir()
{
    return APP_PATH . 'views/cron/' . subdomain_as_directory() . '/';
}

/**
 * Strips out all duplicate values and compact the array.
 *
 * @since 6.0.04
 * @param mixed $a
 *            An array that be compacted.
 * @return mixed
 */
function array_unique_compact($a)
{
    $tmparr = array_unique($a);
    $i = 0;
    foreach ($tmparr as $v) {
        $newarr[$i] = $v;
        $i ++;
    }
    return $newarr;
}

function check_mime_type($file, $mode = 0)
{
    if ('' == _trim($file)) {
        $message = _t('Invalid file: empty file given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    // mode 0 = full check
    // mode 1 = extension check only
    $mime_types = array(
        'txt' => 'text/plain',
        'csv' => 'text/plain',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        // adobe
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint'
    );

    $ext = strtolower(array_pop(explode('.', $file)));

    if (function_exists('mime_content_type') && $mode == 0) {
        $mimetype = mime_content_type($file);
        return $mimetype;
    }

    if (function_exists('finfo_open') && $mode == 0) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mimetype;
    } elseif (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }
}

/**
 * Check whether variable is an eduTrac SIS Error.
 *
 * Returns true if $object is an object of the \app\src\Core\etsis_Error class.
 *
 * @since 6.1.14
 * @param mixed $object
 *            Check if unknown variable is an \app\src\Core\etsis_Error object.
 * @return bool True, if \app\src\Core\etsis_Error. False, if not \app\src\Core\etsis_Error.
 */
function is_etsis_error($object)
{
    return ($object instanceof \app\src\Core\etsis_Error);
}

/**
 * Check whether variable is an eduTrac SIS Exception.
 *
 * Returns true if $object is an object of the `\app\src\Core\Exception\BaseException` class.
 *
 * @since 6.1.14
 * @param mixed $object
 *            Check if unknown variable is an `\app\src\Core\Exception\BaseException` object.
 * @return bool True, if `\app\src\Core\Exception\BaseException`. False, if not `\app\src\Core\Exception\BaseException`.
 */
function is_etsis_exception($object)
{
    return ($object instanceof \app\src\Core\Exception\BaseException);
}

/**
 * Returns the datetime of when the content of file was changed.
 *
 * @since 6.2.0
 * @param string $file
 *            Absolute path to file.
 */
function file_mod_time($file)
{
    return filemtime($file);
}

/**
 * Returns an array of function names in a file.
 *
 * @since 6.2.0
 * @param string $file
 *            The path to the file.
 * @param bool $sort
 *            If TRUE, sort results by function name.
 */
function get_functions_in_file($file, $sort = FALSE)
{
    $file = file($file);
    $functions = [];
    foreach ($file as $line) {
        $line = trim($line);
        if (substr($line, 0, 8) == 'function') {
            $functions[] = strtolower(substr($line, 9, strpos($line, '(') - 9));
        }
    }
    if ($sort) {
        asort($functions);
        $functions = array_values($functions);
    }
    return $functions;
}

/**
 * Checks a given file for any duplicated named user functions.
 *
 * @since 6.2.0
 * @param string $file_name            
 */
function is_duplicate_function($file_name)
{
    if ('' == _trim($file_name)) {
        $message = _t('Invalid file name: empty file name given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    $plugin = get_functions_in_file($file_name);
    $functions = get_defined_functions();
    $merge = array_merge($plugin, $functions['user']);
    if (count($merge) !== count(array_unique($merge))) {
        $dupe = array_unique(array_diff_assoc($merge, array_unique($merge)));
        foreach ($dupe as $key => $value) {
            return new \app\src\Core\etsis_Error('duplicate_function_error', sprintf(_t('The following function is already defined elsewhere: <strong>%s</strong>'), $value));
        }
    }
    return false;
}

/**
 * Performs a check within a php script and returns any other files
 * that might have been required or included.
 *
 * @since 6.2.0
 * @param string $file_name
 *            PHP script to check.
 */
function etsis_php_check_includes($file_name)
{
    if ('' == _trim($file_name)) {
        $message = _t('Invalid file name: empty file name given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    // NOTE that any file coming into this function has already passed the syntax check, so
    // we can assume things like proper line terminations
    $includes = [];
    // Get the directory name of the file so we can prepend it to relative paths
    $dir = dirname($file_name);

    // Split the contents of $fileName about requires and includes
    // We need to slice off the first element since that is the text up to the first include/require
    $requireSplit = array_slice(preg_split('/require|include/i', _file_get_contents($file_name)), 1);

    // For each match
    foreach ($requireSplit as $string) {
        // Substring up to the end of the first line, i.e. the line that the require is on
        $string = substr($string, 0, strpos($string, ";"));

        // If the line contains a reference to a variable, then we cannot analyse it
        // so skip this iteration
        if (strpos($string, "$") !== false) {
            continue;
        }

        // Split the string about single and double quotes
        $quoteSplit = preg_split('/[\'"]/', $string);

        // The value of the include is the second element of the array
        // Putting this in an if statement enforces the presence of '' or "" somewhere in the include
        // includes with any kind of run-time variable in have been excluded earlier
        // this just leaves includes with constants in, which we can't do much about
        if ($include = $quoteSplit[1]) {
            // If the path is not absolute, add the dir and separator
            // Then call realpath to chop out extra separators
            if (strpos($include, ':') === FALSE)
                $include = realpath($dir . DS . $include);

            array_push($includes, $include);
        }
    }

    return $includes;
}

/**
 * Performs a syntax and error check of a given PHP script.
 *
 * @since 6.2.0
 * @param string $file_name
 *            PHP script to check.
 * @param bool $check_includes
 *            If set to TRUE, will check if other files have been included.
 * @return void|\app\src\Core\Exception\Exception
 */
function etsis_php_check_syntax($file_name, $check_includes = true)
{
    // If it is not a file or we can't read it throw an exception
    if (!is_file($file_name) || !is_readable($file_name)) {
        return new \app\src\Core\Exception\Exception(_t('Cannot read file ') . $file_name, 'php_check_syntax');
    }

    $dupe_function = is_duplicate_function($file_name);

    if (is_etsis_error($dupe_function)) {
        return new \app\src\Core\Exception\Exception($dupe_function->get_error_message(), 'php_check_syntax');
    }

    // Sort out the formatting of the filename
    $file_name = realpath($file_name);

    // Get the shell output from the syntax check command
    $output = shell_exec('php -l "' . $file_name . '"');

    // Try to find the parse error text and chop it off
    $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, - 1, $count);

    // If the error text above was matched, throw an exception containing the syntax error
    if ($count > 0) {
        return new \app\src\Core\Exception\Exception(trim($syntaxError), 'php_check_syntax');
    }

    // If we are going to check the files includes
    if ($check_includes) {
        foreach (etsis_php_check_includes($file_name) as $include) {
            // Check the syntax for each include
            etsis_php_check_syntax($include);
        }
    }
}

/**
 * Validates a plugin and checks to make sure there are no syntax and/or
 * parsing errors.
 *
 * @since 6.2.0
 * @param string $plugin_name
 *            Name of the plugin file (i.e. moodle.plugin.php).
 */
function etsis_validate_plugin($plugin_name)
{
    $app = \Liten\Liten::getInstance();

    $plugin = str_replace('.plugin.php', '', $plugin_name);

    if (!file_exists(ETSIS_PLUGIN_DIR . $plugin . '/' . $plugin_name)) {
        $file = ETSIS_PLUGIN_DIR . $plugin_name;
    } else {
        $file = ETSIS_PLUGIN_DIR . $plugin . '/' . $plugin_name;
    }

    $error = etsis_php_check_syntax($file);
    if (is_etsis_exception($error)) {
        $app->flash('error_message', _t('Plugin could not be activated because it triggered a <strong>fatal error</strong>. <br /><br />') . $error->getMessage());
        return false;
    }

    if (file_exists($file)) {
        include_once ($file);
    }

    /**
     * Fires before a specific plugin is activated.
     *
     * $pluginName refers to the plugin's
     * name (i.e. moodle.plugin.php).
     *
     * @since 6.1.00
     * @param string $plugin_name
     *            The plugin's base name.
     */
    $app->hook->do_action('activate_plugin', $plugin_name);

    /**
     * Fires as a specifig plugin is being activated.
     *
     * $pluginName refers to the plugin's
     * name (i.e. moodle.plugin.php).
     *
     * @since 6.1.00
     * @param string $plugin_name
     *            The plugin's base name.
     */
    $app->hook->do_action('activate_' . $plugin_name);

    /**
     * Activate the plugin if there are no errors.
     *
     * @since 5.0.0
     * @param string $plugin_name
     *            The plugin's base name.
     */
    activate_plugin($plugin_name);

    /**
     * Fires after a plugin has been activated.
     *
     * $pluginName refers to the plugin's
     * name (i.e. moodle.plugin.php).
     *
     * @since 6.1.06
     * @param string $plugin_name
     *            The plugin's base name.
     */
    $app->hook->do_action('activated_plugin', $plugin_name);
}

/**
 * Single file writable atribute check.
 * Thanks to legolas558.users.sf.net
 *
 * @since 6.2.0
 * @param string $path            
 * @return true
 */
function win_is_writable($path)
{
    // will work in despite of Windows ACLs bug
    // NOTE: use a trailing slash for folders!!!
    // see http://bugs.php.net/bug.php?id=27609
    // see http://bugs.php.net/bug.php?id=30931
    if ($path{strlen($path) - 1} == '/') { // recursively return a temporary file path
        return win_is_writable($path . uniqid(mt_rand()) . '.tmp');
    } elseif (is_dir($path)) {
        return win_is_writable($path . '/' . uniqid(mt_rand()) . '.tmp');
    }
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = fopen($path, 'a');
    if ($f === false) {
        return false;
    }
    fclose($f);
    if (!$rm) {
        unlink($path);
    }
    return true;
}

/**
 * Alternative to PHP's native is_writable function due to a Window's bug.
 *
 * @since 6.2.0
 * @param string $path
 *            Path to check.
 */
function etsis_is_writable($path)
{
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
        return win_is_writable($path);
    } else {
        return is_writable($path);
    }
}

/**
 * Takes an array and turns it into an object.
 *
 * @param array $array
 *            Array of data.
 */
function array_to_object(array $array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = array_to_object($value);
        }
    }
    return (object) $array;
}

/**
 * Strip close comment and close php tags from file headers.
 *
 * @since 6.2.3
 * @param string $str
 *            Header comment to clean up.
 * @return string
 */
function _etsis_cleanup_file_header_comment($str)
{
    return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}

/**
 * Retrieve metadata from a file.
 *
 * Searches for metadata in the first 8kB of a file, such as a plugin or layout.
 * Each piece of metadata must be on its own line. Fields can not span multiple
 * lines, the value will get cut at the end of the first line.
 *
 * If the file data is not within that first 8kB, then the author should correct
 * their plugin file and move the data headers to the top.
 *
 * @since 6.2.3
 * @param string $file
 *            Path to the file.
 * @param array $default_headers
 *            List of headers, in the format array('HeaderKey' => 'Header Name').
 * @param string $context
 *            Optional. If specified adds filter hook "extra_{$context}_headers".
 *            Default empty.
 * @return array Array of file headers in `HeaderKey => Header Value` format.
 */
function etsis_get_file_data($file, $default_headers, $context = '')
{
    $app = \Liten\Liten::getInstance();
    // We don't need to write to the file, so just open for reading.
    $fp = fopen($file, 'r');
    // Pull only the first 8kB of the file in.
    $file_data = fread($fp, 8192);
    // PHP will close file handle.
    fclose($fp);
    // Make sure we catch CR-only line endings.
    $file_data = str_replace("\r", "\n", $file_data);
    /**
     * Filter extra file headers by context.
     *
     * The dynamic portion of the hook name, `$context`, refers to
     * the context where extra headers might be loaded.
     *
     * @since 6.2.3
     *       
     * @param array $extra_context_headers
     *            Empty array by default.
     */
    if ($context && $extra_headers = $app->hook->apply_filter("extra_{$context}_headers", [])) {
        $extra_headers = array_combine($extra_headers, $extra_headers); // keys equal values
        $all_headers = array_merge($extra_headers, (array) $default_headers);
    } else {
        $all_headers = $default_headers;
    }
    foreach ($all_headers as $field => $regex) {
        if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1])
            $all_headers[$field] = _etsis_cleanup_file_header_comment($match[1]);
        else
            $all_headers[$field] = '';
    }
    return $all_headers;
}

/**
 * Parses the plugin contents to retrieve plugin's metadata.
 *
 * The metadata of the plugin's data searches for the following in the plugin's
 * header. All plugin data must be on its own line. For plugin description, it
 * must not have any newlines or only parts of the description will be displayed
 * and the same goes for the plugin data. The below is formatted for printing.
 *
 * /*
 * Plugin Name: Name of Plugin
 * Plugin URI: Link to plugin information
 * Description: Plugin Description
 * Author: Plugin author's name
 * Author URI: Link to the author's web site
 * Version: Plugin version value.
 * Text Domain: Optional. Unique identifier, should be same as the one used in
 * load_plugin_textdomain()
 *
 * The first 8kB of the file will be pulled in and if the plugin data is not
 * within that first 8kB, then the plugin author should correct their plugin
 * and move the plugin data headers to the top.
 *
 * The plugin file is assumed to have permissions to allow for scripts to read
 * the file. This is not checked however and the file is only opened for
 * reading.
 *
 * @since 6.2.3
 *       
 * @param string $plugin_file
 *            Path to the plugin file
 * @param bool $markup
 *            Optional. If the returned data should have HTML markup applied.
 *            Default true.
 * @param bool $translate
 *            Optional. If the returned data should be translated. Default true.
 * @return array {
 *         Plugin data. Values will be empty if not supplied by the plugin.
 *        
 *         @type string $Name Name of the plugin. Should be unique.
 *         @type string $Title Title of the plugin and link to the plugin's site (if set).
 *         @type string $Description Plugin description.
 *         @type string $Author Author's name.
 *         @type string $AuthorURI Author's website address (if set).
 *         @type string $Version Plugin version.
 *         @type string $TextDomain Plugin textdomain.
 *         @type string $DomainPath Plugins relative directory path to .mo files.
 *         @type bool $Network Whether the plugin can only be activated network-wide.
 *         }
 */
function get_plugin_data($plugin_file, $markup = true, $translate = true)
{
    $default_headers = array(
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Version' => 'Version',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'TextDomain' => 'Text Domain'
    );
    $plugin_data = etsis_get_file_data($plugin_file, $default_headers, 'plugin');
    if ($markup || $translate) {
        $plugin_data = _get_plugin_data_markup_translate($plugin_file, $plugin_data, $markup, $translate);
    } else {
        $plugin_data['Title'] = $plugin_data['Name'];
        $plugin_data['AuthorName'] = $plugin_data['Author'];
    }
    return $plugin_data;
}

/**
 * Hide fields function.
 * 
 * Hides or unhides fields based on html element.
 * 
 * @param string $element .
 * @return string
 */
function etsis_field_css_class($element)
{
    $app = \Liten\Liten::getInstance();

    if (_h(get_option($element)) == 'hide') {
        return $app->hook->apply_filter('field_css_class', " $element");
    }
}

/**
 * A wrapper for htmLawed which is a set of functions
 * for html purifier
 *
 * @since 5.0
 * @param string $str            
 * @return mixed
 */
function _escape($t, $C = 1, $S = [])
{
    return htmLawed($t, $C, $S);
}

/**
 * Converts seconds to time format.
 * 
 * @since 6.2.11
 * @param numeric $seconds
 */
function etsis_seconds_to_time($seconds)
{
    $ret = "";

    /** get the days */
    $days = intval(intval($seconds) / (3600 * 24));
    if ($days > 0) {
        $ret .= "$days days ";
    }

    /** get the hours */
    $hours = (intval($seconds) / 3600) % 24;
    if ($hours > 0) {
        $ret .= "$hours hours ";
    }

    /** get the minutes */
    $minutes = (intval($seconds) / 60) % 60;
    if ($minutes > 0) {
        $ret .= "$minutes minutes ";
    }

    /** get the seconds */
    $seconds = intval($seconds) % 60;
    if ($seconds > 0) {
        $ret .= "$seconds seconds";
    }

    return $ret;
}
