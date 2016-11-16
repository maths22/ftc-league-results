<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 11/8/2016
 * Time: 9:42 AM
 */

namespace App\Controller\Component;


use Cake\Controller\Component;

class RankingComponent extends Component
{
    public function rankTeams($teams) {
        $rankings = [];
        foreach($teams as $team) {
            $rankings[] = $this->computeRanking($team);
        }
        usort($rankings, function ($r2, $r1) {
            $diff = $r1['qp'] - $r2['qp'];
            if($diff != 0) {
                return $diff;
            }
            $diff = $r1['rp'] - $r2['rp'];
            if($diff != 0) {
                return $diff;
            }
            $minSize = min(sizeof($r1['scores']), sizeof($r2['scores']));
            for($i = 0; $i < $minSize; $i++) {
                $diff = $r1['scores'][$i] - $r2['scores'][$i];
                if($diff != 0) {
                    return $diff;
                }
            }
            return 0;
        });
        return $rankings;
    }

    public function computeRanking($team) {
        $matches = $team->matches;
        $matches = array_filter($matches, function($match) {
           return $match->qp >= 0;
        });

        $matches_played = sizeof($matches);

        usort($matches, function ($m2, $m1) {
            $diff = $m1->qp - $m2->qp;
            if($diff != 0) {
                return $diff;
            }
            $diff = $m1->rp - $m2->rp;
            if($diff != 0) {
                return $diff;
            }
            $diff = $m1->score - $m2->score;
            return $diff;
        });

        $matches = array_slice($matches, 0, 10);

        $ret = [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'division' => $team->division,
            'matches_played' => $matches_played,
            'qp' => 0,
            'rp' => 0,
            'scores' => []
        ];
        foreach($matches as $match) {
            $ret['qp'] += $match->qp;
            $ret['rp'] += $match->rp;
            $ret['scores'][] = $match->score;
        }
        rsort($ret['scores']);

        return $ret;
    }
}
