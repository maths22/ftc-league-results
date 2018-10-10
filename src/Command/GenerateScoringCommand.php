<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use PDO;
use PDOException;
use SQLite3;
use ZipArchive;

/**
 * @property  \App\Model\Table\TeamsTable $Teams
 * @property  \App\Model\Table\EventsTable $Events
 * @property  \App\Model\Table\LeaguesTable $Leagues
 * @property  \App\Model\Table\DivisionsTable $Divisions
 */
class GenerateScoringCommand extends Command
{
    const TYPE_SCRIMMAGE = 0;
    const TYPE_LEAGUE_MEET = 1;
    const TYPE_QUALIFIER = 2;
    const TYPE_LEAGUE_TOURNAMENT = 3;
    const TYPE_CHAMPIONSHIP = 4;
    const TYPE_OTHER = 5;

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Teams');
        $this->loadModel('Events');
        $this->loadModel('Divisions');
        $this->loadModel('Leagues');
    }

    // https://stackoverflow.com/a/30010928
    function tempdir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000)
    {
        /* Use the system temp dir by default. */
        if (is_null($dir))
        {
            $dir = sys_get_temp_dir();
        }

        /* Trim trailing slashes from $dir. */
        $dir = rtrim($dir, '/');

        /* If we don't have permission to create a directory, fail, otherwise we will
         * be stuck in an endless loop.
         */
        if (!is_dir($dir) || !is_writable($dir))
        {
            return false;
        }

        /* Make sure characters in prefix are safe. */
        if (strpbrk($prefix, '\\/:*?"<>|') !== false)
        {
            return false;
        }

        /* Attempt to create a random directory until it works. Abort if we reach
         * $maxAttempts. Something screwy could be happening with the filesystem
         * and our loop could otherwise become endless.
         */
        $attempts = 0;
        do
        {
            //TODO make rand again
            $path = sprintf('%s/%s%s', $dir, $prefix, mt_rand(100000, mt_getrandmax()));
        } while (
            !mkdir($path, $mode) &&
            $attempts++ < $maxAttempts
        );

        return $path;
    }

    function resultSetToArray($queryResultSet, $assoc = true){
        $multiArray = array();
        $count = 0;
        while($row = $queryResultSet->fetchArray(SQLITE3_ASSOC)){
            foreach($row as $i=>$value) {
                $multiArray[$count][$i] = $value;
            }
            $count++;
        }
        return $multiArray;
    }


    public function execute(Arguments $args, ConsoleIo $io)
    {
        $tmpdir = $this->tempdir(null, 'scor_');
        exec('rm -rf '. $tmpdir);
        mkdir($tmpdir);
        chdir($tmpdir);

        try {
            $awards = [];
            $zip = new ZipArchive;
            if ($zip->open(ROOT . DS . 'data' . DS . 'scoring-system.zip') === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    if(strpos($zip->getNameIndex($i), 'global.db') !== false) {
                        file_put_contents('global.db', $zip->getFromIndex($i));
                    }
                }
            }
            if(file_exists('global.db')) {
                $global_db = new SQLite3('global.db');
                $global_db->enableExceptions(true);
                $stmt = $global_db->prepare("SELECT id, name, description, teamAward, editable, required, awardOrder FROM awardInfo");
                $awards = $this->resultSetToArray($stmt->execute());
            }

            $server_db = new SQLite3('server.db');
            $server_db->enableExceptions(true);
            $server_db_init = file_get_contents(ROOT . '/resources/sql/create_server_db.sql');
            $server_db->exec($server_db_init);
            $create_event_stmt = $server_db->prepare("INSERT INTO events 
                           (code, name, type, status, finals, divisions, start, end)
                           VALUES (:code, :name, :type, :status, :finals, :divisions, :start, :end)");

            $create_event_stmt->bindParam(':code', $slug);
            $create_event_stmt->bindParam(':name', $name);
            $create_event_stmt->bindParam(':type', $type_id);
            $status = 1;
            $create_event_stmt->bindParam(':status', $status);
            $create_event_stmt->bindParam(':finals', $has_finals);
            $divisions = 0;
            $create_event_stmt->bindParam(':divisions', $divisions);
            $create_event_stmt->bindParam(':start', $date);
            $create_event_stmt->bindParam(':end', $end_date);

            $events = $this->Events->find()->all();
            foreach ($events as $event) {
                $slug = $event->slug;
                $name = $event->name;
                $type_id = ($event->type == 'meet') ? self::TYPE_LEAGUE_MEET : self::TYPE_LEAGUE_TOURNAMENT;
                $has_finals = ($event->type == 'meet') ? 0 : 1;
                $date = strtotime($event->date) . '000';
                $end_date = ($event->end_date ? strtotime($event->end_date) : strtotime($event->date)) . '000';
                $create_event_stmt->execute();

                $event_db = new SQLite3($slug . '.db');
                $event_db->enableExceptions(true);
                $event_db_init = file_get_contents(ROOT . '/resources/sql/create_event_db.sql');
                $event_db->exec($event_db_init);
                $add_config_stmt = $event_db->prepare("INSERT INTO config (key, value) VALUES (:key, :value)");
                $add_config_stmt->bindParam(':key', $config_key);
                $add_config_stmt->bindParam(':value', $config_val);
                $config_key = "fieldCount";
                $config_val = ($event->type == 'meet') ? 1 : 2;
                $add_config_stmt->execute();


                $league_id = $event->league_id;
                if ($event->type == 'meet') {
                    $league_id = $this->Divisions->get($event->division_id)->league_id;
                }
                $league = $this->Leagues->get($league_id, [
                    'contain' => ['Divisions', 'Divisions.Teams', 'Divisions.Teams.Matches', 'Divisions.Teams.Matches.Events']
                ]);

                $add_league_stmt = $event_db->prepare("INSERT INTO leagueInfo
                            (code, name, country, state, city)
                            VALUES (:code, :name, :country, :state, :city)");
                $add_league_stmt->bindParam('code', $league_code);
                $add_league_stmt->bindParam('name', $league_name);
                $add_league_stmt->bindParam('country', $country);
                $add_league_stmt->bindParam('state', $state);
                $add_league_stmt->bindValue('city', '');

                $add_league_team_stmt = $event_db->prepare("INSERT INTO leagueMembers
                            (code, team)
                            VALUES (:code, :team)");
                $add_league_team_stmt->bindParam('code', $league_code);
                $add_league_team_stmt->bindParam('team', $team_number);

                $add_teams_stmt = $event_db->prepare("INSERT INTO teams
                            (number, advanced, division)
                            VALUES (:number, :advanced, :division)");
                $add_teams_stmt->bindParam('number', $team_number);
                $add_teams_stmt->bindValue('advanced', 0);
                $add_teams_stmt->bindValue('division', 0);


                $add_league_history_stmt = $event_db->prepare("INSERT INTO leagueHistory 
                           (team, eventCode, match, rp, tbp, score)
                           VALUES (:team, :eventCode, :match, :rp, :tbp, :score)");
                $add_league_history_stmt->bindParam('team', $team_number);
                $add_league_history_stmt->bindParam('eventCode', $event_code);
                $add_league_history_stmt->bindParam('match', $match_no);
                $add_league_history_stmt->bindParam('rp', $rp);
                $add_league_history_stmt->bindParam('tbp', $tbp);
                $add_league_history_stmt->bindParam('score', $score);

                $add_team_info_stmt = $event_db->prepare("INSERT INTO teamInfo
                           (number, name, school, city, state, country, rookie)
                           VALUES (:number, :name, :school, :city, :state, :country, :rookie)");
                $add_team_info_stmt->bindParam('number', $team_number, PDO::PARAM_INT);
                $add_team_info_stmt->bindParam('name', $team_name);
                $add_team_info_stmt->bindParam('school', $organization);
                $add_team_info_stmt->bindParam('city', $city);
                $add_team_info_stmt->bindParam('state', $state);
                $add_team_info_stmt->bindParam('country', $country);
                $add_team_info_stmt->bindParam('rookie', $rookie_year, PDO::PARAM_INT);



                foreach ($league->divisions as $division) {
                    $league_code = $division->slug;
                    $league_name = $division->name;
                    $country = "USA";
                    $state = "IL";
                    $add_league_stmt->execute();
                    foreach ($division->teams as $team) {

                        $team_number = $team->id;
                        $team_name = $team->name;
                        $organization = $team->organization;
                        $city = $team->city;
                        $state = $team->state;
                        $country = $team->country;
                        $rookie_year = $team->rookie_year;
                        $add_league_team_stmt->execute();

                        foreach ($team->matches as $match) {
                            $event_code = $match->event->slug;
                            $match_no = $match->num;
                            $rp = $match->rp;
                            $tbp = $match->tbp;
                            $score = $match->score;
                            $add_league_history_stmt->execute();
                        }
                        if ($event->type == 'championship' || $division->id == $event->division_id) {
                            $add_teams_stmt->execute();
                            $add_team_info_stmt->execute();

                        }
                    }
                }

                $add_award_info_stmt = $event_db->prepare("INSERT INTO awardInfo
                          (id, name, description, teamAward, editable, required, awardOrder)
                          VALUES (:id, :name, :description, :teamAward, :editable, :required, :awardOrder)");
                $add_award_info_stmt->bindParam("id", $award_id);
                $add_award_info_stmt->bindParam("name", $award_name);
                $add_award_info_stmt->bindParam("description", $award_descr);
                $add_award_info_stmt->bindParam("teamAward", $team_award);
                $add_award_info_stmt->bindParam("editable", $editable);
                $add_award_info_stmt->bindParam("required", $required);
                $add_award_info_stmt->bindParam("awardOrder", $award_order);

                foreach ($awards as $award) {
                    $award_id = $award['id'];
                    $award_name = $award['name'];
                    $award_descr = $award['description'];
                    $team_award = $award['teamAward'];
                    $editable = $award['editable'];
                    $required = $award['required'];
                    $award_order = $award['awardOrder'];
                    $add_award_info_stmt->execute();
                }

                // TODO: import complete events
            }
        } catch( PDOException $Exception ) {
            print_r($Exception->getMessage());
            throw $Exception;
        }

        exec("rm -rf " . ROOT . '/data/base_scoring_dbs');
        rename($tmpdir, ROOT . '/data/base_scoring_dbs');
    }
}
