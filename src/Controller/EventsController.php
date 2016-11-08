<?php
namespace App\Controller;

use App\Controller\AppController;
use ZipArchive;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 */
class EventsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Divisions']
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
            'contain' => ['Divisions', 'Matches']
        ]);

        $this->set('event', $event);
        $this->set('_serialize', ['event']);
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
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The event could not be saved. Please, try again.'));
            }
        }
        $divisions = $this->Events->Divisions->find('list', ['limit' => 200, 'valueField' => function ($e) {
            return $e->league->name . ' - ' . $e->name;
        }])->contain(['Leagues']);
        $this->set(compact('event', 'divisions'));
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
        $this->set(compact('event', 'divisions'));
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

    public function upload()
    {
        $event = $this->Events->newEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->data);

            $upload_path = $this->request->data['scoring_dump']['tmp_name'];

            $za = new ZipArchive();

            $za->open($upload_path);

            $matchFile = null;
            $divisionsFile = null;
            for ($i=0; $i<$za->numFiles;$i++) {
                $filename = basename($za->statIndex($i)['name']);
                if($filename == 'matches.txt') {
                    $matchFile = $za->getFromIndex($i);
                }
                if($filename == 'divisions.txt') {
                    $divisionsFile = $za->getFromIndex($i);
                }
            }

            $event->name = explode("\n",$divisionsFile)[1];

            $successs = true;
            if ($successs = $this->Events->save($event)) {
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
//debug($match);
                        $successs = $successs && $this->Events->Matches->save($match);
                    }
                }
            }

            if ($successs) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The event could not be saved. Please, try again.'));
            }
        }
        $divisions = $this->Events->Divisions->find('list', ['limit' => 200, 'valueField' => function ($e) {
            return $e->league->name . ' - ' . $e->name;
        }])->contain(['Leagues']);
        $this->set(compact('event', 'divisions'));
        $this->set('_serialize', ['event']);
    }
}
