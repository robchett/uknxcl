<?php

namespace model;

use classes\table;
use html\node;


/**
 * Class club
 */
class club extends table {


    public string $name;
    public int $score = 0;
    public int $total = 0;
    public int $number = 1;
    public $content;
    public $max_pilots;


    /**
     * @param $pilot |pilot_official $pilot
     */
    public function AddSub($pilot) {
        if ($this->number < $this->max_pilots) {
            $this->score += $pilot->score;
            $this->number++;
            $this->content .= $pilot->output($this->number);
        }
    }

    /**
     * @param $pilot |pilot_official $pilot
     * @param $num
     */
    function set_from_pilot($pilot, $num) {
        $this->max_pilots = $num;
        $this->name = $pilot->club;
        $this->score = $pilot->score;
        $this->total = $this->score;
        $this->content = $pilot->output(1);
    }

    /**
     * @param $pos
     * @return string
     */
    public function writeClubSemiHead($pos): string {
        return "<h3><span class='pos'>$pos</span><span class='score'>$this->score</span><spsn class='name'>$this->name</span></h3>";
    }
}
