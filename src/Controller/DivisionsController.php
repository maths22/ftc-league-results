<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Divisions Controller
 *
 * @property \App\Model\Table\DivisionsTable $Divisions
 */
class DivisionsController extends AppController
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
        $this->paginate = [
            'contain' => ['Leagues']
        ];
        $divisions = $this->paginate($this->Divisions);

        $this->set(compact('divisions'));
        $this->set('_serialize', ['divisions']);
    }

    public function summary()
    {
        $divisions = $this->Divisions->find('all', [
            'contain' => ['Leagues']
        ]);

        $this->set(compact('divisions'));
        $this->set('_serialize', ['divisions']);
    }

    /**
     * View method
     *
     * @param string|null $id Division id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $division = $this->Divisions->get($id, [
            'contain' => ['Leagues', 'Events', 'Teams']
        ]);

        $this->set('division', $division);
        $this->set('_serialize', ['division']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $division = $this->Divisions->newEntity();
        if ($this->request->is('post')) {
            $division = $this->Divisions->patchEntity($division, $this->request->data);
            if ($this->Divisions->save($division)) {
                $this->Flash->success(__('The division has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The division could not be saved. Please, try again.'));
            }
        }
        $leagues = $this->Divisions->Leagues->find('list', ['limit' => 200]);
        $this->set(compact('division', 'leagues'));
        $this->set('_serialize', ['division']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Division id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $division = $this->Divisions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $division = $this->Divisions->patchEntity($division, $this->request->data);
            if ($this->Divisions->save($division)) {
                $this->Flash->success(__('The division has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The division could not be saved. Please, try again.'));
            }
        }
        $leagues = $this->Divisions->Leagues->find('list', ['limit' => 200]);
        $this->set(compact('division', 'leagues'));
        $this->set('_serialize', ['division']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Division id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $division = $this->Divisions->get($id);
        if ($this->Divisions->delete($division)) {
            $this->Flash->success(__('The division has been deleted.'));
        } else {
            $this->Flash->error(__('The division could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function rankings($id = null) {
        $division = $this->Divisions->get($id, [
            'contain' => ['Teams', 'Teams.Matches', 'Leagues', 'Events']
        ]);
        $rankings = $this->Ranking->rankTeams($division->teams);
        $this->set('division', $division);
        $this->set('rankings', $rankings);
    }
}
