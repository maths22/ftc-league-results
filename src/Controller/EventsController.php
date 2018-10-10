<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Client;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use SQLite3;
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

        $scoring_system = ROOT . DS . 'data' . DS . 'scoring-system.zip';
        $scoring_system_path = tempnam(sys_get_temp_dir(), 'ftcscoring');
        file_put_contents($scoring_system_path, file_get_contents($scoring_system));
        $linebreak = "\n";
        $event = $this->Events->get($id);

        $zip = new ZipArchive;
        if ($zip->open($scoring_system_path) === TRUE) {
            $zip_root =  explode('/', $zip->getNameIndex(0))[0];

            $temp = tempnam(sys_get_temp_dir(), 'ftcscoring');
            file_put_contents($temp, file_get_contents(ROOT . DS . 'data' . DS . 'base_scoring_dbs' . DS . 'server.db'));
            $db = new SQLite3($temp);
            $stmt = $db->prepare("DELETE FROM events WHERE code <> :code");
            $stmt->bindValue("code", $event->slug);
            $stmt->execute();
            $db->close();
            $zip->addFile(
                ROOT. DS . 'data' . DS . 'base_scoring_dbs' . DS . $event->slug . '.db',
                $zip_root . '/lib/db/' . $event->slug . '.db');
//            debug($zip_root . '/lib/db/' . $event->slug . '.db');
//            debug($zip->filename);
            $zip->addFile($temp, $zip_root . '/lib/db/server.db');

            $zip->close();
        } else {
            //TODO throw something
        }


        // Return the response to prevent controller from trying to render
        // a view.
        $response = $this->response;


        $response = $response->withFile($scoring_system_path);

        $clean_event_name = $event->slug;
        // Optionally force file download
        $response = $response->withDownload("ftc-scoring-il-$clean_event_name-1819.zip");

        // Return response object to prevent controller from trying to render
        // a view.
        return $response;
    }

    function resultSetToArray($queryResultSet){
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

    public function upload($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['Leagues', 'Divisions', 'Matches']
        ]);
        if ($this->request->is(['post', 'patch', 'put'])) {
            $dest_dir = WWW_ROOT . DS . 'event_results' . DS . $id . DS;
            $filename = basename($this->request->getData('scoring_dump')['name']);
            if(!file_exists($dest_dir)) {
                mkdir($dest_dir, 0777, true);
            }
            move_uploaded_file($this->request->getData('scoring_dump')['tmp_name'], $dest_dir . $filename);
            $upload_path = $dest_dir . $filename;

            $db = new SQLite3($upload_path);
            $db->enableExceptions(true);
            $stmt = $db->prepare("SELECT team, match, rp, tbp, score FROM leagueHistory WHERE eventCode = :code");
            $stmt->bindValue("code", $event->slug);
            $res = $this->resultSetToArray($stmt->execute());

            $success = true;
            foreach ($res as $mData) {
                $match = $this->Events->Matches->newEntity();
                $match->num = $mData['match'];
                $match->team_id = $mData['team'];
                $match->rp = $mData['rp'];
                $match->tbp = $mData['tbp'];
                $match->score = $mData['score'];
                $match->event_id = $event->id;
                $success = $success && $this->Events->Matches->save($match);
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
