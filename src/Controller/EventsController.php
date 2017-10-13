<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Client;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use ZipArchive;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 */
class EventsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['all', 'generateScoringSystem']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Divisions', 'Leagues']
        ];
        $events = $this->paginate($this->Events);

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * View method
     *
     * @param string|null $id Event id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['Divisions', 'Leagues', 'Matches']
        ]);

        $this->set('event', $event);
        $this->set('_serialize', ['event']);
    }

    public function all($id = null)
    {
        $this->paginate = [
            'contain' => ['Divisions', 'Leagues', 'Divisions.Leagues', 'Matches']
        ];
        $events = $this->paginate($this->Events);

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $event = $this->Events->newEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->data);
            if ($this->Events->save($event)) {
                //TODO externalize
                $liveAccount = $this->createLiveResultsAccount(strtolower('il-rr-'.Text::slug($event->name)));
                if($liveAccount) {
                    $event = $this->Events->patchEntity($event, $liveAccount);
                    $this->Events->save($event);
                }
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The event could not be saved. Please, try again.'));
            }
        }
        $divisions = $this->Events->Divisions->find('list', ['limit' => 200, 'valueField' => function ($e) {
            return $e->league->name . ' - ' . $e->name;
        }])->contain(['Leagues']);
        $leagues = $this->Events->Leagues->find('list', ['limit' => 200, 'valueField' => function ($e) {
            return $e->name;
        }]);
        $this->set(compact('event', 'divisions', 'leagues'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->data);
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The event could not be saved. Please, try again.'));
            }
        }
        $divisions = $this->Events->Divisions->find('list', ['limit' => 200, 'valueField' => function ($e) {
            return $e->league->name . ' - ' . $e->name;
        }])->contain(['Leagues']);
        $leagues = $this->Events->Leagues->find('list', ['limit' => 200, 'valueField' => function ($e) {
            return $e->name;
        }]);
        $this->set(compact('event', 'divisions', 'leagues'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        if ($this->Events->delete($event)) {
            $this->Flash->success(__('The event has been deleted.'));
        } else {
            $this->Flash->error(__('The event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function createLiveResultsAccount($slug)
    {
        $http = new Client();

        $response = $http->post(Configure::read('LiveResults.registrationUrl'), [
            'security-key' => Configure::read('LiveResults.securityKey'),
            'event-key' => $slug,
            'json' => true,
        ]);
        $body = $response->body('json_decode');
        if(isset($body->error)) return false;
        $url = parse_url($body->url);
        return [
            'ftp_host' => $url['host'],
            'ftp_port' => $url['port'],
            'ftp_user' => $url['user'],
            'ftp_pass' => $url['pass'],
            'ftp_path' => $url['path'],
            'web_url' => $body->web,
        ];
    }

    public function generateScoringSystem($id = null)
    {
        $scoring_system = ROOT . DS . 'external' . DS . 'ftc-scoring.zip';
        $scoring_system_path =  tempnam(sys_get_temp_dir(), 'ftcscoring');
        file_put_contents($scoring_system_path, file_get_contents($scoring_system));
        $linebreak = "\n";
        $event = $this->Events->get($id, [
            'contain' => ['Divisions.Leagues.Divisions.Teams', 'Divisions.Leagues.Divisions.Teams.Divisions', 'Divisions.Leagues.Divisions.Teams.Matches', 'Leagues.Divisions.Teams', 'Leagues.Divisions.Teams.Matches']
        ]);

        $teams = [];
        if($event->type == 'championship') {
            foreach($event->league->divisions as $division) {
                $teams = array_merge($teams, $division->teams);
            }
            foreach($teams as $team) {
                $team->present = true;
            }
        } else {
            foreach($event->division->league->divisions as $division) {
                $teams = array_merge($teams, $division->teams);
            }
            foreach($teams as $team) {
                if($team->division->id == $event->division_id) {
                    $team->present = true;
                } else {
                    $team->present = false;
                }
            }
        }
        $teamsFile = '';
        foreach($teams as $team) {
            $line = 1 . "|" . $team->id . "|"
                . ($team->name ? $team->name : ' ') . "|"
                . ($team->organization ? $team->organization : ' ') . "|"
                . ($team->city ? $team->city : ' ') . "|"
                . ($team->state ? $team->state : ' ') . "|"
                . ($team->country ? $team->country : ' ') . "|"
                . 'false' . "|" . ($team->present ? 'true' : 'false');
            $res = [];
            foreach($team->matches as $match) {
                $res[] = $match->qp . '|' . $match->rp . '|' . $match->score;
            }
            $teamsFile .= $line . '|' . count($res) . '|' . implode('|', $res) . $linebreak;
        };
        $divisionsFile = '';
        $divisionsFile .= 'Event Name' . $linebreak;
        $divisionsFile .= $event->name . $linebreak;
        $divisionsFile .= 'Event Type' . $linebreak;
        $divisionsFile .= ($event->type == 'championship' ? 'LEAGUE_CHAMPIONSHIP' : 'LEAGUE') . $linebreak;
        $divisionsFile .= 'Using Multiple Divisions' . $linebreak;
        $divisionsFile .= 'false' . $linebreak;
        $divisionsFile .= 'Number|Name|Matches Per Team|Password' . $linebreak;
        $date = $event->date;
        $month = $date->month - 1;
        $divisionsFile .= "1|$event->name|0|$month|$date->day|$date->year|Illinois" . $linebreak;

        $zip = new ZipArchive;
        if ($zip->open($scoring_system_path) === TRUE) {
            $zip->addFromString("teams.txt", $teamsFile);
            $zip->addFromString("divisions.txt", $divisionsFile);
            $properties = $zip->getFromName("FTCScoring.properties");
            if($event->ftp_user && $event->ftp_pass) {
                $properties .= $linebreak
                    .= <<<EXTRAPROPS
FTPUploadBaseDirectory=$event->ftp_path
FTPUploadPort=$event->ftp_port
FTPUploadSite=$event->ftp_host
FTPUploadUser=$event->ftp_user
FTPUploadPassword=$event->ftp_pass
FTPUploadPassive=true
EXTRAPROPS;
            }

            $zip->addFromString("FTCScoring.properties", $properties);

            $zip->close();
        } else {
            //TODO throw something
        }


        // Return the response to prevent controller from trying to render
        // a view.
        $response = $this->response;


        $response = $response->withFile($scoring_system_path);

        $clean_event_name = Text::slug($event->name);
        // Optionally force file download
        $response = $response->withDownload("ftc-scoring-rr-$clean_event_name.zip");

        // Return response object to prevent controller from trying to render
        // a view.
        return $response;
    }

    public function upload($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['Leagues', 'Divisions']
        ]);
        if ($this->request->is(['post', 'patch', 'put'])) {
            $upload_path = $this->request->data['scoring_dump']['tmp_name'];

            $za = new ZipArchive();

            $za->open($upload_path);

            $matchFile = null;
            for ($i=0; $i<$za->numFiles;$i++) {
                $filename = basename(str_replace("\\","/",$za->statIndex($i)['name']));
                if($filename == 'matches.txt') {
                    $matchFile = $za->getFromIndex($i);
                }
            }

            $success = true;
            $jarfile = ROOT . DS . 'external' . DS . 'ftc-ranking-processor.jar';

            $cmd = "java -jar ${jarfile}";

            $descriptorSpec = array(
                0 => ["pipe", "r"],  // stdin is a pipe that the child will read from
                1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
                2 => ["pipe", "w"],
            );

            $process = proc_open($cmd, $descriptorSpec, $pipes);

            $matchContent = null;
            if (is_resource($process)) {
                // $pipes now looks like this:
                // 0 => writeable handle connected to child stdin
                // 1 => readable handle connected to child stdout

                fwrite($pipes[0], $matchFile);
                fclose($pipes[0]);

                $matchContent = stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                $matchErrContent = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                // It is important that you close any pipes before calling
                // proc_close in order to avoid a deadlock
                $return_value = proc_close($process);
            }
            $parsedMatchFile = array_map(function ($l) {
                return str_getcsv($l, '|');
            }, explode("\n", $matchContent));

            foreach ($parsedMatchFile as $matchArr) {
                $match = $this->Events->Matches->newEntity();
                if (sizeof($matchArr) >= 5) {
                    $match->num = $matchArr[0];
                    $match->team_id = $matchArr[1];
                    $match->qp = $matchArr[2];
                    $match->rp = $matchArr[3];
                    $match->score = $matchArr[4];
                    $match->event_id = $event->id;
                    $success = $success && $this->Events->Matches->save($match);
                }
            }
            if ($success) {
                $this->Flash->success(__('The event has been imported.'));
            } else {
                $this->Flash->error(__('The event could not be imported. Please, try again.'));
            }
        }

        $this->set(compact('event'));
        $this->set('_serialize', ['event']);
    }
}
