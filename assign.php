<?php
/**
 * 分摊
 *
 * $sku = array(
 *     array('id' => 'xxxx', 'price' => xxxx),
 *     array('id' => 'xxxx', 'price' => xxxx),
 *     array('id' => 'xxxx', 'price' => xxxx),
 *     array('id' => 'xxxx', 'price' => xxxx),
 * );
 * $reduction = xxxx;
 *
 * reduction sku[x].price 必须是整数
 *
 * return array(
 *     array('id' => 'xxxx', 'price' => xxxx, 'assign' => xxxx),
 *     array('id' => 'xxxx', 'price' => xxxx, 'assign' => xxxx),
 *     array('id' => 'xxxx', 'price' => xxxx, 'assign' => xxxx),
 *     array('id' => 'xxxx', 'price' => xxxx, 'assign' => xxxx),
 * );
 *
 * demo:
 *
 * $obj = new assign($sku, $reduction);
 * $ret = $obj->run();
 *
 */
class assign {
    // sku
    private $sku = array();
    // 需要分摊的总金额
    private $totalReduction = 0;
    // 需要分摊的剩余金额
    private $reduction = 0;
    // sku 金额总和
    private $totalPrice = 0;

    public function __construct($sku, $reduction) {
        $this->setSku($sku);
        $this->setReduction($reduction);
        $this->reduction = $reduction;
    }


    // 计算分摊
    public function run() {
        if ($this->check() === false) {
            return $this->sku;
        }

        // 是否再次按比例拆分
        $need = false;
        foreach ($this->sku as $key => $value) {
            $assign = (int) ($this->reduction * $value['rate']);
            $this->sku[$key]['assign'] += $assign;
            $this->reduction -= $assign;
            if ($assign != 0) {
                $need = true;
            }
        }

        if ($need) {
            return $this->run();
        }

        // 未拆分完 | 不可拆分
        if (!empty($this->reduction)) {
            // 是否满足剩余的额度给其中某一件商品
            foreach ($this->sku as $key => $value) {
                if ($value['price'] - $value['assign'] >= $this->reduction) {
                    $this->sku[$key]['assign'] += $this->reduction;
                    $this->reduction = 0;
                    break;
                }
            }
        }
        if (!empty($this->reduction)) {
            // 平均分摊给每一个sku
            while ($this->reduction > 0) {
                foreach ($this->sku as $key => $value) {
                    if ($value['price'] - $value['assign'] >= 1) {
                        $this->sku[$key]['assign'] += 1;
                        $this->reduction -= 1;
                    }
                    if (empty($this->reduction)) {
                        break;
                    }
                }
            }
        }

        return $this->sku;
    }

    /**
     * 是否终止分摊
     */
    private function check() {
        if (empty($this->totalPrice) || empty($this->totalReduction) || empty($this->reduction)) {
            return false;
        }

        return true;
    }

    private function setSku($sku) {
        $this->setTotalPrice($sku);
        foreach ($sku as $key => $info) {
            $sku[$key]['assign'] = 0;
            $sku[$key]['rate'] = $info['price'] / $this->totalPrice;
        }
        $this->sku = $sku;
    }

    private function setReduction($reduction) {
        $this->totalReduction= (int) $reduction;
        if ($this->totalReduction > $this->totalPrice) {
            $this->totalReduction = $this->totalPrice;
        }
    }

    private function setTotalPrice($sku) {
        if (!empty($this->totalPrice)) {
            return;
        }
        $totalPrice = 0;
        foreach ($sku as $info) {
            $totalPrice += $info['price'];
        }
        $this->totalPrice = $totalPrice;
    }

    private function show() {
        echo "\n----------------------\n";
        $assign = 0;
        foreach ($this->sku as $info) {
            echo sprintf("%-1s %10s\n", $info['price'], $info['assign']);
            $assign += $info['assign'];
        }

        echo "totalPrice:{$this->totalPrice} assign:{$assign} reduction:{$this->reduction} totalReduction:{$this->totalReduction}\n";
        echo "\n----------------------\n";
    }
}
