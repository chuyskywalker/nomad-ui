<?php

namespace Nomad;

class Link {

    public function a($text) {
        return '<a href="allocation.php?id=' . $text . '" title="' . $text . '">' . explode('-', $text)[0] . '</a>';
    }

    public function n($text) {
        return '<a href="node.php?id=' . $text . '" title="' . $text . '">' . explode('-', $text)[0] . '</a>';
    }

    public function e($text) {
        return '<a href="evaluation.php?id=' . $text . '" title="' . $text . '">' . explode('-', $text)[0] . '</a>';
    }

}