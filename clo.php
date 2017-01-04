<?php
class T {
    private $data = [];
    public function __construct() {
        $this->data['b'] = function() {
            return new B();
        };

        $this->data['c'] = function() {
            return new C();
        };
    }

    public function __get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key]();
        }
    }
}

class B {
    public $name = "cguo";

}

class C {
    public $name = "angelina";

}

$t = new T();
echo $t->c->name, "\n";
echo $t->b->name, "\n";

