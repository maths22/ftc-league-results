<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Leagues Controller
 *
 * @property \App\Model\Table\LeaguesTable $Leagues
 */
class LeaguesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Ranking');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $leagues = $this->paginate($this->Leagues);

        $this->set(compact('leagues'));
        $this->set('_serialize', ['leagues']);
    }

    /**
     * View method
     *
     * @param string|null $id League id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $league = $this->Leagues->get($id, [
            'contain' => ['Divisions']
        ]);

        $this->set('league', $league);
        $this->set('_serialize', ['league']);
    }

    public function all()
    {
        $leagues = $this->Leagues->find('all');

        $this->set('leagues', $leagues);
        $this->set('_serialize', ['leagues']);
    }


    public function details($slug)
    {
        $league = $this->Leagues->findBySlug($slug)
            ->contain(['Divisions','Events','Divisions.Teams','Divisions.Events'])
            ->first();

        $this->set(compact('league'));
        $this->set('_serialize', ['league']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $league = $this->Leagues->newEntity();
        if ($this->request->is('post')) {
            $league = $this->Leagues->patchEntity($league, $this->request->data);
            if ($this->Leagues->save($league)) {
                $this->Flash->success(__('The league has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The league could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('league'));
        $this->set('_serialize', ['league']);
    }

    /**
     * Edit method
     *
     * @param string|null $id League id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $league = $this->Leagues->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $league = $this->Leagues->patchEntity($league, $this->request->data);
            if ($this->Leagues->save($league)) {
                $this->Flash->success(__('The league has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The league could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('league'));
        $this->set('_serialize', ['league']);
    }

    /**
     * Delete method
     *
     * @param string|null $id League id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $league = $this->Leagues->get($id);
        if ($this->Leagues->delete($league)) {
            $this->Flash->success(__('The league has been deleted.'));
        } else {
            $this->Flash->error(__('The league could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function rankings($id = null) {
        $league = $this->Leagues->get($id, [
            'contain' => ['Divisions','Divisions.Teams', 'Divisions.Teams.Matches', 'Divisions.Teams.Divisions']
        ]);
        $teams = [];
        foreach($league->divisions as $division) {
            $teams = array_merge($teams, $division->teams);
        }

        $rankings = $this->Ranking->rankTeams($teams);
        $this->set('league', $league);
        $this->set('rankings', $rankings);
    }
}
