<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;

/**
 * @property  Teams
 */
class UpdateCommand extends Command
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Teams');
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $i = 0;
        $url = "http://es01.usfirst.org/teams_v1/_search?q=team_type:FTC%%20AND%%20profile_year:2018%%20AND%%20team_number_yearly:%d";
        $teams = $this->Teams->find()->all();
        foreach($teams as $team) {
            $furl = sprintf($url, $team->id);
            //  Initiate curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$furl);
            $result=curl_exec($ch);
            curl_close($ch);
            $i++;

            $first_info = json_decode($result, true);
            if(!array_key_exists('hits', $first_info)) continue;
            $first_info = $first_info['hits'];
            if(!array_key_exists('hits', $first_info)) continue;
            $first_info = $first_info['hits'];
            if(!array_key_exists(0, $first_info)) continue;
            $first_info = $first_info[0];
            if(!array_key_exists('_source', $first_info)) continue;
            $first_info = $first_info['_source'];

            $team->name = $first_info['team_nickname'];
            $team->organization = $first_info['team_name_calc'];
            $team->city = $first_info['team_city'];
            $team->state = $first_info['team_stateprov'];
            $team->country = $first_info['team_country'];
            $this->Teams->save($team);
        }
    }
}
