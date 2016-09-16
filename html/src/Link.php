<?php

namespace Nomad;

// TODO: ALL THE ESCAPING
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

    public function j($text) {
        return '<a href="job.php?id=' . $text . '" title="' . $text . '">' . $text . '</a>';
    }

    public function s($text) {
        return '<a href="server.php?id=' . $text . '" title="' . $text . '">' . $text . '</a>';
    }

}