<?php
namespace lib\html;

class Array2Html {

    private $data = array();

    public function setParam($data) {
        $this->data = $data;
        return $this;
    }

    public function transfer() {
        $content = '<table>';
        foreach ($this->data as $arrLine) {
            if (empty($arrLine)) {
                continue;
            }
            $content .= "<tr>";
            foreach ($arrLine as $data) {
                $content .= "<td>{$data}</td>";
            }
            $content .= "</tr>";
        }
        $content .= "</table>";
        return $content;
    }
}
