<?php
namespace format;

class Mall {

    private $time = '';
    private $curTime = '';
    private $warningHtml = '';
    private $errorHtml = '';
    private $logPath = '/data/wwwlogs/seaslog/default/';

    private $errorArr = array();
    private $warningArr = array();

    public function __construct($time) {
        $this->time = $time;
        $this->curTime = date("Y-m-d H:i:s");
    }

    public function run() {
        $collectHours = array();
        $time = $this->time;
        while (true) {
            if ($time > $this->curTime) {
                break;
            }
            $collectHours[] = date("YmdH", strtotime($time));
            $time = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($time)));
        }

        if (!empty($collectHours)) {
            foreach ($collectHours as $hour) {
                $this->errorProc($hour);
                $this->warningProc($hour);
            }
        }

        $this->htmlFormat();

        return $this;
    }

    /**
     * html 格式化
     */
    private function htmlFormat() {
        $htmlObj = new \lib\html\Array2Html();
        if (!empty($this->errorArr)) {
            $this->errorHtml = $htmlObj->setParam($this->errorArr)->transfer();
        }
        if (!empty($this->warningArr)) {
            $this->warningHtml = $htmlObj->setParam($this->warningArr)->transfer();
        }
    }

    /**
     * 处理一小时的文件
     */
    private function errorProc($hour) {
        $errorFile = $this->logPath . "error.{$hour}.log";
        if (!file_exists($errorFile)) {
            return;
        }
        if (count($this->errorArr) >= 500) {
            return;
        }

        $handle = @fopen($errorFile, 'r');
        if ($handle) {
            while(($buffer = fgets($handle, 4096)) !== false) {
                $line = explode('|', trim($buffer));
                // 必须大于日志的时间
                if (strtotime($this->time) > $line[2]) {
                    continue;
                }
                $this->errorArr[] = $line;
                if (count($this->errorArr) >= 500) {
                    break;
                }
            }
            fclose($handle);
        }
    }

    private function warningProc($hour) {
        $warningFile = $this->logPath . "warning.{$hour}.log";
    }

    public function warning() {
        return $this->warningHtml;
    }

    public function error() {
        return $this->errorHtml;
    }
}
