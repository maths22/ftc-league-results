<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event[]|\Cake\Collection\CollectionInterface $events
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Event'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="events index large-9 medium-8 columns content">
    <h3><?= __('Events') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col">League - Division</th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('date') ?></th>
                <th scope="col">Imported</th>
                <th scope="col" class="actions"><?= __('Live Results') ?></th>
                <th scope="col" class="actions"><?= __('Scoring System') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
            <tr>
                <td><?= h(ucfirst($event->type)) ?></td>
                <td>
                    <?php if($event->has('division')): ?>
                        <?=  $this->Html->link($event->division->league->name, ['controller' => 'Leagues', 'action' => 'rankings', $event->division->league->id]) ?>
                        - <br>
                        <?=  $this->Html->link($event->division->name, ['controller' => 'Divisions', 'action' => 'rankings', $event->division->id]) ?>
                    <?php else: ?>
                        <?= $event->has('league') ? $this->Html->link($event->league->name, ['controller' => 'Leagues', 'action' => 'rankings', $event->league->id]) : '' ?>
                    <?php endif; ?>
                </td>
                <td><?= h($event->name) ?></td>
                <td><?= h($event->date) ?></td>
                <td><?= $event->imported ? 'Yes' : 'No' ?></td>
                <td>
                    <?= $event->has('web_url') ? $this->Html->link('Results', $event->web_url, ['target' => '_blank']) : ' ' ?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(__('Download'), ['action' => 'generateScoringSystem', $event->id]) ?>
                    <?php if($authUser != null): ?>
                        <?= $this->Html->link(__('Upload'), ['action' => 'upload', $event->id]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
