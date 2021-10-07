<?php

namespace model;

use classes\interfaces\model_interface;
use classes\table;
use module\tables\model\league_table;

class club implements model_interface
{
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $cid,
        public string $title,
    ) {
    }
    
    public float $score = 0;
    public float $total = 0;
    public int $number = 1;
    public string $content;
    public int $max_pilots;

    public function AddSub(league_table $data, scorable $pilot): void
    {
        if ($this->number < $this->max_pilots) {
            $this->score += $pilot->score;
            $this->number++;
            $this->content .= $pilot->output($data, $this->number);
        }
    }

    function set_from_pilot(league_table $data, scorable $pilot, int $num): void
    {
        $this->max_pilots = $num;
        $this->score = $pilot->score;
        $this->total = $this->score;
        $this->content = $pilot->output($data, 1);
    }

    public function writeClubSemiHead(int $pos): string
    {
        return "<h3><span class='pos'>$pos</span><span class='score'>$this->score</span><spsn class='name'>$this->title</span></h3>";
    }
}
