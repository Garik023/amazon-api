<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 9/16/2018
 * Time: 2:34 PM
 */
namespace App;

class Unzipper {
    public $localdir = '.';
    public $zipfiles = array();
    public function __construct() {
        // Read directory and pick .zip, .rar and .gz files.
        if ($dh = opendir($this->localdir)) {
            while (($file = readdir($dh)) !== FALSE) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'zip'
                    || pathinfo($file, PATHINFO_EXTENSION) === 'gz'
                    || pathinfo($file, PATHINFO_EXTENSION) === 'rar'
                ) {
                    $this->zipfiles[] = $file;
                }
            }
            closedir($dh);
            if (!empty($this->zipfiles)) {
                $GLOBALS['status'] = array('info' => '.zip or .gz or .rar files found, ready for extraction');
            }
            else {
                $GLOBALS['status'] = array('info' => 'No .zip or .gz or rar files found. So only zipping functionality available.');
            }
        }
    }
    /**
     * Prepare and check zipfile for extraction.
     *
     * @param string $archive
     *   The archive name including file extension. E.g. my_archive.zip.
     * @param string $destination
     *   The relative destination path where to extract files.
     */
    public function prepareExtraction($archive, $destination = '') {
        // Determine paths.
        if (empty($destination)) {
            $extpath = $this->localdir;
        }
        else {
            $extpath = $this->localdir . '/' . $destination;
            // Todo: move this to extraction function.
            if (!is_dir($extpath)) {
                mkdir($extpath);
            }
        }
        // Only local existing archives are allowed to be extracted.
        if (in_array($archive, $this->zipfiles)) {
            self::extract($archive, $extpath);
        }
    }
    /**
     * Checks file extension and calls suitable extractor functions.
     *
     * @param string $archive
     *   The archive name including file extension. E.g. my_archive.zip.
     * @param string $destination
     *   The relative destination path where to extract files.
     */
    public static function extract($archive, $destination) {
        $ext = pathinfo($archive, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'zip':
                self::extractZipArchive($archive, $destination);
                break;
            case 'gz':
                self::extractGzipFile($archive, $destination);
                break;
            case 'rar':
                self::extractRarArchive($archive, $destination);
                break;
        }
    }
    /**
     * Decompress/extract a zip archive using ZipArchive.
     *
     * @param $archive
     * @param $destination
     */
    public static function extractZipArchive($archive, $destination) {
        // Check if webserver supports unzipping.
        if (!class_exists('ZipArchive')) {
            $GLOBALS['status'] = array('error' => 'Error: Your PHP version does not support unzip functionality.');
            return;
        }
        $zip = new ZipArchive;
        // Check if archive is readable.
        if ($zip->open($archive) === TRUE) {
            // Check if destination is writable
            if (is_writeable($destination . '/')) {
                $zip->extractTo($destination);
                $zip->close();
                $GLOBALS['status'] = array('success' => 'Files unzipped successfully');
            }
            else {
                $GLOBALS['status'] = array('error' => 'Error: Directory not writeable by webserver.');
            }
        }
        else {
            $GLOBALS['status'] = array('error' => 'Error: Cannot read .zip archive.');
        }
    }
    /**
     * Decompress a .gz File.
     *
     * @param string $archive
     *   The archive name including file extension. E.g. my_archive.zip.
     * @param string $destination
     *   The relative destination path where to extract files.
     */
    public static function extractGzipFile($archive, $destination) {
        // Check if zlib is enabled
        if (!function_exists('gzopen')) {
            $GLOBALS['status'] = array('error' => 'Error: Your PHP has no zlib support enabled.');
            return;
        }
        $filename = $destination;
        $gzipped = gzopen($archive, "rb");
        $file = fopen($filename, "w");
        while ($string = gzread($gzipped, 4096)) {
            fwrite($file, $string, strlen($string));
        }
        gzclose($gzipped);
        fclose($file);
        return true;
        // Check if file was extracted.
//        if (file_exists($filename)) {
//            $GLOBALS['status'] = array('success' => 'File unzipped successfully.');
//            // If we had a tar.gz file, let's extract that tar file.
//            if (pathinfo($filename, PATHINFO_EXTENSION) == 'tar') {
//                $phar = new PharData($filename);
//                if ($phar->extractTo($filename)) {
//                    $GLOBALS['status'] = array('success' => 'Extracted tar.gz archive successfully.');
//                    // Delete .tar.
//                    unlink($destination . '/' . $filename);
//                }
//            }
//        }
//        else {
//            $GLOBALS['status'] = array('error' => 'Error unzipping file.');
//        }
    }
    /**
     * Decompress/extract a Rar archive using RarArchive.
     *
     * @param string $archive
     *   The archive name including file extension. E.g. my_archive.zip.
     * @param string $destination
     *   The relative destination path where to extract files.
     */
    public static function extractRarArchive($archive, $destination) {
        // Check if webserver supports unzipping.
        if (!class_exists('RarArchive')) {
            $GLOBALS['status'] = array('error' => 'Error: Your PHP version does not support .rar archive functionality. <a class="info" href="http://php.net/manual/en/rar.installation.php" target="_blank">How to install RarArchive</a>');
            return;
        }
        // Check if archive is readable.
        if ($rar = RarArchive::open($archive)) {
            // Check if destination is writable
            if (is_writeable($destination . '/')) {
                $entries = $rar->getEntries();
                foreach ($entries as $entry) {
                    $entry->extract($destination);
                }
                $rar->close();
                $GLOBALS['status'] = array('success' => 'Files extracted successfully.');
            }
            else {
                $GLOBALS['status'] = array('error' => 'Error: Directory not writeable by webserver.');
            }
        }
        else {
            $GLOBALS['status'] = array('error' => 'Error: Cannot read .rar archive.');
        }
    }
}

?>