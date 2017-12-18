<?php
/**
 * Created by PhpStorm.
 * User: Арсен
 * Date: 23.07.2017
 * Time: 15:35
 */

namespace Viper\Daemon;


use Viper\Support\DaemonLogger;
use Viper\Support\Util;

class DaemonHistorian implements Chronicle
{
    private const LOGS_FOLDER = ROOT.'/storage/daemon/logs/';
    private const DAEMON_STORAGE = ROOT.'/storage/daemon/';

    private $file;
    private $succFile;
    private $errFile;
    private $logger;
    private $errLogger;

    function __construct($id)
    {
        $this -> file = self::DAEMON_STORAGE.$id;
        $this -> succFile = self::LOGS_FOLDER.$id.'.log';
        $this -> errFile = self::LOGS_FOLDER.$id.'.error.log';

        $this -> logger = new DaemonLogger($this -> succFile);
        $this -> errLogger = new DaemonLogger($this -> errFile);
    }

    /**
     * Logs a successful action
     * @param string $message
     */
    function actionLog(string $message) : void
    {
        $this -> logger -> write($message);
    }

    /**
     * Logs an error
     * @param string $err
     */
    function errorLog(string $err): void
    {
        $this -> errLogger -> write($err);
    }

    /**
     * Reads daemon log
     * @return string
     */
    function getLog() : string
    {
        return file_get_contents($this -> logger -> getFile());
    }

    /**
     * Reads daemon log
     * @return string
     */
    function getErrorLog() : string
    {
        return file_get_contents($this -> errLogger -> getFile());
    }

    /**
     * Cleans daemon log
     */
    function clearLog() : void
    {
        @unlink($this -> errLogger -> getFile());
        @unlink($this -> logger -> getFile());
        @unlink($this -> errLogger -> getFile().'.shell');
        @unlink($this -> logger -> getFile().'.shell');
    }

    /**
     * Stores daemon data
     * @param array $data
     */
    function store(array $data) : void
    {
        Util::put($this -> file, serialize($data));
    }

    /**
     * Returns daemon data from memory
     * @return array
     */
    function restore() : ?array
    {
        if (file_exists($this -> file))
            return unserialize(file_get_contents($this -> file));
        else return NULL;
    }

    /**
     * @return string
     */
    public function getSuccFile(): string
    {
        return $this->succFile;
    }

    /**
     * @return string
     */
    public function getErrFile(): string
    {
        return $this->errFile;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}